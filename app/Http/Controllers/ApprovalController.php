<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DokumenApproval;
use App\Models\PengajuanPembelian;
use App\Models\HtaDanGpa;
use App\Models\UsulanInvestasi;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables; // Pastikan package yajra/laravel-datatables terinstall

class ApprovalController extends Controller
{
    public function index(Request $request)
    {
        // ==========================================
        // 1. PROSES REQUEST AJAX DATATABLES
        // ==========================================
        if ($request->ajax()) {
            $userId = Auth::id();

            // Query Utama: Filter berlapis (Approval -> Dokumen -> Pengajuan)
            $query = DokumenApproval::with([
                'getUser',
                'getJabatan',
                'getDepartemen',
                'getDokumenHTAGPA.getPengajuan',       // Eager load bertingkat
                'getDokumenUsulanInvestasi.getPengajuan'
            ])
                ->where('UserId', $userId)
                ->where('Status', 'Pending')
                ->where(function ($q) {
                    // Cek untuk HTA/GPA: JenisFormId cocok, Dokumen ada, Pengajuan ada
                    $q->where(function ($subQ) {
                        $subQ->whereIn('JenisFormId', [1, 2, 16])
                            ->whereHas('getDokumenHTAGPA', function ($docQ) {
                                $docQ->whereHas('getPengajuan');
                            });
                    })
                        // Cek untuk Usulan Investasi: JenisFormId cocok, Dokumen ada, Pengajuan ada
                        ->orWhere(function ($subQ) {
                        $subQ->whereIn('JenisFormId', [7, 11, 12, 13, 14, 15])
                            ->whereHas('getDokumenUsulanInvestasi', function ($docQ) {
                                $docQ->whereHas('getPengajuan');
                            });
                    });
                })
                ->orderByDesc('created_at');


            return DataTables::of($query)
                ->addIndexColumn()

                // Kolom: Jenis Dokumen
                ->addColumn('jenis_dokumen', function ($row) {
                    if (in_array($row->JenisFormId, [1, 2, 16])) {
                        return '<span class="badge badge-doc hta">HTA / GPA</span>';
                    } elseif (in_array($row->JenisFormId, [7, 11, 12, 13, 14, 15])) {
                        return '<span class="badge badge-doc fui">Usulan Investasi</span>';
                    }
                    return '<span class="badge badge-secondary">-</span>';
                })

                // Kolom: Kode Pengajuan
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

                // Kolom: Nama Barang
                ->addColumn('nama_barang', function ($row) {
                    $pengajuan = $this->getPengajuanFromApproval($row);
                    if ($pengajuan && $pengajuan->getPengajuanItem && $pengajuan->getPengajuanItem->first()) {
                        return '<span class="text-dark">' .
                            e($pengajuan->getPengajuanItem->first()->getBarang->Nama ?? '-') .
                            '</span>';
                    }
                    return '<span class="text-muted">-</span>';
                })

                // Kolom: Urutan
                ->addColumn('urutan', function ($row) {
                    return '<span class="badge bg-secondary bg-opacity-75 rounded-pill px-3">#' . e($row->Urutan) . '</span>';
                })

                // Kolom: Tanggal
                ->addColumn('tanggal', function ($row) {
                    return '<span class="text-muted small">' .
                        \Carbon\Carbon::parse($row->created_at)->translatedFormat('d M Y') .
                        '</span>';
                })

                // Kolom: Aksi
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


                // Hidden Column: Row Class (untuk border warna)
                ->addColumn('row_class', function ($row) {
                    if (in_array($row->JenisFormId, [1, 2, 16])) {
                        return 'border-success';
                    } elseif (in_array($row->JenisFormId, [7, 11, 12, 13, 14, 15])) {
                        return 'border-warning';
                    }
                    return '';
                })

                // Hidden Column: Doc URL (untuk klik row)
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

        // Hitung Statistik dengan filter yang SAMA PERSIS dengan query DataTables
        $stats = [
            'total' => DokumenApproval::where('UserId', $userId)
                ->where('Status', 'Pending')
                ->where(function ($q) {
                    $q->where(function ($subQ) {
                        $subQ->whereIn('JenisFormId', [1, 2, 16])
                            ->whereHas('getDokumenHTAGPA', function ($docQ) {
                                $docQ->whereHas('getPengajuan');
                            });
                    })
                        ->orWhere(function ($subQ) {
                            $subQ->whereIn('JenisFormId', [7, 11, 12, 13, 14, 15])
                                ->whereHas('getDokumenUsulanInvestasi', function ($docQ) {
                                    $docQ->whereHas('getPengajuan');
                                });
                        });
                })->count(),

            'hta' => DokumenApproval::where('UserId', $userId)
                ->where('Status', 'Pending')
                ->whereIn('JenisFormId', [1, 2, 16])
                ->whereHas('getDokumenHTAGPA', function ($q) {
                    $q->whereHas('getPengajuan');
                })
                ->count(),

            'fui' => DokumenApproval::where('UserId', $userId)
                ->where('Status', 'Pending')
                ->whereIn('JenisFormId', [7, 11, 12, 13, 14, 15])
                ->whereHas('getDokumenUsulanInvestasi', function ($q) {
                    $q->whereHas('getPengajuan');
                })
                ->count(),
        ];

        return view('approval.index', compact('stats'));
    }

    // Helper: Ambil data pengajuan dari approval
    private function getPengajuanFromApproval($approval)
    {
        $doc = null;

        if (in_array($approval->JenisFormId, [1, 2, 16])) {
            $doc = $approval->getDokumenHTAGPA;
        } elseif (in_array($approval->JenisFormId, [7, 11, 12, 13, 14, 15])) {
            $doc = $approval->getDokumenUsulanInvestasi;
        }

        // Validasi: Dokumen harus ada, dan Pengajuan di dalamnya juga harus ada
        if (!$doc || !$doc->getPengajuan) {
            return null;
        }

        return $doc->getPengajuan;
    }

    /**
     * Helper: Menggenerate URL Show untuk Dokumen
     */
    private function getDocumentShowUrl($approval)
    {
        $doc = null;

        if (in_array($approval->JenisFormId, [1, 2, 16])) {
            $doc = $approval->getDokumenHTAGPA;
        } elseif (in_array($approval->JenisFormId, [7, 11, 12, 13, 14, 15])) {
            $doc = $approval->getDokumenUsulanInvestasi;
        }

        // Validasi berlapis: Dokumen ada, Punya PengajuanItemId, dan Pengajuan ada
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

    // Method untuk memproses approval (opsional)
    public function process(Request $request, $token)
    {
        $approval = DokumenApproval::where('ApprovalToken', $token)->firstOrFail();

        if ($approval->UserId !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses untuk approval ini.');
        }

        if ($approval->Status !== 'Pending') {
            return redirect()->back()->with('error', 'Approval ini sudah diproses sebelumnya.');
        }

        $redirectUrl = $this->getDocumentShowUrl($approval);
        return redirect($redirectUrl);
    }
}
