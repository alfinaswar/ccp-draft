<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DokumenApproval;
use App\Models\HtaDanGpa;
use App\Models\MasterForm;
use App\Models\PengajuanPembelian;
use App\Models\UsulanInvestasi;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; // ✅ Wajib ada untuk whereNotExists
use Yajra\DataTables\Facades\DataTables;

class ApprovalController extends Controller
{
    public function index(Request $request)
    {
        $komplainFormId = MasterForm::where('Nama', 'Komplain Vendor')->value('id');

        if ($request->ajax()) {
            $userId = Auth::id();

            // ==========================================
            // A. QUERY APPROVAL DOKUMEN (HTA + FUI + FS + KOMPLAIN VENDOR)
            // ==========================================
            $approvalDokumenQuery = DokumenApproval::with([
                'getUser',
                'getJabatan',
                'getDepartemen',
                'getDokumenHTAGPA.getPengajuan',
                'getDokumenUsulanInvestasi.getPengajuan',
                'getDokumenFS.getPengajuan', // ✅ Tambahkan relasi FS
                'getDokumenKomplainVendor',
            ])
                ->where('UserId', $userId)
                ->where('Status', 'Pending')
                ->whereNotExists(function ($subQuery) {
                    $subQuery->select(DB::raw(1))
                        ->from('dokumen_approvals as prev')
                        ->whereColumn('prev.JenisFormId', 'dokumen_approvals.JenisFormId')
                        ->whereColumn('prev.DokumenId', 'dokumen_approvals.DokumenId')
                        ->whereColumn('prev.Urutan', '<', 'dokumen_approvals.Urutan')
                        ->where('prev.Status', '!=', 'Approved');
                })
                ->where(function ($q) use ($komplainFormId) {
                    // 1. HTA/GPA
                    $q->where(function ($subQ) {
                        $subQ->whereIn('JenisFormId', [1, 2, 16])
                            ->whereHas('getDokumenHTAGPA', function ($docQ) {
                                $docQ->whereHas('getPengajuan');
                            });
                    })
                        // 2. FUI (Usulan Investasi)
                        ->orWhere(function ($subQ) {
                        $subQ->whereIn('JenisFormId', [7, 11, 12, 13, 14, 15])
                            ->whereHas('getDokumenUsulanInvestasi', function ($docQ) {
                                $docQ->whereHas('getPengajuan', function ($pengajuanQ) {
                                    $pengajuanQ->whereIn('Status', ['Selesai']);
                                });
                            });
                    })
                        // 3. ✅ FS (Feasibility Study) - TAMBAHKAN INI
                        ->orWhere(function ($subQ) {
                        $subQ->whereIn('JenisFormId', [6]) // ⚠️ SESUAIKAN dengan JenisFormId FS di database Anda
                            ->whereHas('getDokumenFS', function ($docQ) {
                                $docQ->whereHas('getPengajuan', function ($pengajuanQ) {
                                    $pengajuanQ->whereIn('Status', ['Selesai', 'Selesai Review']);
                                });
                            });
                    })
                        // 4. Komplain Vendor
                        ->when($komplainFormId, function ($subQuery) use ($komplainFormId) {
                            $subQuery->orWhere(function ($subQ) use ($komplainFormId) {
                                $subQ->where('JenisFormId', $komplainFormId)
                                    ->whereHas('getDokumenKomplainVendor');
                            });
                        });
                })
                ->get();

            // ==========================================
            // B. QUERY APPROVAL DIREKTUR
            // ==========================================
            $approvalDirekturQuery = PengajuanPembelian::with(['getPerusahaan', 'getPengajuanItem.getBarang'])
                ->where('DirekturId', $userId)
                ->whereNull('AccDirektur')
                ->whereNotNull('DirekturId')
                ->whereNull('AccDirekturPada')
                ->get();

            // ==========================================
            // C. GABUNGKAN DATA
            // ==========================================
            $mergedData = collect();

            foreach ($approvalDokumenQuery as $row) {
                $mergedData->push([
                    'type' => 'dokumen',
                    'data' => $row,
                    'sort_order' => $row->Urutan ?? 999,
                ]);
            }

            foreach ($approvalDirekturQuery as $row) {
                $mergedData->push([
                    'type' => 'direktur',
                    'data' => $row,
                    'sort_order' => 0,
                ]);
            }

            $mergedData = $mergedData->sortBy('sort_order')->values();

            // ==========================================
            // D. FORMAT UNTUK DATATABLES
            // ==========================================
            return DataTables::of($mergedData)
                ->addIndexColumn()
                ->addColumn('jenis_dokumen', function ($row) use ($komplainFormId) {
                    if ($row['type'] === 'direktur') {
                        return '<span class="badge badge-doc direktur">Approval Direktur</span>';
                    }

                    $data = $row['data'];
                    if (in_array($data->JenisFormId, [1, 2, 16])) {
                        return '<span class="badge badge-doc hta">HTA / GPA</span>';
                    } elseif (in_array($data->JenisFormId, [7, 11, 12, 13, 14, 15])) {
                        return '<span class="badge badge-doc fui">Usulan Investasi</span>';
                    } elseif (in_array($data->JenisFormId, [6])) { // ⚠️ SESUAIKAN
                        return '<span class="badge badge-doc fs">Feasibility Study</span>';
                    } elseif ($komplainFormId && (int) $data->JenisFormId === (int) $komplainFormId) {
                        return '<span class="badge badge-doc bg-danger text-white">Komplain Vendor</span>';
                    }
                    return '<span class="badge badge-secondary">-</span>';
                })
                ->addColumn('kode_pengajuan', function ($row) use ($komplainFormId) {
                    if ($row['type'] === 'direktur') {
                        $pengajuan = $row['data'];
                        $id = encrypt($pengajuan->id);
                        $kode = $pengajuan->KodePengajuan ?? '-';
                        return '<a href="' . route('ajukan.show', $id) . '"
                       class="kode-link" target="_blank"
                       onclick="event.stopPropagation();">' . e($kode) . '</a>';
                    }

                    $data = $row['data'];
                    if ($komplainFormId && (int) $data->JenisFormId === (int) $komplainFormId && $data->getDokumenKomplainVendor) {
                        $komplain = $data->getDokumenKomplainVendor;
                        return '<a href="' . route('komplain.show', encrypt($komplain->id)) . '"
                       class="kode-link" target="_blank"
                       onclick="event.stopPropagation();">' . e($komplain->KodeKomplain) . '</a>';
                    }

                    $pengajuan = $this->getPengajuanFromApproval($data);
                    if ($pengajuan) {
                        $id = encrypt($pengajuan->id);
                        $kode = $pengajuan->KodePengajuan ?? '-';
                        return '<a href="' . route('ajukan.show', $id) . '"
                       class="kode-link" target="_blank"
                       onclick="event.stopPropagation();">' . e($kode) . '</a>';
                    }
                    return '<span class="text-muted">-</span>';
                })
                ->addColumn('nama_barang', function ($row) use ($komplainFormId) {
                    if ($row['type'] === 'direktur') {
                        $pengajuan = $row['data'];
                        if ($pengajuan && $pengajuan->getPengajuanItem && $pengajuan->getPengajuanItem->first()) {
                            return '<span class="text-dark">' .
                                e($pengajuan->getPengajuanItem->first()->getBarang->Nama ?? '-') . '</span>';
                        }
                        return '<span class="text-muted">-</span>';
                    }

                    $data = $row['data'];
                    if ($komplainFormId && (int) $data->JenisFormId === (int) $komplainFormId && $data->getDokumenKomplainVendor) {
                        return '<span class="text-dark">' . e($data->getDokumenKomplainVendor->NamaBarang ?? '-') . '</span>';
                    }

                    $pengajuan = $this->getPengajuanFromApproval($data);
                    if ($pengajuan && $pengajuan->getPengajuanItem && $pengajuan->getPengajuanItem->first()) {
                        return '<span class="text-dark">' .
                            e($pengajuan->getPengajuanItem->first()->getBarang->Nama ?? '-') . '</span>';
                    }
                    return '<span class="text-muted">-</span>';
                })
                ->addColumn('urutan', function ($row) {
                    if ($row['type'] === 'direktur') {
                        return '<span class="badge bg-danger bg-opacity-75 rounded-pill px-3">#Direktur</span>';
                    }
                    $data = $row['data'];
                    return '<span class="badge bg-secondary bg-opacity-75 rounded-pill px-3">#' . e($data->Urutan) . '</span>';
                })
                ->addColumn('tanggal', function ($row) use ($komplainFormId) {
                    if ($row['type'] === 'direktur') {
                        $pengajuan = $row['data'];
                        $tanggalPresentasi = $pengajuan->TanggalPresentasi ?? null;
                        return '<span class="text-muted small">' .
                            ($tanggalPresentasi ? \Carbon\Carbon::parse($tanggalPresentasi)->translatedFormat('d M Y') : '-') . '</span>';
                    }

                    $data = $row['data'];
                    if ($komplainFormId && (int) $data->JenisFormId === (int) $komplainFormId && $data->getDokumenKomplainVendor) {
                        $tgl = $data->getDokumenKomplainVendor->Tanggal ?? null;
                        return '<span class="text-muted small">' .
                            ($tgl ? \Carbon\Carbon::parse($tgl)->translatedFormat('d M Y') : '-') . '</span>';
                    }

                    $pengajuan = $this->getPengajuanFromApproval($data);
                    $tanggalPresentasi = $pengajuan->TanggalPresentasi ?? null;
                    return '<span class="text-muted small">' .
                        ($tanggalPresentasi ? \Carbon\Carbon::parse($tanggalPresentasi)->translatedFormat('d M Y') : '-') . '</span>';
                })
                ->addColumn('asal_perm_rs', function ($row) use ($komplainFormId) {
                    if ($row['type'] === 'direktur') {
                        $pengajuan = $row['data'];
                        if ($pengajuan && isset($pengajuan->getPerusahaan->Nama)) {
                            return '<span class="text-dark">' . e($pengajuan->getPerusahaan->Nama) . '</span>';
                        }
                        return '<span class="text-muted">-</span>';
                    }

                    $data = $row['data'];
                    if ($komplainFormId && (int) $data->JenisFormId === (int) $komplainFormId && $data->getDokumenKomplainVendor) {
                        return '<span class="text-dark">' . e($data->getDokumenKomplainVendor->NamaRS ?? '-') . '</span>';
                    }

                    $pengajuan = $this->getPengajuanFromApproval($data);
                    if ($pengajuan && isset($pengajuan->getPerusahaan->Nama)) {
                        return '<span class="text-dark">' . e($pengajuan->getPerusahaan->Nama) . '</span>';
                    }
                    return '<span class="text-muted">-</span>';
                })
                ->addColumn('aksi', function ($row) use ($komplainFormId) {
                    if ($row['type'] === 'direktur') {
                        $pengajuan = $row['data'];
                        $approvalUrl = route('usulan-investasi.approve-direktur', [
                            'kodePengajuan' => $pengajuan->KodePengajuan ?? $pengajuan->id,
                            'direkturId' => $pengajuan->DirekturId
                        ]);
                        return '<a href="' . $approvalUrl . '"
                       class="btn btn-danger btn-review"
                       title="Approve sebagai Direktur"
                       target="_blank"
                       onclick="event.stopPropagation();">
                       <i class="fa fa-check-circle me-1"></i> Approve
                   </a>';
                    }

                    $data = $row['data'];
                    $docUrl = $this->getDocumentShowUrl($data, $komplainFormId);
                    if ($docUrl === '#') {
                        return '<button class="btn btn-secondary btn-sm" disabled>
                       <i class="fa fa-ban me-1"></i> Tidak Tersedia
                   </button>';
                    }
                    return '<a href="' . $docUrl . '"
                   class="btn btn-primary btn-review"
                   title="Review & Approve"
                   target="_blank"
                   onclick="event.stopPropagation();">
                   <i class="fa fa-eye me-1"></i> Lihat
               </a>';
                })
                ->addColumn('row_class', function ($row) use ($komplainFormId) {
                    if ($row['type'] === 'direktur') {
                        return 'border-danger';
                    }
                    $data = $row['data'];
                    if (in_array($data->JenisFormId, [1, 2, 16])) {
                        return 'border-success';
                    } elseif (in_array($data->JenisFormId, [7, 11, 12, 13, 14, 15])) {
                        return 'border-warning';
                    } elseif (in_array($data->JenisFormId, [6])) { // ⚠️ SESUAIKAN
                        return 'border-info';
                    } elseif ($komplainFormId && (int) $data->JenisFormId === (int) $komplainFormId) {
                        return 'border-danger';
                    }
                    return '';
                })
                ->addColumn('doc_url', function ($row) use ($komplainFormId) {
                    if ($row['type'] === 'direktur') {
                        $pengajuan = $row['data'];
                        return route('usulan-investasi.approve-direktur', [
                            'kodePengajuan' => $pengajuan->KodePengajuan ?? $pengajuan->id,
                            'direkturId' => $pengajuan->DirekturId
                        ]);
                    }
                    $data = $row['data'];
                    return $this->getDocumentShowUrl($data, $komplainFormId);
                })
                ->rawColumns(['asal_perm_rs', 'jenis_dokumen', 'kode_pengajuan', 'nama_barang', 'urutan', 'tanggal', 'aksi'])
                ->make(true);
        }

        // ==========================================
        // 2. NON-AJAX (STATS)
        // ==========================================
        $userId = Auth::id();

        $baseQuery = DokumenApproval::where('UserId', $userId)
            ->where('Status', 'Pending')
            ->whereNotExists(function ($subQuery) {
                $subQuery->select(DB::raw(1))
                    ->from('dokumen_approvals as prev')
                    ->whereColumn('prev.JenisFormId', 'dokumen_approvals.JenisFormId')
                    ->whereColumn('prev.DokumenId', 'dokumen_approvals.DokumenId')
                    ->whereColumn('prev.Urutan', '<', 'dokumen_approvals.Urutan')
                    ->where('prev.Status', '!=', 'Approved');
            });

        $approvalDirekturCount = PengajuanPembelian::where('DirekturId', $userId)
            ->whereNull('AccDirektur')
            ->whereNotNull('DirekturId')
            ->whereNull('AccDirekturPada')
            ->count();

        $stats = [
            'total' => (clone $baseQuery)->count() + $approvalDirekturCount,

            'hta' => (clone $baseQuery)
                ->whereIn('JenisFormId', [1, 2, 16])
                ->whereHas('getDokumenHTAGPA', function ($q) {
                    $q->whereHas('getPengajuan');
                })
                ->count(),

            'fui' => (clone $baseQuery)
                ->whereIn('JenisFormId', [7, 11, 12, 13, 14, 15])
                ->whereHas('getDokumenUsulanInvestasi', function ($q) {
                    $q->whereHas('getPengajuan', function ($pengajuanQ) {
                        $pengajuanQ->whereIn('Status', ['Selesai']);
                    });
                })
                ->count(),

            'fs' => (clone $baseQuery)
                ->whereIn('JenisFormId', [6]) //
                ->whereHas('getDokumenFS', function ($q) {
                    $q->whereHas('getPengajuan', function ($pengajuanQ) {
                        $pengajuanQ->whereIn('Status', ['Selesai Review',]);
                    });
                })
                ->count(),

            'komplain' => $komplainFormId
                ? (clone $baseQuery)
                    ->where('JenisFormId', $komplainFormId)
                    ->whereHas('getDokumenKomplainVendor')
                    ->count()
                : 0,

            'direktur' => $approvalDirekturCount,
        ];

        return view('approval.index', compact('stats'));
    }

