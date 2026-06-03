<?php

namespace App\Http\Controllers;

use App\Models\DokumenApproval;
use App\Models\HtaDanGpa;
use App\Models\HtaDanGpaDetail;
use App\Models\HtaMedis;
use App\Models\LembarDisposisi;
use App\Models\ListVendor;
use App\Models\ListVendorDetail;
use App\Models\MasterBarang;
use App\Models\MasterDepartemen;
use App\Models\MasterJenisPengajuan;
use App\Models\MasterPerusahaan;
use App\Models\MasterVendor;
use App\Models\PengajuanItem;
use App\Models\PengajuanPembelian;
use App\Models\PermintaanPembelian;
use App\Models\UsulanInvestasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class PengajuanPembelianController extends Controller
{

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $kodePerusahaan = auth()->user()->kodeperusahaan;
            $user = auth()->user();
            if ($user->hasRole('Admin') || $user->hasRole('CCP') || $user->hasRole('CEO') || $user->hasRole('Group Head')) {
                $data = PengajuanPembelian::with(['getPerusahaan', 'getJenisPermintaan', 'getPengajuanItem'])
                    ->orderBy('id', 'desc');
                if ($request->filled('perusahaan')) {
                    $data->where('KodePerusahaan', $request->perusahaan);
                }
                if ($request->filled('jenis')) {
                    $data->where('Jenis', $request->jenis);
                }
                if ($request->filled('status')) {
                    $data->where('Status', $request->status);
                }
            } elseif ($user->hasRole('SMI')) {
                $data = PengajuanPembelian::with(['getPerusahaan', 'getJenisPermintaan', 'getPengajuanItem'])
                    ->where('KodePerusahaan', $kodePerusahaan)
                    ->where('Jenis', 1)
                    ->orderBy('id', 'desc');
                if ($request->filled('status')) {
                    $data->where('Status', $request->status);
                }
            } elseif ($user->hasRole('LOGUM')) {
                $data = PengajuanPembelian::with(['getPerusahaan', 'getJenisPermintaan', 'getPengajuanItem'])
                    ->where('KodePerusahaan', $kodePerusahaan)
                    ->where('Jenis', '!=', 1)
                    ->orderBy('id', 'desc');

                if ($request->filled('status')) {
                    $data->where('Status', $request->status);
                }
            } else {
                $data = PengajuanPembelian::with(['getPerusahaan', 'getJenisPermintaan', 'getPengajuanItem'])
                    ->where('KodePerusahaan', $kodePerusahaan)
                    ->orderBy('id', 'desc');
                if ($request->filled('jenis')) {
                    $data->where('Jenis', $request->jenis);
                }
                if ($request->filled('status')) {
                    $data->where('Status', $request->status);
                }
            }
            return DataTables::of($data)
                ->addIndexColumn()
                // contoh penerapan pencarian di kolom realasi/virtual (supaya search datatable bisa)
                ->filter(function ($query) use ($request) {
                    if ($request->has('search') && $request->input('search.value') != '') {
                        $search = $request->input('search.value');
                        // Search pada kolom KodePengajuan, Status, relasi Nama Perusahaan, Nama Barang, dsb
                        $query->where(function ($q) use ($search) {
                            $q->where('KodePengajuan', 'like', "%{$search}%")
                                ->orWhere('Status', 'like', "%{$search}%")
                                ->orWhereHas('getPerusahaan', function ($sub) use ($search) {
                                    $sub->where('Nama', 'like', "%{$search}%");
                                })
                                ->orWhereHas('getJenisPermintaan', function ($sub) use ($search) {
                                    $sub->where('Nama', 'like', "%{$search}%");
                                })
                                ->orWhereHas('getPengajuanItem.getBarang', function ($sub) use ($search) {
                                    $sub->where('Nama', 'like', "%{$search}%")
                                        ->orWhere('Tipe', 'like', "%{$search}%");
                                });
                        });
                    }
                })
                ->editColumn('Jenis', function ($row) {
                    // Kolom Jenis ini berubah menjadi Nama dari relasi
                    return optional($row->getJenisPermintaan)->Nama ?? '-';
                })
                ->editColumn('KodePerusahaan', function ($row) {
                    return $row->getPerusahaan->Nama ?? '-';
                })
                ->addColumn('KodePengajuan', function ($row) {
                    $id = encrypt($row->id);
                    $kode = isset($row->KodePengajuan) ? $row->KodePengajuan : '-';
                    return '<a href="' . route('ajukan.show', $id) . '" style="color: #007bff; font-weight: bold;">' . e($kode) . '</a>';
                })
                ->addColumn('NamaBarang', function ($row) {
                    $namaBarang = '-';
                    $merek = '-';
                    if ($row->getPengajuanItem && count($row->getPengajuanItem) > 0) {
                        $item = $row->getPengajuanItem[0];
                        $namaBarang = $item->getBarang->Nama ?? '-';
                        $merek = $item->getBarang->getMerk->Nama ?? '';
                        $tipe = $item->getBarang->Tipe ?? '-';
                    }
                    return $namaBarang . ' / ' . $merek;
                })
                ->addColumn('action', function ($row) {
                    $id = $row->id;
                    return '
                        <button class="btn btn-md btn-danger btn-delete" data-id="' . $id . '" title="Hapus">
                            <i class="fa fa-trash"></i>
                        </button>
                    ';
                })
                ->addColumn('CekStatus', function ($row) {
                    $html = '-';
                    if ($row->Jenis === 1) {
                        $hta = 1;
                        $fui = [7, 11, 12];
                        $dispo = 9;
                        $cariHTa = HtaDanGpa::where('JenisForm', $hta)->where('IdPengajuan', $row->id)->first();
                        $cekHta = null;
                        if ($cariHTa) {
                            $cekHta = DokumenApproval::where('JenisFormId', $hta)
                                ->where('DokumenId', $cariHTa->id)
                                ->where('UserId', auth()->user()->id)
                                ->where('Status', 'Pending')
                                ->first();
                        }
                        $cekFui = null;
                        foreach ($fui as $JenisFui) {
                            $cariFUI = UsulanInvestasi::where('JenisForm', $JenisFui)->where('IdPengajuan', $row->id)->first();
                            $cek = null;
                            if ($cariFUI) {
                                $cek = DokumenApproval::where('JenisFormId', $JenisFui)
                                    ->where('DokumenId', $cariFUI->id)
                                    ->where('UserId', auth()->user()->id)
                                    ->where('Status', 'Pending')
                                    ->first();
                            }
                            if ($cek) {
                                $cekFui = $cek;
                                break;
                            }
                        }

                        // Cari dokumen Disposisi yang sesuai pengajuan
                        $cariDispo = LembarDisposisi::where('JenisForm', $dispo)->where('IdPengajuan', $row->id)->first();
                        $cekDispo = null;
                        if ($cariDispo) {
                            $cekDispo = DokumenApproval::where('JenisFormId', $dispo)
                                ->where('DokumenId', $cariDispo->id)
                                ->where('UserId', auth()->user()->id)
                                ->where('Status', 'Pending')
                                ->first();
                        }

                        $pesan = [];
                        if ($cekDispo) {
                            $pesan[] = '<span style="color:#dc3545;"><b>Form: Disposisi</b> - <i>Perlu Disetujui</i></span>';
                        }
                        if ($cekFui) {
                            $pesan[] = '<span style="color:#dc3545;"><b>Form: FUI</b> - <i>Perlu Disetujui</i></span>';
                        }
                        if ($cekHta) {
                            $pesan[] = '<span style="color:#dc3545;"><b>Form: HTA</b> - <i>Perlu Disetujui</i></span>';
                        }

                        if (!empty($pesan)) {
                            $html = implode('<br>', $pesan);
                        }
                    }
                    return $html;
                })
                ->addColumn('Status', function ($row) {
                    switch ($row->Status) {
                        case 'Draft':
                            // Abu-abu, icon pencil
                            return '<span class="badge" style="background-color:#6c757d;color:#fff;">
                                <i class="fa fa-pencil-alt"></i> Draft
                            </span>';
                        case 'Diajukan':
                            // Biru, icon paper-plane
                            return '<span class="badge" style="background-color:#0d6efd;color:#fff;">
                                <i class="fa fa-paper-plane"></i> Diajukan
                            </span>';
                        case 'Dalam Review':
                            // Jingga, icon search
                            return '<span class="badge" style="background-color:#fd7e14;color:#fff;">
                                <i class="fa fa-search"></i> Dalam Review
                            </span>';
                        case 'Selesai':
                            // Hijau tua, icon flag-checkered
                            return '<span class="badge" style="background-color:#198754;color:#fff;">
                                <i class="fa fa-flag-checkered"></i> Selesai
                            </span>';
                        case 'Disetujui':
                            // Hijau muda, icon thumbs-up
                            return '<span class="badge" style="background-color:#20c997;color:#fff;">
                                <i class="fa fa-thumbs-up"></i> Disetujui
                            </span>';
                        case 'Siap Presentasi':
                            // Biru muda, icon chalkboard-teacher
                            return '<span class="badge" style="background-color:#0dcaf0;color:#000;">
                                <i class="fa fa-chalkboard-teacher"></i> Siap Presentasi
                            </span>';
                        case 'Selesai Review':
                            // Ungu, icon clipboard-check
                            return '<span class="badge" style="background-color:#6f42c1;color:#fff;">
                                <i class="fa fa-clipboard-check"></i> Selesai Review
                            </span>';
                        case 'Menunggu Rekomendasi GH':
                            // Oranye, icon hourglass-half
                            return '<span class="badge" style="background-color:#ffc107;color:#212529;">
                                <i class="fa fa-hourglass-half"></i> Menunggu Rekomendasi GH
                            </span>';
                        case 'Ditolak':
                            // Merah, icon times-circle
                            return '<span class="badge" style="background-color:#dc3545;color:#fff;">
                                <i class="fa fa-times-circle"></i> Ditolak
                            </span>';
                        default:
                            return '<span class="badge" style="background-color:#f8f9fa;color:#212529;">
                                <i class="fa fa-question-circle"></i> ' . e($row->Status ?? '-') . '
                            </span>';
                    }
                })
                ->addColumn('TanggalPresentasi', function ($row) {
                    return $row->TanggalPresentasi
                        ? \Carbon\Carbon::parse($row->TanggalPresentasi)->translatedFormat('d M Y H:i')
                        : '-';
                })
                ->rawColumns(['action', 'KodePengajuan', 'Status', 'NamaBarang', 'TanggalPresentasi', 'CekStatus'])
                ->make(true);
        }
        $kodePerusahaan = auth()->user()->kodeperusahaan;
        $jenisPermintaan = MasterJenisPengajuan::get();
        $perusahaan = MasterPerusahaan::get();
        $permintaan = PermintaanPembelian::with('getPerusahaan', 'getDepartemen', 'getDiajukanOleh', 'getJenisPermintaan', 'getDetail')
            ->where('Status', 'Telah Disetujui')
            ->where('KodePerusahaan', $kodePerusahaan)
            ->whereDoesntHave('getPengajuanPembelian')
            ->latest()
            ->get();

        return view('form.pengajuan-pembelian.index', compact('permintaan', 'jenisPermintaan', 'perusahaan'));
    }
    public function indexSelesai(Request $request)
    {
        if ($request->ajax()) {
            $kodePerusahaan = auth()->user()->kodeperusahaan;
            $user = auth()->user();
            // Tampilkan hanya data dengan Status 'Selesai' untuk semua role
            if ($user->hasRole('Admin') || $user->hasRole('CCP') || $user->hasRole('CEO') || $user->hasRole('Group Head')) {
                $data = PengajuanPembelian::with(['getPerusahaan', 'getJenisPermintaan', 'getPengajuanItem'])
                    ->where('Status', 'Selesai')
                    ->orderBy('id', 'desc');
                if ($request->filled('perusahaan')) {
                    $data->where('KodePerusahaan', $request->perusahaan);
                }
                if ($request->filled('jenis')) {
                    $data->where('Jenis', $request->jenis);
                }
            } elseif ($user->hasRole('SMI')) {
                $data = PengajuanPembelian::with(['getPerusahaan', 'getJenisPermintaan', 'getPengajuanItem'])
                    ->where('KodePerusahaan', $kodePerusahaan)
                    ->where('Jenis', 1)
                    ->where('Status', 'Selesai')
                    ->orderBy('id', 'desc');
            } elseif ($user->hasRole('LOGUM')) {
                $data = PengajuanPembelian::with(['getPerusahaan', 'getJenisPermintaan', 'getPengajuanItem'])
                    ->where('KodePerusahaan', $kodePerusahaan)
                    ->where('Jenis', '!=', 1)
                    ->where('Status', 'Selesai')
                    ->orderBy('id', 'desc');
            } else {
                $data = PengajuanPembelian::with(['getPerusahaan', 'getJenisPermintaan', 'getPengajuanItem'])
                    ->where('KodePerusahaan', $kodePerusahaan)
                    ->where('Status', 'Selesai')
                    ->orderBy('id', 'desc');
                if ($request->filled('jenis')) {
                    $data->where('Jenis', $request->jenis);
                }
                if ($request->filled('status')) {
                    $data->where('Status', $request->status);
                }
            }
            return DataTables::of($data)
                ->addIndexColumn()
                // contoh penerapan pencarian di kolom realasi/virtual (supaya search datatable bisa)
                ->filter(function ($query) use ($request) {
                    if ($request->has('search') && $request->input('search.value') != '') {
                        $search = $request->input('search.value');
                        // Search pada kolom KodePengajuan, Status, relasi Nama Perusahaan, Nama Barang, dsb
                        $query->where(function ($q) use ($search) {
                            $q->where('KodePengajuan', 'like', "%{$search}%")
                                ->orWhere('Status', 'like', "%{$search}%")
                                ->orWhereHas('getPerusahaan', function ($sub) use ($search) {
                                    $sub->where('Nama', 'like', "%{$search}%");
                                })
                                ->orWhereHas('getJenisPermintaan', function ($sub) use ($search) {
                                    $sub->where('Nama', 'like', "%{$search}%");
                                })
                                ->orWhereHas('getPengajuanItem.getBarang', function ($sub) use ($search) {
                                    $sub->where('Nama', 'like', "%{$search}%")
                                        ->orWhere('Tipe', 'like', "%{$search}%");
                                });
                        });
                    }
                })
                ->editColumn('Jenis', function ($row) {
                    // Kolom Jenis ini berubah menjadi Nama dari relasi
                    return optional($row->getJenisPermintaan)->Nama ?? '-';
                })
                ->editColumn('KodePerusahaan', function ($row) {
                    return $row->getPerusahaan->Nama ?? '-';
                })
                ->addColumn('KodePengajuan', function ($row) {
                    $id = encrypt($row->id);
                    $kode = isset($row->KodePengajuan) ? $row->KodePengajuan : '-';
                    return '<a href="' . route('ajukan.show', $id) . '" style="color: #007bff; font-weight: bold;">' . e($kode) . '</a>';
                })
                ->addColumn('NamaBarang', function ($row) {
                    $namaBarang = '-';
                    $merek = '-';
                    if ($row->getPengajuanItem && count($row->getPengajuanItem) > 0) {
                        $item = $row->getPengajuanItem[0];
                        $namaBarang = $item->getBarang->Nama ?? '-';
                        $merek = $item->getBarang->getMerk->Nama ?? '';
                        $tipe = $item->getBarang->Tipe ?? '-';
                    }
                    return $namaBarang . ' / ' . $merek;
                })
                ->addColumn('action', function ($row) {
                    $id = $row->id;
                    return '
                        <button class="btn btn-md btn-danger btn-delete" data-id="' . $id . '" title="Hapus">
                            <i class="fa fa-trash"></i>
                        </button>
                    ';
                })
                ->addColumn('CekStatus', function ($row) {
                    $html = '-';
                    if ($row->Jenis === 1) {
                        $hta = 1;
                        $fui = [7, 11, 12];
                        $dispo = 9;
                        $cariHTa = HtaDanGpa::where('JenisForm', $hta)->where('IdPengajuan', $row->id)->first();
                        $cekHta = null;
                        if ($cariHTa) {
                            $cekHta = DokumenApproval::where('JenisFormId', $hta)
                                ->where('DokumenId', $cariHTa->id)
                                ->where('UserId', auth()->user()->id)
                                ->where('Status', 'Pending')
                                ->first();
                        }
                        $cekFui = null;
                        foreach ($fui as $JenisFui) {
                            $cariFUI = UsulanInvestasi::where('JenisForm', $JenisFui)->where('IdPengajuan', $row->id)->first();
                            $cek = null;
                            if ($cariFUI) {
                                $cek = DokumenApproval::where('JenisFormId', $JenisFui)
                                    ->where('DokumenId', $cariFUI->id)
                                    ->where('UserId', auth()->user()->id)
                                    ->where('Status', 'Pending')
                                    ->first();
                            }
                            if ($cek) {
                                $cekFui = $cek;
                                break;
                            }
                        }

                        // Cari dokumen Disposisi yang sesuai pengajuan
                        $cariDispo = LembarDisposisi::where('JenisForm', $dispo)->where('IdPengajuan', $row->id)->first();
                        $cekDispo = null;
                        if ($cariDispo) {
                            $cekDispo = DokumenApproval::where('JenisFormId', $dispo)
                                ->where('DokumenId', $cariDispo->id)
                                ->where('UserId', auth()->user()->id)
                                ->where('Status', 'Pending')
                                ->first();
                        }

                        $pesan = [];
                        if ($cekDispo) {
                            $pesan[] = '<span style="color:#dc3545;"><b>Form: Disposisi</b> - <i>Perlu Disetujui</i></span>';
                        }
                        if ($cekFui) {
                            $pesan[] = '<span style="color:#dc3545;"><b>Form: FUI</b> - <i>Perlu Disetujui</i></span>';
                        }
                        if ($cekHta) {
                            $pesan[] = '<span style="color:#dc3545;"><b>Form: HTA</b> - <i>Perlu Disetujui</i></span>';
                        }

                        if (!empty($pesan)) {
                            $html = implode('<br>', $pesan);
                        }
                    }
                    return $html;
                })
                ->addColumn('Status', function ($row) {
                    switch ($row->Status) {
                        case 'Draft':
                            // Abu-abu, icon pencil
                            return '<span class="badge" style="background-color:#6c757d;color:#fff;">
                                <i class="fa fa-pencil-alt"></i> Draft
                            </span>';
                        case 'Diajukan':
                            // Biru, icon paper-plane
                            return '<span class="badge" style="background-color:#0d6efd;color:#fff;">
                                <i class="fa fa-paper-plane"></i> Diajukan
                            </span>';
                        case 'Dalam Review':
                            // Jingga, icon search
                            return '<span class="badge" style="background-color:#fd7e14;color:#fff;">
                                <i class="fa fa-search"></i> Dalam Review
                            </span>';
                        case 'Selesai':
                            // Hijau tua, icon flag-checkered
                            return '<span class="badge" style="background-color:#198754;color:#fff;">
                                <i class="fa fa-flag-checkered"></i> Selesai
                            </span>';
                        case 'Disetujui':
                            // Hijau muda, icon thumbs-up
                            return '<span class="badge" style="background-color:#20c997;color:#fff;">
                                <i class="fa fa-thumbs-up"></i> Disetujui
                            </span>';
                        case 'Siap Presentasi':
                            // Biru muda, icon chalkboard-teacher
                            return '<span class="badge" style="background-color:#0dcaf0;color:#000;">
                                <i class="fa fa-chalkboard-teacher"></i> Siap Presentasi
                            </span>';
                        case 'Selesai Review':
                            // Ungu, icon clipboard-check
                            return '<span class="badge" style="background-color:#6f42c1;color:#fff;">
                                <i class="fa fa-clipboard-check"></i> Selesai Review
                            </span>';
                        case 'Menunggu Rekomendasi GH':
                            // Oranye, icon hourglass-half
                            return '<span class="badge" style="background-color:#ffc107;color:#212529;">
                                <i class="fa fa-hourglass-half"></i> Menunggu Rekomendasi GH
                            </span>';
                        case 'Ditolak':
                            // Merah, icon times-circle
                            return '<span class="badge" style="background-color:#dc3545;color:#fff;">
                                <i class="fa fa-times-circle"></i> Ditolak
                            </span>';
                        default:
                            return '<span class="badge" style="background-color:#f8f9fa;color:#212529;">
                                <i class="fa fa-question-circle"></i> ' . e($row->Status ?? '-') . '
                            </span>';
                    }
                })
                ->addColumn('TanggalPresentasi', function ($row) {
                    return $row->TanggalPresentasi
                        ? \Carbon\Carbon::parse($row->TanggalPresentasi)->translatedFormat('d M Y H:i')
                        : '-';
                })
                ->rawColumns(['action', 'KodePengajuan', 'Status', 'NamaBarang', 'TanggalPresentasi', 'CekStatus'])
                ->make(true);
        }
    }
    /**}
     * Show the form for creating a new resource..show
     */
    public function create($id)
    {
        $vendor = MasterVendor::where('Status', 'Y')->orderBy('Nama', 'asc')->get();
        $masterbarang = MasterBarang::get();
        $permintaan = PermintaanPembelian::with('getPerusahaan', 'getDepartemen', 'getDiajukanOleh', 'getDetail', 'getPengajuanPembelian.getVendor.getVendorDetail')->find($id);
        $JenisPengajuan = MasterJenisPengajuan::get();
        $departemen = MasterDepartemen::get();
        return view('form.pengajuan-pembelian.create', compact('JenisPengajuan', 'masterbarang', 'permintaan', 'vendor', 'departemen'));
    }

    public function SimpanDraft($id)
    {
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'pengajuan.tanggal' => 'required|date',
            'pengajuan.id_permintaan' => 'required',
            'pengajuan.jenis' => 'required|string|max:255',
            'pengajuan.tujuan' => 'required|string|max:255',
            'pengajuan.perkiraan_utilitasi_bulanan' => 'required',
            'pengajuan.perkiraan_bep_pada_tahun' => 'required',
            'pengajuan.rkap' => 'required',
            'pengajuan.nominal_rkap' => 'required',
            'vendors.*.penawaran_file.*' => 'required|file|max:5120',
        ]);

        $nomor = $this->generateNomorPengajuan();

        $pengajuan = PengajuanPembelian::updateOrCreate(
            [
                'IdPermintaan' => $request->pengajuan['id_permintaan'],
            ],
            [
                'KodePengajuan' => $nomor,
                'Tanggal' => $request->pengajuan['tanggal'],
                'Jenis' => $request->pengajuan['jenis'],
                'Tujuan' => $request->pengajuan['tujuan'],
                'PerkiraanUtilitasiBulanan' => $request->pengajuan['perkiraan_utilitasi_bulanan'],
                'PerkiraanBepPadaTahun' => $request->pengajuan['perkiraan_bep_pada_tahun'],
                'Rkap' => $request->pengajuan['rkap'],
                'NominalRkap' => preg_replace('/\D/', '', $request->pengajuan['nominal_rkap']),
                'DepartemenId' => $request->pengajuan['departemen'],
                'KodePerusahaan' => auth()->user()->kodeperusahaan,
                'UserCreate' => auth()->user()->name,
            ]
        );

        foreach ($request->vendors as $key => $vendorData) {
            if (!isset($vendorData['vendor_id']) || $vendorData['vendor_id'] === null) {
                continue;
            }
            $filename = null;
            $ListVendorOld = ListVendor::where('IdPengajuan', $pengajuan->id)
                ->where('VendorKe', $key + 1)
                ->first();
            $filename = null;
            $isOldFileExists = ($ListVendorOld && $ListVendorOld->SuratPenawaranVendor);
            if (isset($vendorData['penawaran_file']) && is_array($vendorData['penawaran_file'])) {
                $fileArr = $vendorData['penawaran_file'];
                if (isset($fileArr[0]) && $fileArr[0] instanceof \Illuminate\Http\UploadedFile) {
                    $file = $fileArr[0];
                    $filename = 'penawaran_' . time() . '_' . ($key + 1) . '.' . $file->getClientOriginalExtension();
                    $file->storeAs('penawaran_vendor', $filename, 'public');
                } elseif (isset($fileArr[0]) && is_string($fileArr[0]) && $fileArr[0]) {
                    $filename = $fileArr[0];
                }
            } elseif (isset($vendorData['penawaran_file']) && is_string($vendorData['penawaran_file']) && $vendorData['penawaran_file'] !== null && $vendorData['penawaran_file'] !== '') {
                $filename = $vendorData['penawaran_file'];
            } elseif ($request->hasFile('penawaran_file_' . ($key + 1))) {
                $file = $request->file('penawaran_file_' . ($key + 1));
                if ($file) {
                    $filename = 'penawaran_' . time() . '_' . ($key + 1) . '.' . $file->getClientOriginalExtension();
                    $file->storeAs('penawaran_vendor', $filename, 'public');
                }
            } elseif ($isOldFileExists) {
                $filename = $ListVendorOld->SuratPenawaranVendor;
            }
            if (!$filename) {
                return back()
                    ->withErrors(['vendors.' . $key . '.penawaran_file.0' => 'File penawaran vendor wajib diupload.'])
                    ->withInput();
            }

            $ListVendor = ListVendor::updateOrCreate(
                [
                    'IdPengajuan' => $pengajuan->id,
                    'VendorKe' => $key + 1,
                ],
                [
                    'NamaVendor' => $vendorData['vendor_id'],
                    'NamaPic' => $vendorData['nama_pic'],
                    'KontakPic' => $vendorData['no_hp_pic'],
                    'SuratPenawaranVendor' => $filename,
                    'HargaTanpaDiskon' => preg_replace('/\D/', '', $vendorData['total_harga_sebelum_diskon']) !== '' ? preg_replace('/\D/', '', $vendorData['total_harga_sebelum_diskon']) : 0,
                    'HargaDenganDiskon' => preg_replace('/\D/', '', $vendorData['total_harga_setelah_diskon']) !== '' ? preg_replace('/\D/', '', $vendorData['total_harga_setelah_diskon']) : 0,
                    'TotalDiskon' => preg_replace('/\D/', '', $vendorData['total_diskon']) !== '' ? preg_replace('/\D/', '', $vendorData['total_diskon']) : 0,
                    'Ppn' => isset($vendorData['ppn_persen']) && $vendorData['ppn_persen'] !== '' ? $vendorData['ppn_persen'] : 0,
                    'TotalPpn' => preg_replace('/\D/', '', $vendorData['total_ppn']) !== '' ? preg_replace('/\D/', '', $vendorData['total_ppn']) : 0,
                    'TotalHarga' => preg_replace('/\D/', '', $vendorData['grand_total']) !== '' ? preg_replace('/\D/', '', $vendorData['grand_total']) : 0,
                    'KodePerusahaan' => auth()->user()->kodeperusahaan,
                    // Masukkan UserCreate atau UserUpdate sesuai kebutuhan
                    'UserCreate' => auth()->user()->name,
                    'UserUpdate' => auth()->user()->name,
                ]
            );

            if (isset($vendorData['details']) && is_array($vendorData['details'])) {
                ListVendorDetail::where('IdPengajuan', $pengajuan->id)
                    ->where('IdListVendor', $ListVendor->id)
                    ->forceDelete();

                foreach ($vendorData['details'] as $detailB) {
                    $diskonValue = 0;
                    if (isset($detailB['diskon_item'])) {
                        if (isset($detailB['jenis_diskon_item']) && strtolower($detailB['jenis_diskon_item']) == 'rp') {
                            // Jika Rp, ambil angka aja
                            $diskonValue = preg_replace('/\D/', '', $detailB['diskon_item']);
                        } else {
                            // Jika bukan Rp, buang semua karakter kecuali titik, dan jika ada koma ganti menjadi titik
                            $diskonValue = str_replace(',', '.', $detailB['diskon_item']);
                            $diskonValue = preg_replace('/[^0-9.]/', '', $diskonValue);
                        }
                    }
                    ListVendorDetail::create([
                        'IdPengajuan' => $pengajuan->id,
                        'IdListVendor' => $ListVendor->id,
                        'NamaBarang' => $detailB['barang_id'] ?? 0,
                        'NamaVendor' => null,
                        'Jumlah' => isset($detailB['jumlah']) ? $detailB['jumlah'] : 0,
                        'HargaSatuan' => isset($detailB['harga_satuan']) ? preg_replace('/\D/', '', $detailB['harga_satuan']) : 0,
                        'Diskon' => $diskonValue,
                        'JenisDiskon' => $detailB['jenis_diskon_item'] ?? 0,
                        'TotalDiskon' => isset($detailB['total_diskon']) ? preg_replace('/\D/', '', $detailB['total_diskon']) : 0,
                        'TotalHarga' => isset($detailB['total_harga']) ? preg_replace('/\D/', '', $detailB['total_harga']) : 0,
                        'KodePerusahaan' => auth()->user()->kodeperusahaan,
                        'UserCreate' => auth()->user()->name,
                    ]);
                }
            }
        }
        foreach ($request->vendors[0]['details'] as $key => $listalat) {
            PengajuanItem::updateOrCreate(
                [
                    'IdPengajuan' => $pengajuan->id,
                    'IdBarang' => $listalat['barang_id'],
                ],
                [
                    'RencanaPenempatan' => $listalat['rencana_penempatan'] ?? null,
                    'DiajukanOleh' => $listalat['diajukan_oleh'] ?? null,
                    'DiajukanDepartemen' => $request->pengajuan['departemen'] ?? null,
                    'Jumlah' => $listalat['jumlah'] ?? null,
                    'Satuan' => $listalat['satuan'] ?? null,
                    'VendorAcc' => $listalat['vendor_acc'] ?? null,
                    'HargaSatuanAcc' => isset($listalat['harga_satuan_acc']) ? preg_replace('/\D/', '', $listalat['harga_satuan_acc']) : null,
                    'HargaNegoAcc' => isset($listalat['harga_nego_acc']) ? preg_replace('/\D/', '', $listalat['harga_nego_acc']) : null,
                    'HargaAkhirFui' => isset($listalat['harga_akhir_fui']) ? preg_replace('/\D/', '', $listalat['harga_akhir_fui']) : null,
                    'KodePerusahaan' => auth()->user()->kodeperusahaan,
                    'UserCreate' => auth()->user()->name,
                ]
            );
        }

        activity('pengajuan_pembelian')
            ->causedBy(auth()->user())
            ->performedOn($pengajuan)
            ->withProperties([
                'attributes' => $pengajuan->toArray(),
                'vendors' => $request->vendors,
            ])
            ->log('Membuat pengajuan pembelian baru dengan kode ' . $pengajuan->KodePengajuan);
        return redirect()->back()->with('success', 'Pengisian vendor berhasil, silahkan lanjutkan isi vendor selanjutnya jika diperlukan');
    }

    private function generateNomorPengajuan()
    {
        $prefix = 'PJ';
        $tahun = date('y');  // 2 digit tahun
        $bulan = date('m');  // 2 digit bulan

        $maxNomor = PengajuanPembelian::where('KodePengajuan', 'like', "{$prefix}{$tahun}{$bulan}%")
            ->orderByDesc('KodePengajuan')
            ->value('KodePengajuan');

        if ($maxNomor) {
            $lastNumber = (int) substr($maxNomor, -4);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        $nomorAkhir = $prefix . $tahun . $bulan . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
        return $nomorAkhir;
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $id = decrypt($id);
        $data = PengajuanPembelian::with('getVendor.getVendorDetail', 'getJenisPermintaan', 'getPengajuanItem.getBarang', 'getPengajuanItem.getHtaGpa', 'getPengajuanItem.getRekomendasi', 'getPengajuanItem.getFui', 'getPengajuanItem.getDisposisi', 'getDepartemen', 'getPengajuanItem.getFs')->find($id);
        // dd($data);
        $vendor = MasterVendor::orderBy('Nama', 'asc')->get();
        $masterbarang = MasterBarang::get();
        return view('form.pengajuan-pembelian.show', compact('data', 'vendor', 'masterbarang'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $id = decrypt($id);
        $vendor = MasterVendor::where('Status', 'Y')->orderBy('Nama', 'asc')->get();
        $masterbarang = MasterBarang::get();
        $permintaan = PermintaanPembelian::with('getPerusahaan', 'getDepartemen', 'getDiajukanOleh', 'getDetail', 'getPengajuanPembelian.getVendor.getVendorDetail')->find($id);
        $JenisPengajuan = MasterJenisPengajuan::get();
        $departemen = MasterDepartemen::get();
        $data = PengajuanPembelian::with('getVendor.getVendorDetail', 'getJenisPermintaan', 'getPengajuanItem.getBarang', 'getPengajuanItem.getHtaGpa', 'getPengajuanItem.getRekomendasi', 'getPengajuanItem.getFui', 'getDepartemen')->find($id);
        // dd($id);
        return view('form.pengajuan-pembelian.edit', compact('JenisPengajuan', 'masterbarang', 'permintaan', 'vendor', 'departemen', 'data'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // dd($request->all());
        $request->validate([
            'pengajuan.tanggal' => 'required|date',
            'pengajuan.id_permintaan' => 'required',
            'pengajuan.jenis' => 'required|string|max:255',
            'pengajuan.tujuan' => 'required|string|max:255',
            'pengajuan.perkiraan_utilitasi_bulanan' => 'required',
            'pengajuan.perkiraan_bep_pada_tahun' => 'required',
            'pengajuan.rkap' => 'required',
            'pengajuan.nominal_rkap' => 'required',
        ]);

        $pengajuan = PengajuanPembelian::findOrFail($id);

        $pengajuan->Tanggal = $request->pengajuan['tanggal'];
        $pengajuan->Jenis = $request->pengajuan['jenis'];
        $pengajuan->Tujuan = $request->pengajuan['tujuan'];
        $pengajuan->PerkiraanUtilitasiBulanan = $request->pengajuan['perkiraan_utilitasi_bulanan'];
        $pengajuan->PerkiraanBepPadaTahun = $request->pengajuan['perkiraan_bep_pada_tahun'];
        $pengajuan->Rkap = $request->pengajuan['rkap'];
        $pengajuan->NominalRkap = preg_replace('/\D/', '', $request->pengajuan['nominal_rkap']);
        $pengajuan->DepartemenId = $request->pengajuan['departemen'];
        $pengajuan->KodePerusahaan = auth()->user()->kodeperusahaan;
        $pengajuan->UserUpdate = auth()->user()->name;
        $pengajuan->save();

        // Dapatkan vendor lama & detailnya
        $existingVendors = ListVendor::where('IdPengajuan', $pengajuan->id)->get()->keyBy('VendorKe');
        $existingVendorDetails = ListVendorDetail::where('IdPengajuan', $pengajuan->id)->get()->groupBy('IdListVendor');

        $usedVendorKeys = [];

        foreach ($request->vendors as $key => $vendorData) {
            if (!isset($vendorData['vendor_id']) || $vendorData['vendor_id'] === null) {
                continue;
            }

            $vendorKe = $key + 1;  // Penentu urutan vendor
            $usedVendorKeys[] = $vendorKe;

            // Cek jika vendor ke-x sudah ada
            $ListVendor = $existingVendors->get($vendorKe);
            $filename = null;

            if ($ListVendor) {
                // Sudah ada, patch/replace data vendor
            }

            // Batasi ukuran file 5 MB (5 * 1024 * 1024)
            $maxFileSize = 5 * 1024 * 1024;
            if (isset($vendorData['penawaran_file']) && $vendorData['penawaran_file'] instanceof \Illuminate\Http\UploadedFile) {
                $file = $vendorData['penawaran_file'];
                if ($file->getSize() > $maxFileSize) {
                    return redirect()->back()->with('error', 'Ukuran file penawaran vendor melebihi batas maksimum 5 MB.');
                }
                $filename = 'penawaran_' . time() . '_' . ($key + 1) . '.' . $file->getClientOriginalExtension();
                $file->storeAs('penawaran_vendor', $filename, 'public');
            } elseif (isset($vendorData['penawaran_file']) && is_array($vendorData['penawaran_file'])) {
                $fileArr = $vendorData['penawaran_file'];
                if (isset($fileArr[0]) && $fileArr[0] instanceof \Illuminate\Http\UploadedFile) {
                    $file = $fileArr[0];
                    if ($file->getSize() > $maxFileSize) {
                        return redirect()->back()->with('error', 'Ukuran file penawaran vendor melebihi batas maksimum 5 MB.');
                    }
                    $filename = 'penawaran_' . time() . '_' . ($key + 1) . '.' . $file->getClientOriginalExtension();
                    $file->storeAs('penawaran_vendor', $filename, 'public');
                } elseif (isset($fileArr[0]) && is_string($fileArr[0]) && $fileArr[0]) {
                    $filename = $fileArr[0];
                }
            } elseif (isset($vendorData['penawaran_file']) && is_string($vendorData['penawaran_file']) && $vendorData['penawaran_file'] !== null && $vendorData['penawaran_file'] !== '') {
                $filename = $vendorData['penawaran_file'];
            } elseif ($request->hasFile('penawaran_file_' . ($key + 1))) {
                $file = $request->file('penawaran_file_' . ($key + 1));
                if ($file) {
                    if ($file->getSize() > $maxFileSize) {
                        return redirect()->back()->with('error', 'Ukuran file penawaran vendor melebihi batas maksimum 5 MB.');
                    }
                    $filename = 'penawaran_' . time() . '_' . ($key + 1) . '.' . $file->getClientOriginalExtension();
                    $file->storeAs('penawaran_vendor', $filename, 'public');
                }
            } elseif ($ListVendor && $ListVendor->SuratPenawaranVendor) {
                $filename = $ListVendor->SuratPenawaranVendor;
            }

            $vendorDataToSave = [
                'IdPengajuan' => $pengajuan->id,
                'VendorKe' => $vendorKe,
                'NamaPic' => isset($vendorData['nama_pic']) && $vendorData['nama_pic'] !== null && $vendorData['nama_pic'] !== '' ? $vendorData['nama_pic'] : 'Tidak Diisi',
                'KontakPic' => isset($vendorData['no_hp_pic']) && $vendorData['no_hp_pic'] !== null && $vendorData['no_hp_pic'] !== '' ? $vendorData['no_hp_pic'] : 'Tidak Diisi',
                'NamaVendor' => $vendorData['vendor_id'],
                'SuratPenawaranVendor' => $filename,
                'HargaTanpaDiskon' => preg_replace('/\D/', '', $vendorData['total_harga_sebelum_diskon']) !== '' ? preg_replace('/\D/', '', $vendorData['total_harga_sebelum_diskon']) : 0,
                'HargaDenganDiskon' => preg_replace('/\D/', '', $vendorData['total_harga_setelah_diskon']) !== '' ? preg_replace('/\D/', '', $vendorData['total_harga_setelah_diskon']) : 0,
                'TotalDiskon' => preg_replace('/\D/', '', $vendorData['total_diskon']) !== '' ? preg_replace('/\D/', '', $vendorData['total_diskon']) : 0,
                'Ppn' => isset($vendorData['ppn_persen']) && $vendorData['ppn_persen'] !== '' ? $vendorData['ppn_persen'] : 0,
                'TotalPpn' => preg_replace('/\D/', '', $vendorData['total_ppn']) !== '' ? preg_replace('/\D/', '', $vendorData['total_ppn']) : 0,
                'TotalHarga' => preg_replace('/\D/', '', $vendorData['grand_total']) !== '' ? preg_replace('/\D/', '', $vendorData['grand_total']) : 0,
                'KodePerusahaan' => auth()->user()->kodeperusahaan,
                'UserUpdate' => auth()->user()->name,
            ];

            if ($ListVendor) {
                $vendorDataToSave['UserCreate'] = $ListVendor->UserCreate ?: auth()->user()->name;
                $ListVendor->update($vendorDataToSave);
            } else {
                $vendorDataToSave['UserCreate'] = auth()->user()->name;
                $ListVendor = ListVendor::create($vendorDataToSave);
            }

            // Simpan detail vendor, update jika ada, hapus jika diperlukan
            $existingDetailsForThisVendor = $existingVendorDetails->get($ListVendor->id) ?? collect();
            $usedDetailIds = [];

            if (isset($vendorData['details']) && is_array($vendorData['details'])) {
                foreach ($vendorData['details'] as $detailBKey => $detailB) {
                    // Cari jika detail sudah ada berdasarkan urutan
                    $detailToUpdate = $existingDetailsForThisVendor->get($detailBKey);

                    $diskonValue = 0;
                    if (isset($detailB['diskon_item'])) {
                        if (isset($detailB['jenis_diskon_item']) && strtolower($detailB['jenis_diskon_item']) == 'rp') {
                            $diskonValue = preg_replace('/\D/', '', $detailB['diskon_item']);
                        } else {
                            $diskonValue = str_replace(',', '.', $detailB['diskon_item']);
                            $diskonValue = preg_replace('/[^0-9.]/', '', $diskonValue);
                        }
                    }

                    $detailDataToSave = [
                        'IdPengajuan' => $pengajuan->id,
                        'IdListVendor' => $ListVendor->id,
                        'NamaBarang' => $detailB['barang_id'] ?? 0,
                        'NamaVendor' => null,
                        'Jumlah' => isset($detailB['jumlah']) ? $detailB['jumlah'] : 0,
                        'HargaSatuan' => isset($detailB['harga_satuan']) ? (preg_replace('/\D/', '', $detailB['harga_satuan']) !== '' ? preg_replace('/\D/', '', $detailB['harga_satuan']) : 0) : 0,
                        'Diskon' => $diskonValue,
                        'JenisDiskon' => $detailB['jenis_diskon_item'] ?? 0,
                        'TotalDiskon' => isset($detailB['total_diskon']) ? (preg_replace('/\D/', '', $detailB['total_diskon']) !== '' ? preg_replace('/\D/', '', $detailB['total_diskon']) : 0) : 0,
                        'TotalHarga' => isset($detailB['total_harga']) ? (preg_replace('/\D/', '', $detailB['total_harga']) !== '' ? preg_replace('/\D/', '', $detailB['total_harga']) : 0) : 0,
                        'KodePerusahaan' => auth()->user()->kodeperusahaan,
                        'UserCreate' => auth()->user()->name,
                    ];

                    if ($detailToUpdate) {
                        $detailToUpdate->update($detailDataToSave);
                        $usedDetailIds[] = $detailToUpdate->id;
                    } else {
                        $newDetail = ListVendorDetail::create($detailDataToSave);
                        $usedDetailIds[] = $newDetail->id;
                    }
                }
            }

            // Hapus detail yang tidak ada di input baru (hanya milik vendor ini)
            $detailsToDelete = $existingDetailsForThisVendor->whereNotIn('id', $usedDetailIds);
            foreach ($detailsToDelete as $dtd) {
                $dtd->delete();
            }
        }

        // Hapus vendor (dan detailnya) yang tidak ada pada request vendor baru
        $vendorsToDelete = $existingVendors->whereNotIn('VendorKe', $usedVendorKeys);
        foreach ($vendorsToDelete as $vendorDel) {
            // Hapus detail
            ListVendorDetail::where('IdListVendor', $vendorDel->id)->delete();
            // Hapus vendor
            $vendorDel->delete();
        }

        // foreach ($request->vendors[0]['details'] as $key => $listalat) {
        //     PengajuanItem::create([
        //         'IdPengajuan' => $pengajuan->id,
        //         'IdBarang' => $listalat['barang_id'],
        //         'Jumlah' => $listalat['jumlah'] ?? null,
        //         'Satuan' => $listalat['satuan'] ?? null,
        //         'HargaSatuan' => isset($listalat['harga_satuan']) ? preg_replace('/\D/', '', $listalat['harga_satuan']) : null,
        //         'HargaNego' => isset($listalat['harga_nego']) ? preg_replace('/\D/', '', $listalat['harga_nego']) : null,
        //         'UserCreate' => auth()->user()->name,
        //         'UserUpdate' => auth()->user()->name,
        //     ]);
        // }

        activity('pengajuan_pembelian')
            ->causedBy(auth()->user())
            ->performedOn($pengajuan)
            ->withProperties([
                'attributes' => $pengajuan->toArray(),
                'vendors' => $request->vendors,
            ])
            ->log('Memperbarui pengajuan pembelian dengan kode ' . $pengajuan->KodePengajuan);

        return back()->with('success', 'Pengajuan pembelian berhasil diperbarui');
    }

    public function UpdatePengajuan(Request $request, $id)
    {
        $data = PengajuanPembelian::with('getRekomendasi.getRekomedasiDetail', 'getVendor.getVendorDetail', 'getJenisPermintaan', 'getPengajuanItem.getBarang', 'getPengajuanItem.getHtaGpa', 'getPengajuanItem.getRekomendasi', 'getPengajuanItem.getFui', 'getPengajuanItem.getDisposisi', 'getDepartemen', 'getPengajuanItem.getFs', 'getHtaGpa', 'getPengajuanItem.getFui')->find($id);
        if ($request->Status === 'Ditolak') {
            $pengajuan = PengajuanPembelian::findOrFail($id);
            $pengajuan->Status = $request->Status;
            $pengajuan->Keterangan = $request->Keterangan ?? '';
            $pengajuan->save();

            if (function_exists('activity')) {
                activity()
                    ->causedBy(auth()->user()->id)
                    ->withProperties([
                        'ip' => request()->ip(),
                        'input' => $request->all(),  // Melampirkan semua inputan user
                        'status' => $request->Status,
                        'keterangan' => $request->Keterangan ?? '',
                    ])
                    ->log('Menolak pengajuan pembelian ke CCP: ' . $pengajuan->KodePengajuan);
            }
            return redirect()->back()->with('success', 'Pengajuan Berhasil Ditolak, Segera Informasikan Ke Pengaju');
        }
        if ($request->Status === 'Siap Presentasi') {
            $cekrekom1 = $data->getRekomendasi[0]->getRekomedasiDetail->where('Rekomendasi', 1)->first();
            if ($data->Jenis == '1') {
                // dd('satu');
                $cekDisposisi = $data->getPengajuanItem[0]->getDisposisi;
                $cekFui = $data->getPengajuanItem[0]->getFui;
                $cek = $data->getPengajuanItem[0]->getFs;
                $countCek = $cek->count();
                // dd($cek);
                if (empty($data->getHtaGpa)) {
                    return back()->with('error', 'HTA / GPA belum diisi. Proses tidak dapat diteruskan.');
                }
                // CekApprovalHta
                $approvalHTA = DokumenApproval::with('getUser', 'getJabatan', 'getDepartemen')
                    ->where('JenisFormId', $data->getHtaGpa->JenisForm)
                    ->where('DokumenId', $data->getHtaGpa->id)
                    ->orderBy('Urutan', 'asc')
                    ->get();

                $semuaApprovedHTA = $approvalHTA->every(function ($item) {
                    return $item->Status === 'Approved';
                });

                if (empty($cekFui)) {
                    return back()->with('error', 'FUI belum diisi. Proses tidak dapat diteruskan.');
                }
                // ApprovalFUI
                $approvalFUI = DokumenApproval::with('getUser', 'getJabatan', 'getDepartemen')
                    ->where('JenisFormId', $cekFui->JenisForm)
                    ->where('DokumenId', $cekFui->id)
                    ->orderBy('Urutan', 'asc')
                    ->get();

                $semuaApprovedFUI = $approvalFUI->every(function ($item) {
                    return $item->Status === 'Approved';
                });

                if (empty($cekDisposisi)) {
                    return back()->with('error', 'Disposisi belum diisi. Proses tidak dapat diteruskan.');
                }

                $approvalDisposisi = DokumenApproval::with('getUser', 'getJabatan', 'getDepartemen')
                    ->where('JenisFormId', $cekDisposisi->JenisForm)
                    ->where('DokumenId', $cekDisposisi->id)
                    ->orderBy('Urutan', 'asc')
                    ->get();

                $semuaApprovedDisposisi = $approvalDisposisi->every(function ($item) {
                    return $item->Status === 'Approved';
                });

                $masterBarangId = $data->getPengajuanItem[0]->getBarang->id ?? null;
                // if ($cekrekom1->HargaNego > 100000000 && $cek && $masterBarangId != 294) {
                //     return back()->with('error', 'FS tidak boleh kosong sebelum dapat mengubah status menjadi Siap Presentasi untuk pengajuan di atas 100 juta.');
                // }
                $errors = [];
                if ($cekrekom1->HargaNego > 100000000 && $countCek > 0 && $masterBarangId != 294) {
                    $approvalFS = DokumenApproval::with('getUser', 'getJabatan', 'getDepartemen')
                        ->where('JenisFormId', $cek->JenisForm)
                        ->where('DokumenId', $cek->id)
                        ->orderBy('Urutan', 'asc')
                        ->get();
                    // dd($approvalFS);
                    $semuaApprovedFS = $approvalFS->every(function ($item) {
                        return $item->Status === 'Approved';
                    });
                    // 2. Cek Approval FS
                    if (!$semuaApprovedFS) {
                        $notApprovedFs = $approvalFS->filter(function ($item) {
                            return $item->Status !== 'Approved';
                        })->values();

                        if ($notApprovedFs->count() > 0) {
                            $list = $notApprovedFs->map(function ($item, $idx) {
                                $name = $item->getUser->name ?? 'Pengguna terkait';
                                return ($idx + 1) . ". {$name}";
                            })->implode('<br>');
                            $errors[] = "Daftar Nama Belum Approve FS:<br>{$list}";
                        } else {
                            $errors[] = 'Dokumen <b>FS</b> belum diapprove.';
                        }
                    }
                } else {
                    return back()->with('error', 'FS tidak boleh kosong sebelum dapat mengubah status menjadi Siap Presentasi untuk pengajuan di atas 100 juta.');
                }


                // 1. Cek Approval HTA / GPA
                if (!$semuaApprovedHTA) {
                    $notApprovedHta = $approvalHTA->filter(function ($item) {
                        return $item->Status !== 'Approved';
                    })->values();

                    if ($notApprovedHta->count() > 0) {
                        $list = $notApprovedHta->map(function ($item, $idx) {
                            $name = $item->getUser->name ?? 'Pengguna terkait';
                            return ($idx + 1) . ". {$name}";
                        })->implode('<br>');
                        $errors[] = "Daftar Nama Belum Approve HTA / GPA:<br>{$list}";
                    } else {
                        $errors[] = 'Dokumen <b>HTA / GPA</b> belum diapprove.';
                    }
                }

                // 3. Cek Approval FUI
                if (!$semuaApprovedFUI) {
                    $notApprovedFui = $approvalFUI->filter(function ($item) {
                        return $item->Status !== 'Approved';
                    })->values();
                    $only81NotApproved = $notApprovedFui->every(function ($item) {
                        return $item->UserId == 81;
                    }) && $notApprovedFui->count() > 0;

                    if ($notApprovedFui->count() > 0 && !$only81NotApproved) {
                        $list = $notApprovedFui
                            ->filter(function ($item) {
                                return $item->UserId != 81;
                            })
                            ->values()
                            ->map(function ($item, $idx) {
                                $name = $item->getUser->name ?? 'Pengguna terkait';
                                return ($idx + 1) . ". {$name}";
                            })
                            ->implode('<br>');

                        if (!empty($list)) {
                            $errors[] = "Daftar Nama Belum Approve FUI:<br>{$list}";
                        } else {
                            $errors[] = 'Dokumen <b>FUI</b> belum diapprove.';
                        }
                    } else if ($notApprovedFui->count() == 0) {
                        $errors[] = 'Dokumen <b>FUI</b> belum diapprove.';
                    }
                }
                // Cek Approval Disposisi
                if (!$semuaApprovedDisposisi) {
                    // Filter yang belum approve dan bukan user 81
                    $notApprovedDisposisi = $approvalDisposisi->filter(function ($item) {
                        return $item->Status !== 'Approved' && $item->UserId != 81;
                    })->values();

                    if ($notApprovedDisposisi->count() > 0) {
                        $list = $notApprovedDisposisi->map(function ($item, $idx) {
                            $name = $item->getUser->name ?? 'Pengguna terkait';
                            return ($idx + 1) . ". {$name}";
                        })->implode('<br>');
                        $errors[] = "Daftar Nama Belum Approve Disposisi:<br>{$list}";
                    }
                }

                if (count($errors) > 0) {
                    $message = '<b>Mohon lengkapi persetujuan berikut sebelum lanjut:</b><br>' . implode('<br><br>', $errors);
                    return back()->with('error', $message);
                }
            } elseif ($data->Jenis != 1) {
                // dd('PJ26010185');
                $cekDisposisi = $data->getPengajuanItem[0]->getDisposisi;
                // dd($cekDisposisi);
                $cekFui = $data->getPengajuanItem[0]->getFui;
                $cek = $data->getPengajuanItem[0]->getFs;
                // if (!$cek) {
                //     return back()->with('error', 'FS tidak boleh kosong sebelum dapat mengubah status menjadi Siap Presentasi.');
                // }

                if (empty($data->getHtaGpa)) {
                    return back()->with('error', 'HTA / GPA belum diisi. Proses tidak dapat diteruskan.');
                }
                // CekApprovalHta
                $approvalHTA = DokumenApproval::with('getUser', 'getJabatan', 'getDepartemen')
                    ->where('JenisFormId', $data->getHtaGpa->JenisForm)
                    ->where('DokumenId', $data->getHtaGpa->id)
                    ->orderBy('Urutan', 'asc')
                    ->get();

                $semuaApprovedHTA = $approvalHTA->every(function ($item) {
                    return $item->Status === 'Approved';
                });

                if (empty($cekFui)) {
                    return back()->with('error', 'FUI belum diisi. Proses tidak dapat diteruskan.');
                }
                // ApprovalFUI
                $approvalFUI = DokumenApproval::with('getUser', 'getJabatan', 'getDepartemen')
                    ->where('JenisFormId', $cekFui->JenisForm)
                    ->where('DokumenId', $cekFui->id)
                    ->orderBy('Urutan', 'asc')
                    ->get();

                $semuaApprovedFUI = $approvalFUI->every(function ($item) {
                    return $item->Status === 'Approved';
                });

                if (empty($cekDisposisi)) {
                    return back()->with('error', 'Disposisi belum diisi. Proses tidak dapat diteruskan.');
                }

                $approvalDisposisi = DokumenApproval::with('getUser', 'getJabatan', 'getDepartemen')
                    ->where('JenisFormId', $cekDisposisi->JenisForm)
                    ->where('DokumenId', $cekDisposisi->id)
                    ->orderBy('Urutan', 'asc')
                    ->get();

                $semuaApprovedDisposisi = $approvalDisposisi->every(function ($item) {
                    return $item->Status === 'Approved';
                });

                // if (empty($cek)) {
                //     return back()->with('error', 'Disposisi belum diisi. Proses tidak dapat diteruskan.');
                // }
                // // ApprovalFS
                // $approvalFS = DokumenApproval::with('getUser', 'getJabatan', 'getDepartemen')
                //     ->where('JenisFormId', $cek->JenisForm)
                //     ->where('DokumenId', $cek->id)
                //     ->orderBy('Urutan', 'asc')
                //     ->get();

                // $semuaApprovedFS = $approvalFS->every(function ($item) {
                //     return $item->Status === 'Approved';
                // });

                $errors = [];
                // 1. Cek Approval HTA / GPA
                if (!$semuaApprovedHTA) {
                    $notApprovedHta = $approvalHTA->filter(function ($item) {
                        return $item->Status !== 'Approved';
                    })->values();

                    if ($notApprovedHta->count() > 0) {
                        $list = $notApprovedHta->map(function ($item, $idx) {
                            $name = $item->getUser->name ?? 'Pengguna terkait';
                            return ($idx + 1) . ". {$name}";
                        })->implode('<br>');
                        $errors[] = "Daftar Nama Belum Approve HTA / GPA:<br>{$list}";
                    } else {
                        $errors[] = 'Dokumen <b>HTA / GPA</b> belum diapprove.';
                    }
                }

                // 2. Cek Approval FS
                // if (!$semuaApprovedFS) {
                //     $notApprovedFs = $approvalFS->filter(function ($item) {
                //         return $item->Status !== 'Approved';
                //     })->values();

                //     if ($notApprovedFs->count() > 0) {
                //         $list = $notApprovedFs->map(function ($item, $idx) {
                //             $name = $item->getUser->name ?? 'Pengguna terkait';
                //             return ($idx + 1) . ". {$name}";
                //         })->implode('<br>');
                //         $errors[] = "Daftar Nama Belum Approve FS:<br>{$list}";
                //     } else {
                //         $errors[] = "Dokumen <b>FS</b> belum diapprove.";
                //     }
                // }

                // 3. Cek Approval FUI
                if (!$semuaApprovedFUI) {
                    $notApprovedFui = $approvalFUI->filter(function ($item) {
                        return $item->Status !== 'Approved';
                    })->values();
                    $only81NotApproved = $notApprovedFui->every(function ($item) {
                        return $item->UserId == 81;
                    }) && $notApprovedFui->count() > 0;

                    if ($notApprovedFui->count() > 0 && !$only81NotApproved) {
                        $list = $notApprovedFui
                            ->filter(function ($item) {
                                return $item->UserId != 81;
                            })
                            ->values()
                            ->map(function ($item, $idx) {
                                $name = $item->getUser->name ?? 'Pengguna terkait';
                                return ($idx + 1) . ". {$name}";
                            })
                            ->implode('<br>');

                        if (!empty($list)) {
                            $errors[] = "Daftar Nama Belum Approve FUI:<br>{$list}";
                        } else {
                            $errors[] = 'Dokumen <b>FUI</b> belum diapprove.';
                        }
                    } else if ($notApprovedFui->count() == 0) {
                        $errors[] = 'Dokumen <b>FUI</b> belum diapprove.';
                    }
                }
                // Cek Approval Disposisi
                if (!$semuaApprovedDisposisi) {
                    // Filter yang belum approve dan bukan user 81
                    $notApprovedDisposisi = $approvalDisposisi->filter(function ($item) {
                        return $item->Status !== 'Approved' && $item->UserId != 81;
                    })->values();

                    if ($notApprovedDisposisi->count() > 0) {
                        $list = $notApprovedDisposisi->map(function ($item, $idx) {
                            $name = $item->getUser->name ?? 'Pengguna terkait';
                            return ($idx + 1) . ". {$name}";
                        })->implode('<br>');
                        $errors[] = "Daftar Nama Belum Approve Disposisi:<br>{$list}";
                    }
                }

                if (count($errors) > 0) {
                    $message = '<b>Mohon lengkapi persetujuan berikut sebelum lanjut:</b><br>' . implode('<br><br>', $errors);
                    return back()->with('error', $message);
                }
            }
        }
        // dd(23123);
        if (empty($data->getHtaGpa)) {
            return back()->with('error', 'HTA / GPA belum diisi. Proses tidak dapat diteruskan.');
        }
        $approval = DokumenApproval::with('getUser', 'getJabatan', 'getDepartemen')
            ->where('JenisFormId', $data->getHtaGpa->JenisForm)
            ->where('DokumenId', $data->getHtaGpa->id)
            ->orderBy('Urutan', 'asc')
            ->get();

        $semuaApproved = $approval->every(function ($item) {
            return $item->Status === 'Approved';
        });

        if ($semuaApproved) {
            $countVendor = $data->getVendor;
            $namaUser = auth()->user()->name ?? 'User';
            if (count($countVendor) < 2) {
                return redirect()->back()->with('error', "Hai $namaUser, pengajuan tidak bisa diajukan ke CCP. Minimal harus ada 2 vendor dan maksimal 3 vendor ya.");
            }
            if (count($countVendor) > 3) {
                return redirect()->back()->with('error', "Hai $namaUser, pengajuan tidak bisa diajukan ke CCP. Maksimal hanya boleh 3 vendor ya.");
            }
            $pengajuan = PengajuanPembelian::findOrFail($id);
            $pengajuan->Status = $request->Status;
            $pengajuan->Keterangan = $request->Keterangan ?? '';
            $pengajuan->DiajukanOleh = auth()->user()->id;
            $pengajuan->DiajukanPada = now();
            $pengajuan->save();

            if (function_exists('activity')) {
                activity()
                    ->causedBy(auth()->user()->id)
                    ->withProperties(['ip' => request()->ip()])
                    ->log('Mengajukan pengajuan pembelian ke CCP: ' . $pengajuan->KodePengajuan);
            }

            $message = '';
            if ($request->Status == 'Diajukan') {
                $message = 'Terimakasih ' . auth()->user()->name . ', pengajuan Anda berhasil diajukan ke CCP.';
            } elseif ($request->Status == 'Draft') {
                $message = 'Pengajuan berhasil dikembalikan ke draft.';
            } else {
                $message = 'Status pengajuan berhasil diperbarui.';
            }
            return redirect()
                ->route('ajukan.show', encrypt($id))
                ->with('success', $message);
        } else {
            return back()->with('error', 'HTA / GPA, Belum Disetujui. Proses Tidak Dapat Diteruskan.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $pengajuan = PengajuanPembelian::with('getVendor', 'getVendorDetail')->find($id);
        if (!$pengajuan) {
            return response()->json(['status' => 404, 'message' => 'Data tidak ditemukan']);
        }

        foreach ($pengajuan->getVendor as $vendor) {
            $vendor->delete();
        }

        foreach ($pengajuan->getVendorDetail as $vendorDetail) {
            $vendorDetail->delete();
        }

        if (function_exists('activity')) {
            activity()
                ->causedBy(auth()->user()->id)
                ->withProperties(['ip' => request()->ip()])
                ->log('Menghapus master pengajuan: ' . $pengajuan->Nama);
        }

        $pengajuan->delete();

        return response()->json(['status' => 200, 'message' => 'Master perusahaan berhasil dihapus']);
    }
}
