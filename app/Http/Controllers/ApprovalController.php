<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DokumenApproval;
use App\Models\PengajuanPembelian;
use App\Models\HtaDanGpa;
use App\Models\UsulanInvestasi;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; // ✅ Wajib ada untuk whereNotExists
use Yajra\DataTables\Facades\DataTables;

class ApprovalController extends Controller
{
    public function index(Request $request)
    {
        // ==========================================
        // 1. PROSES REQUEST AJAX DATATABLES
        // ==========================================
        if ($request->ajax()) {
            $userId = Auth::id();

            // Query Utama: Filter berlapis + LOGIKA BERJENJANG + STATUS PENGAJUAN
            $query = DokumenApproval::with([
                'getUser',
                'getJabatan',
                'getDepartemen',
                'getDokumenHTAGPA.getPengajuan',
                'getDokumenUsulanInvestasi.getPengajuan'
            ])
            ->where('UserId', $userId)
            ->where('Status', 'Pending')

            // ✅ LOGIKA BERJENJANG:
            // Jangan tampilkan jika ada langkah sebelumnya (Urutan lebih kecil)
            // untuk dokumen yang sama yang statusnya BUKAN 'Approved'
            ->whereNotExists(function ($subQuery) {
                $subQuery->select(DB::raw(1))
                         ->from('dokumen_approvals as prev')
                         ->whereColumn('prev.JenisFormId', 'dokumen_approvals.JenisFormId')
                         ->whereColumn('prev.DokumenId', 'dokumen_approvals.DokumenId')
                         ->whereColumn('prev.Urutan', '<', 'dokumen_approvals.Urutan')
                         ->where('prev.Status', '!=', 'Approved');
            })

            ->where(function ($q) {
                // 1. Cek untuk HTA/GPA: JenisFormId cocok, Dokumen ada, Pengajuan ada
                $q->where(function ($subQ) {
                    $subQ->whereIn('JenisFormId', [1, 2, 16])
                        ->whereHas('getDokumenHTAGPA', function ($docQ) {
                            $docQ->whereHas('getPengajuan');
                        });
                })
                // 2. ✅ PERUBAHAN DI SINI: Cek untuk Usulan Investasi + Status Pengajuan harus 'Selesai' ATAU 'Disetujui CEO'
                ->orWhere(function ($subQ) {
                    $subQ->whereIn('JenisFormId', [7, 11, 12, 13, 14, 15])
                        ->whereHas('getDokumenUsulanInvestasi', function ($docQ) {
                            $docQ->whereHas('getPengajuan', function ($pengajuanQ) {
                                // ✅ Gunakan whereIn untuk mengecek multiple status
                                $pengajuanQ->whereIn('Status', ['Selesai', 'Disetujui CEO']);
                            });
                        });
                });
            })
            ->orderBy('Urutan', 'asc') // Urutkan berdasarkan urutan approval (berjenjang)
            ->orderByDesc('created_at');

            return DataTables::of($query)
                ->addIndexColumn()

                ->addColumn('jenis_dokumen', function ($row) {
                    if (in_array($row->JenisFormId, [1, 2, 16])) {
                        return '<span class="badge badge-doc hta">HTA / GPA</span>';
                    } elseif (in_array($row->JenisFormId, [7, 11, 12, 13, 14, 15])) {
                        return '<span class="badge badge-doc fui">Usulan Investasi</span>';
                    }
                    return '<span class="badge badge-secondary">-</span>';
                })

                ->addColumn('kode_pengajuan', function ($row) {
                    $pengajuan = $this->getPengajuanFromApproval($row);
                    if ($pengajuan) {
                        $id = encrypt($pengajuan->id);
                        $kode = $pengajuan->KodePengajuan ?? '-';
                        return '<a href="' . route('ajukan.show', $id) . '"
                               class="kode-link"
                               target="_blank"
                               onclick="event.stopPropagation();">' . e($kode) . '</a>';
                    }
                    return '<span class="text-muted">-</span>';
                })

                ->addColumn('nama_barang', function ($row) {
                    $pengajuan = $this->getPengajuanFromApproval($row);
                    if ($pengajuan && $pengajuan->getPengajuanItem && $pengajuan->getPengajuanItem->first()) {
                        return '<span class="text-dark">' .
                            e($pengajuan->getPengajuanItem->first()->getBarang->Nama ?? '-') .
                            '</span>';
                    }
                    return '<span class="text-muted">-</span>';
                })

                ->addColumn('urutan', function ($row) {
                    return '<span class="badge bg-secondary bg-opacity-75 rounded-pill px-3">#' . e($row->Urutan) . '</span>';
                })

                ->addColumn('tanggal', function ($row) {
                    return '<span class="text-muted small">' .
                        \Carbon\Carbon::parse($row->created_at)->translatedFormat('d M Y') .
                        '</span>';
                })

                ->addColumn('aksi', function ($row) {
                    $docUrl = $this->getDocumentShowUrl($row);
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

                ->addColumn('row_class', function ($row) {
                    if (in_array($row->JenisFormId, [1, 2, 16])) {
                        return 'border-success';
                    } elseif (in_array($row->JenisFormId, [7, 11, 12, 13, 14, 15])) {
                        return 'border-warning';
                    }
                    return '';
                })

                ->addColumn('doc_url', function ($row) {
                    return $this->getDocumentShowUrl($row);
                })

                ->rawColumns(['jenis_dokumen', 'kode_pengajuan', 'nama_barang', 'urutan', 'tanggal', 'aksi'])
                ->make(true);
        }

        // ==========================================
        // 2. PROSES REQUEST HALAMAN PERTAMA (NON-AJAX)
        // ==========================================
        $userId = Auth::id();

        // ✅ BASE QUERY: Gunakan query yang sama persis (termasuk logika berjenjang) untuk menghitung statistik
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

        $stats = [
            'total' => (clone $baseQuery)->count(),

            'hta' => (clone $baseQuery)
                ->whereIn('JenisFormId', [1, 2, 16])
                ->whereHas('getDokumenHTAGPA', function ($q) {
                    $q->whereHas('getPengajuan');
                })
                ->count(),

            // ✅ PERUBAHAN DI SINI JUGA: Update stats FUI agar sesuai dengan kondisi query utama
            'fui' => (clone $baseQuery)
                ->whereIn('JenisFormId', [7, 11, 12, 13, 14, 15])
                ->whereHas('getDokumenUsulanInvestasi', function ($q) {
                    $q->whereHas('getPengajuan', function ($pengajuanQ) {
                        $pengajuanQ->whereIn('Status', ['Selesai', 'Disetujui CEO']);
                    });
                })
                ->count(),
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
        }

        if (!$doc || !$doc->getPengajuan) {
            return null;
        }
        return $doc->getPengajuan;
    }

    private function getDocumentShowUrl($approval)
    {
        $doc = null;
        if (in_array($approval->JenisFormId, [1, 2, 16])) {
            $doc = $approval->getDokumenHTAGPA;
        } elseif (in_array($approval->JenisFormId, [7, 11, 12, 13, 14, 15])) {
            $doc = $approval->getDokumenUsulanInvestasi;
        }

        if (!$doc || !$doc->PengajuanItemId || !$doc->getPengajuan) {
            return '#';
        }

        if (in_array($approval->JenisFormId, [1, 2, 16])) {
            return route('htagpa.show', [$doc->IdPengajuan, $doc->PengajuanItemId]);
        }
        if (in_array($approval->JenisFormId, [7, 11, 12, 13, 14, 15])) {
            return route('usulan-investasi.show', [$doc->IdPengajuan, $doc->PengajuanItemId]);
        }
        return '#';
    }
}