    // ==========================================
    // HELPER METHODS
    // ==========================================
    private function getPengajuanFromApproval($approval)
    {
        $doc = null;
        if (in_array($approval->JenisFormId, [1, 2, 16])) {
            $doc = $approval->getDokumenHTAGPA;
        } elseif (in_array($approval->JenisFormId, [7, 11, 12, 13, 14, 15])) {
            $doc = $approval->getDokumenUsulanInvestasi;
        } elseif (in_array($approval->JenisFormId, [6])) { // ⚠️ SESUAIKAN
            $doc = $approval->getDokumenFS;
        }

        if (!$doc || !$doc->getPengajuan) {
            return null;
        }
        return $doc->getPengajuan;
    }


    private function getDocumentShowUrl($approval, $komplainFormId = null)
    {
        if ($komplainFormId && (int) $approval->JenisFormId === (int) $komplainFormId && $approval->getDokumenKomplainVendor) {
            return route('komplain.show', encrypt($approval->DokumenId));
        }

        $doc = null;
        if (in_array($approval->JenisFormId, [1, 2, 16])) {
            $doc = $approval->getDokumenHTAGPA;
        } elseif (in_array($approval->JenisFormId, [7, 11, 12, 13, 14, 15])) {
            $doc = $approval->getDokumenUsulanInvestasi;
        } elseif (in_array($approval->JenisFormId, [6])) { // ⚠️ SESUAIKAN
            $doc = $approval->getDokumenFS;
        }

        if (!$doc || !$doc->getPengajuan) {
            return '#';
        }

        if (in_array($approval->JenisFormId, [1, 2, 16])) {
            return route('htagpa.show', [$doc->IdPengajuan, $doc->PengajuanItemId]);
        }
        if (in_array($approval->JenisFormId, [7, 11, 12, 13, 14, 15])) {
            return route('usulan-investasi.show', [$doc->IdPengajuan, $doc->PengajuanItemId]);
        }
        if (in_array($approval->JenisFormId, [6])) { // ⚠️ SESUAIKAN
            return route('fs.show', [$doc->IdPengajuan, $doc->PengajuanItemId]);
        }
        return '#';
    }
}
