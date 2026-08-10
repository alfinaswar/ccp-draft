<?php

namespace App\Http\Controllers;

use App\Exports\RekomendasiExport;
use App\Mail\NotifApprovalPresentasi;
use App\Models\AktivitasPengajuan;
use App\Models\DokumenApproval;
use App\Models\FeasibilityStudy;
use App\Models\LembarDisposisi;
use App\Models\MasterBarang;
use App\Models\MasterJenisPengajuan;
use App\Models\MasterParameter;
use App\Models\MasterPerusahaan;
use App\Models\MasterVendor;
use App\Models\Negara;
use App\Models\PengajuanItem;
use App\Models\PengajuanPembelian;
use App\Models\PermintaanPembelian;
use App\Models\Rekomendasi;
use App\Models\RekomendasiDetail;
use App\Models\User;
use App\Models\UsulanInvestasi;
use App\Models\UsulanInvestasiDetail;
use App\Services\PdfGeneratorService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\QrCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use setasign\Fpdi\Tcpdf\Fpdi;
use Yajra\DataTables\DataTables;

class RekomendasiController extends Controller
{
    protected $pdfGenerator;

    public function __construct(PdfGeneratorService $pdfGenerator)
    {
        $this->pdfGenerator = $pdfGenerator;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            // dd($request->tanggalPresentasi);
            $hiddenStatuses = ['Selesai', 'Ditolak CEO', 'Disetujui CEO'];
            $data = PengajuanPembelian::with([
                'getPerusahaan',
                'getPermintaan',
                'getJenisPermintaan',
                'getPengajuanItem.getBarang.getMerk'
            ])
                // ->whereIn('Status', [
                //     'Diajukan',
                //     'Selesai Review',
                //     'Menunggu Rekomendasi GH',
                //     'Siap Presentasi',
                //     'Dalam Review'
                // ])
                ->when($request->jenis, fn($q) => $q->where('Jenis', $request->jenis))
                ->when(
                    $request->tanggalPresentasi,
                    fn($q) =>
                        $q->whereDate('TanggalPresentasi', $request->tanggalPresentasi)
                )
                ->when(
                    $request->perusahaan,
                    fn($q) =>
                        $q->where('KodePerusahaan', $request->perusahaan)
                )
                ->when(
                    $request->status,
                    fn($q) =>
                        $q->where('Status', $request->status),
                    function ($q) use ($hiddenStatuses) {
                        // Kecualikan status-status yang dimaksud jika TIDAK difilter
                        $q->whereNotIn('Status', $hiddenStatuses);
                    }
                )
                ->latest();

            return DataTables::of($data)
                ->addIndexColumn()
                ->filter(function ($query) use ($request) {
                    if ($request->has('search') && $request->input('search.value') != '') {
                        $search = $request->input('search.value');
                        // Search pada kolom KodePengajuan, Status, relasi Nama Perusahaan, Nama Barang, dsb
                        $query->where(function ($q) use ($search) {
                            $q
                                ->where('KodePengajuan', 'like', "%{$search}%")
                                ->orWhere('Status', 'like', "%{$search}%")
                                ->orWhereHas('getPerusahaan', function ($sub) use ($search) {
                                    $sub->where('Nama', 'like', "%{$search}%");
                                })
                                ->orWhereHas('getJenisPermintaan', function ($sub) use ($search) {
                                    $sub->where('Nama', 'like', "%{$search}%");
                                })
                                ->orWhereHas('getPengajuanItem.getBarang', function ($sub) use ($search) {
                                    $sub
                                        ->where('Nama', 'like', "%{$search}%")
                                        ->orWhere('Tipe', 'like', "%{$search}%");
                                })
                                ->orWhereHas('getPengajuanItem.getBarang.getMerk', function ($sub) use ($search) {
                                    $sub->where('Nama', 'like', "%{$search}%");
                                });
                        });
                    }
                })
                ->editColumn('Jenis', function ($row) {
                    return optional($row->getJenisPermintaan)->Nama ?? '-';
                })
                ->addColumn('NamaBarang', function ($row) {
                    $namaBarang = '-';
                    $merek = '-';
                    $tipe = '-';
                    if ($row->getPengajuanItem && count($row->getPengajuanItem) > 0) {
                        $item = $row->getPengajuanItem[0];
                        $namaBarang = $item->getBarang->Nama ?? '-';
                        $merek = $item->getBarang->getMerk->Nama ?? '';
                        $tipe = $item->getBarang->Tipe ?? null;
                    }
                    return $namaBarang . ' / ' . $merek . ' / ' . $tipe;
                })
                ->editColumn('KodePerusahaan', function ($row) {
                    return $row->getPerusahaan->Nama ?? '-';
                })
                ->addColumn('action', function ($row) {
                    $id = encrypt($row->id);
                    $idPengajuan = encrypt($row->id);

                    $buttonReview = '
                        <a href="' . route('rekomendasi.show', $id) . '" class="btn btn-sm btn-info" title="Detail">
                            <i class="fa fa-eye"></i> Review
                        </a>
                    ';

                    $idPengajuanItem = $row->getPengajuanItem[0]->id ?? null;
                    $buttonRekap = '
                        <a href="' . route('rekomendasi.rekap', [$idPengajuan, encrypt($idPengajuanItem)]) . '" class="btn btn-sm btn-success" title="Rekap" target="_blank">
                            <i class="fa fa-file-alt"></i> Rekap
                        </a>
                    ';

                    $buttonTracking = '
                        <a href="' . route('rekomendasi.tracking', $id) . '" class="btn btn-sm btn-warning" title="Tracking Progres" target="_blank">
                            <i class="fa fa-route"></i> Tracking
                        </a>
                    ';

                    return $buttonReview . ' ' . $buttonRekap . ' ' . $buttonTracking;
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
                            // Merah muda, icon times
                            return '<span class="badge" style="background-color:#f86c6b;color:#fff;">
                                <i class="fa fa-times"></i> Ditolak
                            </span>';
                        case 'Disetujui CEO':
                            // Hijau, icon user-tie
                            return '<span class="badge" style="background-color:#28a745;color:#fff;">
                                <i class="fa fa-user-tie"></i> Disetujui CEO
                            </span>';
                        case 'Ditolak CEO':
                            // Merah, icon user-times
                            return '<span class="badge" style="background-color:#dc3545;color:#fff;">
                                <i class="fa fa-user-times"></i> Ditolak CEO
                            </span>';
                        default:
                            return '<span class="badge" style="background-color:#f8f9fa;color:#212529;">
                                <i class="fa fa-question-circle"></i> ' . e($row->Status ?? '-') . '
                            </span>';
                    }
                })
                ->addColumn('DiajukanPada', function ($row) {
                    return $row->DiajukanPada
                        ? \Carbon\Carbon::parse($row->DiajukanPada)->translatedFormat('d M Y H:i')
                        : '-';
                })
                ->addColumn('LokasiPenempatan', function ($row) {
                    if (
                        isset($row->getPermintaan) &&
                        isset($row->getPermintaan->getDetail) &&
                        is_array($row->getPermintaan->getDetail) &&
                        isset($row->getPermintaan->getDetail[0]) &&
                        isset($row->getPermintaan->getDetail[0]->RencanaPenempatan)
                    ) {
                        return $row->getPermintaan->getDetail[0]->RencanaPenempatan;
                    } else {
                        return '-';
                    }
                })
                ->addColumn('TanggalPresentasi', function ($row) {
                    if ($row->TanggalPresentasi) {
                        return Carbon::parse($row->TanggalPresentasi)->translatedFormat('d M Y');
                    } else {
                        return '<button type="button"
        class="btn btn-sm btn-primary btn-set-presentasi"
        data-toggle="modal"
        data-target="#modalTanggalPresentasi"
        data-id="' . $row->id . '"
        data-kode="' . e($row->KodePengajuan ?? '') . '">
        <i class="fa fa-calendar-plus"></i> Set Tanggal
    </button>';
                    }
                })
                ->rawColumns(['action', 'Status', 'DiajukanPada', 'NamaBarang', 'TanggalPresentasi', 'LokasiPenempatan'])
                ->make(true);
        }
        $jenis = MasterJenisPengajuan::get();
        $perusahaan = MasterPerusahaan::get();
        return view('rekomendasi-pembelian.index', compact('jenis', 'perusahaan'));
    }

    public function indexSelesai(Request $request)
    {
        if ($request->ajax()) {
            $data = PengajuanPembelian::with([
                'getPerusahaan',
                'getJenisPermintaan',
                'getPengajuanItem.getBarang.getMerk'
            ])
                ->whereIn('Status', ['Selesai', 'Disetujui CEO', 'Ditolak CEO'])
                ->when($request->jenis, function ($query) use ($request) {
                    $query->where('Jenis', $request->jenis);
                })
                ->when($request->tanggalPresentasi, function ($query) use ($request) {
                    $query->whereDate('TanggalPresentasi', $request->tanggalPresentasi);
                })
                ->when($request->perusahaan, function ($query) use ($request) {
                    $query->where('KodePerusahaan', $request->perusahaan);
                })
                ->when($request->status, function ($query) use ($request) {
                    $query->where('Status', $request->status);
                })
                ->orderByDesc('TanggalPresentasi');

            return DataTables::of($data)
                ->addIndexColumn()
                ->filter(function ($query) use ($request) {
                    if ($request->has('search') && $request->input('search.value') != '') {
                        $search = $request->input('search.value');
                        // Search pada kolom KodePengajuan, Status, relasi Nama Perusahaan, Nama Barang, dsb
                        $query->where(function ($q) use ($search) {
                            $q
                                ->where('KodePengajuan', 'like', "%{$search}%")
                                ->orWhere('Status', 'like', "%{$search}%")
                                ->orWhereHas('getPerusahaan', function ($sub) use ($search) {
                                    $sub->where('Nama', 'like', "%{$search}%");
                                })
                                ->orWhereHas('getJenisPermintaan', function ($sub) use ($search) {
                                    $sub->where('Nama', 'like', "%{$search}%");
                                })
                                ->orWhereHas('getPengajuanItem.getBarang', function ($sub) use ($search) {
                                    $sub
                                        ->where('Nama', 'like', "%{$search}%")
                                        ->orWhere('Tipe', 'like', "%{$search}%");
                                })
                                ->orWhereHas('getPengajuanItem.getBarang.getMerk', function ($sub) use ($search) {
                                    $sub->where('Nama', 'like', "%{$search}%");
                                });
                        });
                    }
                })
                ->editColumn('Jenis', function ($row) {
                    return optional($row->getJenisPermintaan)->Nama ?? '-';
                })
                ->addColumn('NamaBarang', function ($row) {
                    $namaBarang = '-';
                    $merek = '-';
                    if ($row->getPengajuanItem && count($row->getPengajuanItem) > 0) {
                        $item = $row->getPengajuanItem[0];
                        $namaBarang = $item->getBarang->Nama ?? '-';
                        $merek = $item->getBarang->getMerk->Nama ?? '';
                        $tipe = $item->getBarang->Tipe ?? '';
                    }
                    return $namaBarang . ' / ' . $merek . ' / ' . $tipe;
                })
                ->editColumn('KodePerusahaan', function ($row) {
                    return $row->getPerusahaan->Nama ?? '-';
                })
                ->addColumn('action', function ($row) {
                    $id = encrypt($row->id);
                    $idPengajuan = encrypt($row->id);

                    $buttonReview = '
                        <a href="' . route('rekomendasi.show', $id) . '" class="btn btn-sm btn-info" title="Detail">
                            <i class="fa fa-eye"></i> Review
                        </a>
                    ';

                    $idPengajuanItem = $row->getPengajuanItem[0]->id ?? null;
                    $buttonRekap = '
                        <a href="' . route('rekomendasi.rekap', [$idPengajuan, encrypt($idPengajuanItem)]) . '" class="btn btn-sm btn-success" title="Rekap" target="_blank">
                            <i class="fa fa-file-alt"></i> Rekap
                        </a>
                    ';

                    $buttonTracking = '
                        <a href="' . route('rekomendasi.tracking', $id) . '" class="btn btn-sm btn-warning" title="Tracking Progres" target="_blank">
                            <i class="fa fa-route"></i> Tracking
                        </a>
                    ';
                    return $buttonReview . ' ' . $buttonRekap . ' ' . $buttonTracking;
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
                            // Merah muda, icon times
                            return '<span class="badge" style="background-color:#f86c6b;color:#fff;">
                                <i class="fa fa-times"></i> Ditolak
                            </span>';
                        case 'Disetujui CEO':
                            // Hijau, icon user-tie
                            return '<span class="badge" style="background-color:#28a745;color:#fff;">
                                <i class="fa fa-user-tie"></i> Disetujui CEO
                            </span>';
                        case 'Ditolak CEO':
                            // Merah, icon user-times
                            return '<span class="badge" style="background-color:#dc3545;color:#fff;">
                                <i class="fa fa-user-times"></i> Ditolak CEO
                            </span>';
                        default:
                            return '<span class="badge" style="background-color:#f8f9fa;color:#212529;">
                                <i class="fa fa-question-circle"></i> ' . e($row->Status ?? '-') . '
                            </span>';
                    }
                })
                ->addColumn('DiajukanPada', function ($row) {
                    return $row->DiajukanPada
                        ? Carbon::parse($row->DiajukanPada)->translatedFormat('d M Y H:i')
                        : '-';
                })
                ->addColumn('LokasiPenempatan', function ($row) {
                    return $row->getPermintaan->getDetail[0]->RencanaPenempatan;
                })
                ->addColumn('TanggalPresentasi', function ($row) {
                    if ($row->TanggalPresentasi) {
                        return Carbon::parse($row->TanggalPresentasi)->translatedFormat('d M Y');
                    } else {
                        // Tampilkan tombol untuk open modal jika TanggalPresentasi null
                        return '<button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#modalTanggalPresentasi" data-id="' . $row->id . '" data-kode="' . e($row->KodePengajuan ?? '') . '">
                                    <i class="fa fa-calendar-plus"></i> Set Tanggal
                                </button>';
                    }
                })
                ->rawColumns(['action', 'Status', 'DiajukanPada', 'NamaBarang', 'TanggalPresentasi', 'LokasiPenempatan'])
                ->make(true);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($idPengajuan, $idPengajuanItem)
    {
        $idPengajuan = decrypt($idPengajuan);
        $idPengajuanItem = decrypt($idPengajuanItem);

        $data = PengajuanPembelian::with([
            'getRekomendasiCcp.getRekomedasiDetail',
            'getVendor.getVendorDetail',
            'getHtaGpa' => function ($query) use ($idPengajuanItem) {
                $query->where('PengajuanItemId', $idPengajuanItem);
            },
            'getVendor.getHtaGpa' => function ($query) use ($idPengajuanItem) {
                $query->where('PengajuanItemId', $idPengajuanItem);
            },
            'getVendor.getRekomendasi' => function ($query) use ($idPengajuanItem) {
                $query->where('PengajuanItemId', $idPengajuanItem);
            },
            'getJenisPermintaan.getForm',
            'getPengajuanItem' => function ($query) use ($idPengajuanItem) {
                $query->where('id', $idPengajuanItem)->with('getBarang.getMerk');
            }
        ])->find($idPengajuan);
        // dd($data);
        $negara = Negara::get();
        $parameter = MasterParameter::get();
        if ($data->Jenis == 1) {
            return view('rekomendasi-pembelian.create', compact('data', 'parameter', 'negara'));
        } else {
            // dd('umum');
            return view('rekomendasi-pembelian.umum.create', compact('data', 'parameter', 'negara'));
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $fileName = null;
        if ($request->hasFile('upload_file')) {
            $file = $request->file('upload_file');
            $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('rekomendasi_file', $fileName, 'public');
        }
        $existingRekomendasi = Rekomendasi::where([
            'IdPengajuan' => $request->rekomendasi[0]['IdPengajuan'],
            'PengajuanItemId' => $request->rekomendasi[0]['PengajuanItemId'],
        ])->first();

        $header = Rekomendasi::updateOrCreate(
            [
                'IdPengajuan' => $request->rekomendasi[0]['IdPengajuan'],
                'PengajuanItemId' => $request->rekomendasi[0]['PengajuanItemId'],
            ],
            [
                'IdPengajuan' => $request->rekomendasi[0]['IdPengajuan'],
                'PengajuanItemId' => $request->rekomendasi[0]['PengajuanItemId'],
                'KodePerusahaan' => $request->rekomendasi[0]['KodePerusahaan'],
                'UserNego' => auth()->user()->id,
                'DisetujuiOleh' => $request->rekomendasi[0]['DisetujuiOleh'] ?? null,
                'DisetujuiPada' => $request->rekomendasi[0]['DisetujuiPada'] ?? null,
                'File' => $fileName !== null
                    ? $fileName
                    : ($existingRekomendasi ? $existingRekomendasi->File : null),
            ]
        );

        if (isset($request->rekomendasi) && is_array($request->rekomendasi)) {
            RekomendasiDetail::where('IdPengajuan', $header->IdPengajuan)
                ->where('PengajuanItemId', $header->PengajuanItemId)
                ->where('IdRekomendasi', $header->id)
                ->forceDelete();

            foreach ($request->rekomendasi as $key => $value) {
                $isi = RekomendasiDetail::create([
                    'IdPengajuan' => $value['IdPengajuan'],
                    'PengajuanItemId' => $value['PengajuanItemId'],
                    'IdRekomendasi' => $header->id,
                    'IdVendor' => $value['IdVendor'] ?? null,
                    'NamaPermintaan' => $value['NamaPermintaan'] ?? null,
                    'HargaAwal' => isset($value['HargaAwal']) ? preg_replace('/\D/', '', $value['HargaAwal']) : null,
                    'HargaNego' => isset($value['HargaNego']) ? preg_replace('/\D/', '', $value['HargaNego']) : null,
                    'Spesifikasi' => $value['Spesifikasi'] ?? null,
                    'NegaraProduksi' => $value['NegaraProduksi'] ?? null,
                    'Garansi' => $value['Garansi'] ?? null,
                    'Teknisi' => $value['Teknisi'] ?? null,
                    'Bmhp' => $value['Bmhp'] ?? null,
                    'SparePart' => $value['SparePart'] ?? null,
                    'BackupUnit' => $value['BackupUnit'] ?? null,
                    'Top' => $value['Top'] ?? null,
                    'Populasi' => $value['Populasi'] ?? 'belum di isi',
                    'TimeLinePekerjaan' => $value['TimeLinePekerjaan'] ?? null,
                    'JumlahPekerja' => $value['JumlahPekerja'] ?? null,
                    'Luasan' => $value['Luasan'] ?? null,
                    'ReviewVendor' => $value['ReviewVendor'] ?? null,
                    'Rekomendasi' => $value['Rekomendasi'] ?? null,
                    'File' => $value['File'] ?? null,
                    'UserNego' => auth()->user()->id,
                    'Keterangan' => $value['Keterangan'] ?? null,
                    'KodePerusahaan' => $request->rekomendasi[0]['KodePerusahaan'],
                ]);
            }
        }
        $pengajuan = PengajuanPembelian::find($request->rekomendasi[0]['IdPengajuan']);
        if ($pengajuan) {
            $pengajuan->Status = 'Dalam Review';
            $pengajuan->save();
        }
        $kodePengajuan = $pengajuan ? $pengajuan->KodePengajuan : null;
        AktivitasPengajuan::create([
            'KodePengajuan' => $kodePengajuan ?? null,
            'Jenis' => 'Rekomendasi',
            'Keterangan' => 'Pembuatan rekomendasi untuk nomor pengajuan ' . $kodePengajuan . ' telah dilakukan (masih sebagai draft)',
            'UserCreate' => auth()->user()->name,
        ]);
        if (function_exists('activity')) {
            $userInput = $request->all();
            $kodePengajuan = $pengajuan ? $pengajuan->KodePengajuan : '-';
            activity()
                ->causedBy(auth()->user()->id)
                ->withProperties([
                    'ip' => request()->ip(),
                    'kode_pengajuan' => $kodePengajuan,
                    'user_input' => $userInput
                ])
                ->log('Simpan rekomendasi: ' . ($request->rekomendasi[0]['IdPengajuan'] ?? '') . ' (Kode: ' . $kodePengajuan . ')');
        }

        return redirect()->back()->with('success', 'Data berhasil disimpan.');
    }

    public function storeSelesai($id)
    {
        $rekomendasi = Rekomendasi::with('getRekomedasiDetail.getNamaVendor')->where('IdPengajuan', $id)->first();
        $listBelumDiisi = [];
        if ($rekomendasi && $rekomendasi->getRekomedasiDetail) {
            foreach ($rekomendasi->getRekomedasiDetail as $value) {
                if ($value->HargaNego === null) {
                    $listBelumDiisi[] = $value->getNamaVendor->Nama;
                }
            }
        }
        if (!empty($listBelumDiisi)) {
            $namaVendors = implode(', ', $listBelumDiisi);
            return redirect()->back()->with('error', 'Maaf anda belum menyelesaikan rekomendasi : ' . $namaVendors . '.');
        }

        $pengajuan = PengajuanPembelian::find($id);
        if ($pengajuan) {
            $pengajuan->Status = 'Menunggu Rekomendasi GH';
            $pengajuan->save();
        }
        $kodePengajuan = $pengajuan ? $pengajuan->KodePengajuan : null;
        AktivitasPengajuan::create([
            'KodePengajuan' => $kodePengajuan ?? null,
            'Jenis' => 'Rekomendasi',
            'Keterangan' => 'Review selesai untuk nomor pengajuan ' . $kodePengajuan . ' telah dilakukan, menunggu rekomendasi GH',
            'UserCreate' => auth()->user()->name,
        ]);
        if (function_exists('activity')) {
            activity()
                ->causedBy(auth()->user()->id)
                ->withProperties([
                    'ip' => request()->ip(),
                    'kode_pengajuan' => $pengajuan ? ($pengajuan->Kode ?? $pengajuan->KodePengajuan ?? '-') : '-',
                ])
                ->log('Review selesai untuk pengajuan: ' . ($pengajuan ? ($pengajuan->id ?? $pengajuan->IdPengajuan ?? '-') : '-'));
        }
        return redirect()->back()->with('success', 'Review Telah Selesai Terimakasih');
    }

    public function batalkan($id)
    {
        // dd($id);
        $id = decrypt($id);
        $pengajuan = PengajuanPembelian::find($id);
        if (!$pengajuan) {
            return redirect()->back()->with('error', 'Pengajuan tidak ditemukan.');
        }
        $pengajuan->Status = 'Dalam Review';
        $pengajuan->save();
        $kodePengajuan = $pengajuan ? $pengajuan->KodePengajuan : null;
        AktivitasPengajuan::create([
            'KodePengajuan' => $kodePengajuan ?? null,
            'Jenis' => 'Rekomendasi',
            'Keterangan' => 'Review untuk nomor pengajuan ' . $kodePengajuan . ' telah dibatalkan dan status dikembalikan ke Dalam Review',
            'UserCreate' => auth()->user()->name,
        ]);
        if (function_exists('activity')) {
            activity('rekomendasi')
                ->causedBy(auth()->user()->id)
                ->withProperties([
                    'ip' => request()->ip(),
                    'kode_pengajuan' => $pengajuan->KodePengajuan ?? '-',
                ])
                ->log('Menembalikan Status Ke Dalam Review: ' . ($pengajuan->KodePengajuan ?? '-'));
        }

        return redirect()->back()->with('success', 'Pengajuan berhasil dibatalkan.');
    }

    public function storeSelesaiAcc($id)
    {
        $rekomendasi = Rekomendasi::with('getRekomedasiDetail.getNamaVendor')->where('IdPengajuan', $id)->first();
        $listBelumDiisi = [];
        if ($rekomendasi && $rekomendasi->getRekomedasiDetail) {
            foreach ($rekomendasi->getRekomedasiDetail as $value) {
                if ($value->Rekomendasi === null) {
                    $listBelumDiisi[] = $value->getNamaVendor->Nama;
                }
            }
        }
        // dd($listBelumDiisi);
        if (!empty($listBelumDiisi)) {
            $namaVendors = implode(', ', $listBelumDiisi);
            return redirect()->back()->with('error', 'Maaf anda belum mengisi pilihan vendor: ' . $namaVendors . '.');
        }

        if ($rekomendasi) {
            $rekomendasi->DisetujuiOleh = auth()->user()->id;
            $rekomendasi->DisetujuiPada = now();
            $rekomendasi->save();
        }
        $pengajuan = PengajuanPembelian::find($id);
        // dd($rekomendasi);
        if ($pengajuan) {
            $pengajuan->Status = 'Selesai Review';
            $pengajuan->save();
        }
        // $this->savePdfToStorage($rekomendasi->IdPengajuan, $rekomendasi->PengajuanItemId);
        $this->pdfGenerator->generateAll($pengajuan->id);

        $kodePengajuan = $pengajuan ? ($pengajuan->KodePengajuan ?? $pengajuan->Nomor ?? $pengajuan->id) : null;
        AktivitasPengajuan::create([
            'KodePengajuan' => $kodePengajuan,
            'Jenis' => 'Rekomendasi',
            'Keterangan' => 'Rekomendasi untuk nomor pengajuan ' . ($kodePengajuan ?? '-') . ' telah selesai dan status menjadi Selesai Review',
            'UserCreate' => auth()->user()->name,
        ]);
        if (function_exists('activity')) {
            activity()
                ->causedBy(auth()->user()->id)
                ->withProperties([
                    'ip' => request()->ip(),
                    'kode_pengajuan' => $pengajuan ? ($pengajuan->KodePengajuan ?? $pengajuan->KodePengajuan ?? '-') : '-',
                ])
                ->log('Rekomendasi selesai untuk pengajuan: ' . ($pengajuan ? ($pengajuan->KodePengajuan ?? $pengajuan->KodePengajuan ?? '-') : '-'));
        }
        return redirect()->back()->with('success', 'Review Telah Selesai Terimakasih');
    }

    public function storeUmum(Request $request)
    {
        // dd($request->all());
        $fileName = null;
        if ($request->hasFile('upload_file')) {
            $file = $request->file('upload_file');
            $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('rekomendasi_file', $fileName, 'public');
        }
        $existingRekomendasi = Rekomendasi::where([
            'IdPengajuan' => $request->rekomendasi[0]['IdPengajuan'],
            'PengajuanItemId' => $request->rekomendasi[0]['PengajuanItemId'],
        ])->first();

        $header = Rekomendasi::updateOrCreate(
            [
                'IdPengajuan' => $request->rekomendasi[0]['IdPengajuan'],
                'PengajuanItemId' => $request->rekomendasi[0]['PengajuanItemId'],
            ],
            [
                'IdPengajuan' => $request->rekomendasi[0]['IdPengajuan'],
                'PengajuanItemId' => $request->rekomendasi[0]['PengajuanItemId'],
                'KodePerusahaan' => $request->rekomendasi[0]['KodePerusahaan'],
                'DisetujuiOleh' => $request->rekomendasi[0]['DisetujuiOleh'] ?? null,
                'DisetujuiPada' => isset($request->rekomendasi[0]['DisetujuiPada']) && $request->rekomendasi[0]['DisetujuiPada']
                    ? Carbon::parse($request->rekomendasi[0]['DisetujuiPada'])->format('Y-m-d H:i:s')
                    : null,
                'UserNego' => auth()->user()->id,
                'File' => $fileName !== null
                    ? $fileName
                    : ($existingRekomendasi ? $existingRekomendasi->File : null),
            ]
        );

        if (isset($request->rekomendasi) && is_array($request->rekomendasi)) {
            // Delete all existing details for this header before inserting new ones
            RekomendasiDetail::where([
                'IdPengajuan' => $request->rekomendasi[0]['IdPengajuan'],
                'PengajuanItemId' => $request->rekomendasi[0]['PengajuanItemId'],
                'IdRekomendasi' => $header->id,
            ])->forceDelete();

            foreach ($request->rekomendasi as $key => $value) {
                RekomendasiDetail::create([
                    'IdPengajuan' => $value['IdPengajuan'],
                    'PengajuanItemId' => $value['PengajuanItemId'],
                    'IdRekomendasi' => $header->id,
                    'IdVendor' => $value['IdVendor'] ?? null,
                    'NamaPermintaan' => $value['NamaPermintaan'] ?? null,
                    'HargaAwal' => isset($value['HargaAwal']) ? preg_replace('/\D/', '', $value['HargaAwal']) : null,
                    'HargaNego' => isset($value['HargaNego']) ? preg_replace('/\D/', '', $value['HargaNego']) : null,
                    'Spesifikasi' => $value['Spesifikasi'] ?? null,
                    'NegaraProduksi' => $value['NegaraProduksi'] ?? null,
                    'Garansi' => $value['Garansi'] ?? null,
                    'Teknisi' => $value['Teknisi'] ?? null,
                    'Bmhp' => $value['Bmhp'] ?? null,
                    'SparePart' => $value['SparePart'] ?? null,
                    'BackupUnit' => $value['BackupUnit'] ?? null,
                    'Top' => $value['Top'] ?? null,
                    'Populasi' => $value['Populasi'] ?? null,
                    'TimeLinePekerjaan' => $value['TimeLinePekerjaan'] ?? null,
                    'JumlahPekerja' => $value['JumlahPekerja'] ?? null,
                    'Luasan' => $value['Luasan'] ?? null,
                    'ReviewVendor' => $value['ReviewVendor'] ?? null,
                    'File' => $value['File'] ?? null,
                    'Rekomendasi' => $value['Rekomendasi'] ?? null,
                    'UserNego' => auth()->user()->id,
                    'Keterangan' => $value['Keterangan'] ?? null,
                    'KodePerusahaan' => $request->rekomendasi[0]['KodePerusahaan'],
                ]);
            }
        }
        $pengajuan = PengajuanPembelian::find($request->rekomendasi[0]['IdPengajuan']);
        if ($pengajuan) {
            $pengajuan->Status = 'Dalam Review';
            $pengajuan->save();
        }
        $kodePengajuan = $pengajuan ? ($pengajuan->KodePengajuan ?? $pengajuan->Kode ?? null) : null;
        AktivitasPengajuan::create([
            'KodePengajuan' => $kodePengajuan,
            'Jenis' => 'Rekomendasi',
            'Keterangan' => 'Pembuatan rekomendasi untuk nomor pengajuan ' . ($kodePengajuan ?? '-') . ' telah dilakukan (masih sebagai draft)',
            'UserCreate' => auth()->user()->name,
        ]);
        if (function_exists('activity')) {
            $kodePengajuan = $pengajuan ? $pengajuan->Kode : '-';
            activity()
                ->causedBy(auth()->user()->id)
                ->withProperties([
                    'ip' => request()->ip(),
                    'kode_pengajuan' => $kodePengajuan
                ])
                ->log('Simpan rekomendasi: ' . ($request->rekomendasi[0]['IdPengajuan'] ?? '') . ' (Kode: ' . $kodePengajuan . ')');
        }
        return redirect()->back()->with('success', 'Data berhasil disimpan.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $id = decrypt($id);
        $data = PengajuanPembelian::with('getVendor.getVendorDetail', 'getJenisPermintaan', 'getPengajuanItem.getBarang', 'getPengajuanItem.getHtaGpa', 'getPengajuanItem.getRekomendasi', 'getPengajuanItem.getFui')->find($id);
        $vendor = MasterVendor::orderBy('Nama', 'asc')->get();
        $masterbarang = MasterBarang::get();
        return view('rekomendasi-pembelian.show', compact('data', 'vendor', 'masterbarang'));
    }

    public function rekap($idPengajuan)
    {
        $idPengajuan = decrypt($idPengajuan);
        $folder = "pengajuan-{$idPengajuan}";
        $filename = "fui-{$idPengajuan}.pdf";
        $relativePath = "rekap-file/{$folder}/{$filename}";
        $storagePath = storage_path("app/public/{$relativePath}");

        $results = $this->pdfGenerator->generateAll($idPengajuan);

        if (!file_exists($storagePath)) {
            $filenameFs = "fs-{$idPengajuan}.pdf";
            $relativePathFs = "rekap-file/{$folder}/{$filenameFs}";
            $storagePathFs = storage_path("app/public/{$relativePathFs}");

            if (!file_exists($storagePathFs)) {
                abort(404, 'File tidak ditemukan.');
            }

            return response()->file($storagePathFs, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . $filenameFs . '"',
            ]);
        }

        return response()->file($storagePath, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
        ]);
    }

    // public function rekap2($idPengajuan, $idPengajuanItem)
    // {
    //     $idPengajuan = decrypt($idPengajuan);
    //     $idPengajuanItem = decrypt($idPengajuanItem);
    //     $caripermintaan = PengajuanPembelian::find($idPengajuan);
    //     // dd($idPengajuan);

    //     // rekomendasi
    //     $rekomendasi = Rekomendasi::with('getRekomedasiDetail.getPerusahaan', 'getRekomedasiDetail.getBarang', 'getRekomedasiDetail.getNegara')
    //         ->where('PengajuanItemId', $idPengajuanItem)
    //         ->whereNotNull('DisetujuiOleh')
    //         ->first();

    //     if (!$rekomendasi) {
    //         return redirect()->back()->with('warning', 'Maaf, Rekomendasi Pembelian Masih Dalam Proses Menunggu Rekomendasi GH');
    //     }

    //     if ($rekomendasi->UserNego !== null) {
    //         $qrCode = QrCode::create($rekomendasi->id)
    //             ->setSize(300)
    //             ->setMargin(10);

    //         $writer = new PngWriter();
    //         $result = $writer->write($qrCode);

    //         $rekomendasi->qrCodeNego = base64_encode($result->getString());
    //     }

    //     if ($rekomendasi->DisetujuiOleh !== null) {
    //         $qrCode = QrCode::create($rekomendasi->id ?? '')
    //             ->setSize(300)
    //             ->setMargin(10);

    //         $writer = new PngWriter();
    //         $result = $writer->write($qrCode);

    //         $rekomendasi->qrCodeApprove = base64_encode($result->getString());
    //     }
    //     // dd($rekomendasi);
    //     // DISPOSISI
    //     $lembarDisposisi = LembarDisposisi::with(['getDetail', 'getBarang'])
    //         ->where('IdPengajuan', $idPengajuan)
    //         ->where('PengajuanItemId', $idPengajuanItem)
    //         ->first();

    //     if (!$lembarDisposisi) {
    //         return redirect()->back()->with('error', 'Maaf, Lembar Disposisi Belum Dibuat / Disetujui');
    //     }

    //     $approval = DokumenApproval::with('getUser', 'getJabatan', 'getDepartemen')
    //         ->where('JenisFormId', $lembarDisposisi->JenisForm)
    //         ->where('DokumenId', $lembarDisposisi->id)
    //         ->orderBy('Urutan', 'asc')
    //         ->get();
    //     // dd($approval);
    //     foreach ($approval as $item) {
    //         if ($item->Status == 'Approved') {
    //             $qrCode = QrCode::create(route('approval.validasi', $item->ApprovalToken))
    //                 ->setSize(300)
    //                 ->setMargin(10);

    //             $writer = new PngWriter();
    //             $result = $writer->write($qrCode);

    //             $item->qrCode = base64_encode($result->getString());
    //         }
    //     }
    //     $data = [
    //         'lembarDisposisi' => $lembarDisposisi,
    //         'namaBarang' => $lembarDisposisi->getBarang->Nama,
    //         'harga' => $lembarDisposisi->Harga,
    //         'rencanaVendor' => $lembarDisposisi->getVendor->Nama,
    //         'tujuanPenempatan' => $lembarDisposisi->TujuanPenempatan,
    //         'formPermintaan' => $lembarDisposisi->FormPermintaanUser,
    //         'approval' => $approval,
    //     ];
    //     // HTA
    //     $dataHta = PengajuanPembelian::with([
    //         'getVendor.getVendorDetail',
    //         'getHtaGpa.getDetailHta' => function ($query) use ($idPengajuanItem) {
    //             $query->where('PengajuanItemId', $idPengajuanItem);
    //         },
    //         'getVendor.getHtaGpa' => function ($query) use ($idPengajuanItem) {
    //             $query->where('PengajuanItemId', $idPengajuanItem);
    //         },
    //         'getJenisPermintaan.getForm',
    //         'getHtaGpa.getPenilai1',
    //         'getHtaGpa.getPenilai2',
    //         'getHtaGpa.getPenilai3',
    //         'getHtaGpa.getPenilai4',
    //         'getHtaGpa.getPenilai5',
    //         'getHtaGpa.getPenilai',
    //         'getPengajuanItem' => function ($query) use ($idPengajuanItem) {
    //             $query->where('id', $idPengajuanItem)->with('getBarang.getMerk');
    //         }
    //     ])->find($idPengajuan);
    //     // dd($dataHta);
    //     $approvalHta = DokumenApproval::with('getUser', 'getJabatan', 'getDepartemen')
    //         ->where('JenisFormId', $dataHta->getHtaGpa->JenisForm)
    //         ->where('DokumenId', $dataHta->getHtaGpa->id)
    //         ->orderBy('Urutan', 'asc')
    //         ->get();

    //     foreach ($approvalHta as $itemHta) {
    //         if ($itemHta->Status == 'Approved') {
    //             $qrCode = QrCode::create(route('approval.validasi', $itemHta->ApprovalToken ?? '0'))
    //                 ->setSize(300)
    //                 ->setMargin(10);

    //             $writer = new PngWriter();
    //             $result = $writer->write($qrCode);

    //             $itemHta->qrCode = base64_encode($result->getString());
    //         }
    //     }

    //     $parameter = MasterParameter::get();
    //     // FUI
    //     $usulan = UsulanInvestasi::with('getFuiDetail.getVendor', 'getBarang', 'getVendor', 'getAccDirektur', 'getAccKadiv', 'getDepartemen', 'getDepartemen2', 'getNamaForm')
    //         ->where('IdPengajuan', $idPengajuan)
    //         ->where('PengajuanItemId', $idPengajuanItem)
    //         ->first();
    //     $dataRekom = Rekomendasi::with('getRekomedasiDetail.getBarang', 'getRekomedasiDetail.getNamaVendor')->where('IdPengajuan', $idPengajuan)->first();

    //     // dd($usulan);
    //     $VendorAcc = Rekomendasi::with([
    //         'getRekomedasiDetail' => function ($query2) {
    //             $query2->where('Rekomendasi', 1);
    //         },
    //         'getRekomedasiDetail.getNamaVendor'
    //     ])
    //         ->where('PengajuanItemId', $idPengajuanItem)
    //         ->first();
    //     $approval2 = DokumenApproval::with('getUser', 'getJabatan', 'getDepartemen')
    //         ->where('JenisFormId', $usulan->JenisForm)
    //         ->where('DokumenId', $usulan->id)
    //         ->orderBy('Urutan', 'asc')
    //         ->get();
    //     foreach ($approval2 as $item) {
    //         if ($item->Status == 'Approved') {
    //             $qrCode = QrCode::create(route('approval.validasi', $item->ApprovalToken))
    //                 ->setSize(300)
    //                 ->setMargin(10);

    //             $writer = new PngWriter();
    //             $result = $writer->write($qrCode);

    //             $item->qrCode = base64_encode($result->getString());
    //         }
    //     }
    //     $Acc = $VendorAcc->getRekomedasiDetail[0]->IdVendor;
    //     // dd($Acc);
    //     $NamaBarangAcc = $VendorAcc->getRekomedasiDetail[0]->NamaPermintaan;
    //     $data2 = PengajuanPembelian::with([
    //         'getVendor' => function ($query2) use ($Acc) {
    //             $query2->where('NamaVendor', $Acc);
    //         },
    //         'getVendor.getVendorDetail' => function ($query) use ($NamaBarangAcc) {
    //             $query->where('NamaBarang', $NamaBarangAcc);
    //         },
    //         'getRekomendasi' => function ($query) {
    //             $query->with([
    //                 'getRekomedasiDetail' => function ($query2) {
    //                     $query2->where('Rekomendasi', 1);
    //                 }
    //             ]);
    //         }
    //     ])->find($idPengajuan);
    //     // END FUI
    //     // FS
    //     $datafs = FeasibilityStudy::with('getFsDetail', 'getBarang')
    //         ->where('IdPengajuan', $idPengajuan)
    //         ->where('PengajuanItemId', $idPengajuanItem)
    //         ->first();
    //     // dd($datafs);
    //     $approvalfS = collect();  // Default to empty collection

    //     if (!is_null($datafs)) {
    //         $approvalfS = DokumenApproval::with('getUser', 'getJabatan', 'getDepartemen')
    //             ->where('JenisFormId', $datafs->JenisForm)
    //             ->where('DokumenId', $datafs->id)
    //             ->orderBy('Urutan', 'asc')
    //             ->get();

    //         // Generate QR code untuk setiap approval yang approved
    //         foreach ($approvalfS as $itemFS) {
    //             if ($itemFS->Status == 'Approved') {
    //                 $qrCode = QrCode::create(route('approval.validasi', $itemFS->ApprovalToken))
    //                     ->setSize(300)
    //                     ->setMargin(10);

    //                 $writer = new PngWriter();
    //                 $result = $writer->write($qrCode);

    //                 $itemFS->qrCode = base64_encode($result->getString());
    //             }
    //         }
    //     }
    //     // END FS
    //     // PERMINTAAN
    //     $permintaan = PermintaanPembelian::with([
    //         'getDetail.getBarang.getMerk',
    //         'getDiajukanOleh',
    //         'getDetail.getBarang.getSatuan'
    //     ])->find($caripermintaan->IdPermintaan);
    //     // dd($permintaan);
    //     $approval3 = DokumenApproval::with('getUser', 'getJabatan', 'getDepartemen')
    //         ->where('JenisFormId', $permintaan->JenisForm)
    //         ->where('DokumenId', $permintaan->id)
    //         ->orderBy('Urutan', 'asc')
    //         ->get();

    //     // Generate QR code untuk setiap approval
    //     foreach ($approval3 as $item) {
    //         if ($item->Status == 'Approved') {
    //             $qrCode = QrCode::create(route('approval.validasi', $item->ApprovalToken))
    //                 ->setSize(80)
    //                 ->setMargin(10);

    //             $writer = new PngWriter();
    //             $result = $writer->write($qrCode);

    //             $item->qrCode = base64_encode($result->getString());
    //         }
    //     }
    //     // dd($caripermintaan);
    //     $pdf = Pdf::loadView('rekomendasi-pembelian.rekap-pdf', [
    //         'rekomendasi' => $rekomendasi,
    //         'data' => $data,
    //         'data2' => $data2,
    //         'usulan' => $usulan,
    //         'approval' => $approval,
    //         'VendorAcc' => $VendorAcc,
    //         'approval2' => $approval2,
    //         'permintaan' => $permintaan,
    //         'approval3' => $approval3,
    //         'dataHta' => $dataHta,
    //         'approvalHta' => $approvalHta,
    //         'parameter' => $parameter,
    //         'datafs' => $datafs,
    //         'approvalfS' => $approvalfS,
    //         'dataRekom' => $dataRekom,
    //     ]);
    //     $pdf->setOptions([
    //         'isHtml5ParserEnabled' => true,
    //         'isRemoteEnabled' => true,
    //     ]);
    //     // dd($caripermintaan);
    //     if ($caripermintaan->Jenis != 1) {
    //         $hasAttachment = !empty($rekomendasi->File) &&
    //             Storage::disk('public')->exists('rekomendasi_file/' . $rekomendasi->File);
    //         if (!$hasAttachment) {
    //             return $pdf->stream('rekap_pengajuan_' . $idPengajuan . '_' . $idPengajuanItem . '.pdf');
    //         }

    //         // Double check if file exists on disk
    //         $storedFilePath = Storage::disk('public')->path('rekomendasi_file/' . $rekomendasi->File);
    //         if (!file_exists($storedFilePath)) {
    //             return $pdf->stream('rekap_pengajuan_' . $idPengajuan . '_' . $idPengajuanItem . '.pdf');
    //         }

    //         // Ensure merged directory exists
    //         $mergedDir = storage_path('app/public/rekomendasi_file/merged');
    //         if (!file_exists($mergedDir)) {
    //             mkdir($mergedDir, 0755, true);
    //         }

    //         // Gunakan nama file konsisten berdasarkan ID
    //         $mergedFileName = 'rekap_merged_' . $idPengajuan . '_' . $idPengajuanItem . '.pdf';
    //         $mergedFullPath = $mergedDir . DIRECTORY_SEPARATOR . $mergedFileName;

    //         // HAPUS file merged lama jika ada (untuk selalu generate yang baru)
    //         if (file_exists($mergedFullPath)) {
    //             unlink($mergedFullPath);
    //         }

    //         // Path for saving generated PDF
    //         $generatedFileName = 'generated_' . time() . '_' . uniqid() . '.pdf';
    //         $generatedFullPath = $mergedDir . DIRECTORY_SEPARATOR . $generatedFileName;

    //         // Save generated PDF
    //         $pdf->save($generatedFullPath);

    //         // Merge PDFs
    //         $fpdi = new Fpdi();

    //         // 1. Add generated PDF pages
    //         $pageCount = $fpdi->setSourceFile($generatedFullPath);
    //         for ($i = 1; $i <= $pageCount; $i++) {
    //             $template = $fpdi->importPage($i);
    //             $size = $fpdi->getTemplateSize($template);
    //             $fpdi->AddPage($size['orientation'], [$size['width'], $size['height']]);
    //             $fpdi->useTemplate($template);
    //         }

    //         // 2. Add uploaded attachment PDF pages
    //         $attachCount = $fpdi->setSourceFile($storedFilePath);
    //         for ($i = 1; $i <= $attachCount; $i++) {
    //             $template = $fpdi->importPage($i);
    //             $size = $fpdi->getTemplateSize($template);
    //             $fpdi->AddPage($size['orientation'], [$size['width'], $size['height']]);
    //             $fpdi->useTemplate($template);
    //         }

    //         // 3. Output merged PDF
    //         // Normalize path untuk TCPDF (gunakan forward slash)
    //         $mergedFullPathNormalized = str_replace('\\', '/', $mergedFullPath);

    //         $fpdi->Output($mergedFullPathNormalized, 'F');

    //         // Clean up generated PDF after merging
    //         if (file_exists($generatedFullPath)) {
    //             unlink($generatedFullPath);
    //         }

    //         // Stream the merged PDF
    //         return response()->file($mergedFullPath, [
    //             'Content-Type' => 'application/pdf',
    //             'Content-Disposition' => 'inline; filename="rekap_pengajuan_' . $idPengajuan . '_' . $idPengajuanItem . '.pdf"'
    //         ]);
    //     }

    //     // Jika jenis = 1, stream PDF biasa tanpa lampiran
    //     return $pdf->stream('rekap_pengajuan_' . $idPengajuan . '_' . $idPengajuanItem . '.pdf');
    // }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Rekomendasi $rekomendasi)
    {
        //
    }

    public function Cetak($idPengajuan, $idPengajuanItem)
    {
        $idPengajuan = decrypt($idPengajuan);
        $idPengajuanItem = decrypt($idPengajuanItem);

        $rekomendasi = Rekomendasi::with(
            'getRekomedasiDetail.getPerusahaan',
            'getRekomedasiDetail.getBarang',
            'getRekomedasiDetail.getNegara',
            'getUserNego',
            'getDisetujuiOleh',
            'getPerusahaan',
            'getPengajuan.getVendor.getVendorDetail'
        )
            ->where('PengajuanItemId', $idPengajuanItem)
            ->whereNotNull('DisetujuiOleh')
            ->first();
        // dd($rekomendasi->getPengajuan->getVendor);
        if (is_null($rekomendasi)) {
            return redirect()->back()->with('error', 'Rekomendasi belum diapprove oleh GH Procurement');
        }
        // dd($rekomendasi);
        $jenis = PengajuanPembelian::find($rekomendasi->IdPengajuan);
        // dd($jenis);
        // Generate QR Codes
        if ($rekomendasi->UserNego !== null) {
            $qrCode = QrCode::create($rekomendasi->id)
                ->setSize(300)
                ->setMargin(10);
            $writer = new PngWriter();
            $result = $writer->write($qrCode);
            $rekomendasi->qrCodeNego = base64_encode($result->getString());
        }

        if ($rekomendasi->DisetujuiOleh !== null) {
            $qrCode = QrCode::create($rekomendasi->id ?? '')
                ->setSize(300)
                ->setMargin(10);
            $writer = new PngWriter();
            $result = $writer->write($qrCode);
            $rekomendasi->qrCodeApprove = base64_encode($result->getString());
        }

        // Generate PDF from view
        if ($jenis->Jenis == 1) {
            $pdf = Pdf::loadView('rekomendasi-pembelian.cetak-review', [
                'rekomendasi' => $rekomendasi,
            ]);
            $pdf->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
            ]);
            return $pdf->stream('rekomendasi.pdf');
        } else {
            // dd('umum');
            $pdf = Pdf::loadView('rekomendasi-pembelian.cetak-review-umum', [
                'rekomendasi' => $rekomendasi,
            ]);
            $pdf->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
            ]);

            // Check if File exists in storage and is not empty
            $hasAttachment = !empty($rekomendasi->File) &&
                Storage::disk('public')->exists('rekomendasi_file/' . $rekomendasi->File);

            // If no file to merge, just stream PDF
            if (!$hasAttachment) {
                return $pdf->stream('cetak_rekomendasi_' . $idPengajuan . '_' . $idPengajuanItem . '.pdf');
            }

            // Double check if file exists on disk
            $storedFilePath = Storage::disk('public')->path('rekomendasi_file/' . $rekomendasi->File);
            if (!file_exists($storedFilePath)) {
                return $pdf->stream('cetak_rekomendasi_' . $idPengajuan . '_' . $idPengajuanItem . '.pdf');
            }

            // Ensure merged directory exists
            $mergedDir = storage_path('app/public/rekomendasi_file/merged');
            if (!file_exists($mergedDir)) {
                mkdir($mergedDir, 0755, true);
            }

            // Gunakan nama file konsisten berdasarkan ID
            $mergedFileName = 'merged_' . $idPengajuan . '_' . $idPengajuanItem . '.pdf';
            $mergedFullPath = $mergedDir . DIRECTORY_SEPARATOR . $mergedFileName;

            // Cek apakah file merged sudah ada
            if (file_exists($mergedFullPath)) {
                // Langsung stream file yang sudah ada
                return response()->file($mergedFullPath, [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'inline; filename="cetak_rekomendasi_' . $idPengajuan . '_' . $idPengajuanItem . '.pdf"'
                ]);
            }

            // Jika belum ada, buat baru
            // Path for saving generated PDF
            $generatedFileName = 'generated_' . time() . '_' . uniqid() . '.pdf';
            $generatedFullPath = $mergedDir . DIRECTORY_SEPARATOR . $generatedFileName;

            // Save generated PDF
            $pdf->save($generatedFullPath);

            // Merge PDFs
            $fpdi = new Fpdi();

            // 1. Add generated PDF pages
            $pageCount = $fpdi->setSourceFile($generatedFullPath);
            for ($i = 1; $i <= $pageCount; $i++) {
                $template = $fpdi->importPage($i);
                $size = $fpdi->getTemplateSize($template);
                $fpdi->AddPage($size['orientation'], [$size['width'], $size['height']]);
                $fpdi->useTemplate($template);
            }

            // 2. Add uploaded attachment PDF pages
            $attachCount = $fpdi->setSourceFile($storedFilePath);
            for ($i = 1; $i <= $attachCount; $i++) {
                $template = $fpdi->importPage($i);
                $size = $fpdi->getTemplateSize($template);
                $fpdi->AddPage($size['orientation'], [$size['width'], $size['height']]);
                $fpdi->useTemplate($template);
            }

            // 3. Output merged PDF
            // Normalize path untuk TCPDF (gunakan forward slash)
            $mergedFullPathNormalized = str_replace('\\', '/', $mergedFullPath);

            $fpdi->Output($mergedFullPathNormalized, 'F');

            // Clean up generated PDF after merging
            if (file_exists($generatedFullPath)) {
                unlink($generatedFullPath);
            }

            // Stream the merged PDF
            return response()->file($mergedFullPath, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="cetak_rekomendasi_' . $idPengajuan . '_' . $idPengajuanItem . '.pdf"'
            ]);
        }
    }

    public function detail($idPengajuan, $idPengajuanItem)
    {
        $idPengajuan = decrypt($idPengajuan);
        $idPengajuanItem = decrypt($idPengajuanItem);
        $data = PengajuanPembelian::with([
            'getVendor.getVendorDetail',
            'getVendor.getHtaGpa' => function ($query) use ($idPengajuanItem) {
                $query->where('PengajuanItemId', $idPengajuanItem);
            },
            'getVendor.getRekomendasi' => function ($query) use ($idPengajuanItem) {
                $query->where('PengajuanItemId', $idPengajuanItem);
            },
            'getJenisPermintaan.getForm',
            'getPengajuanItem' => function ($query) use ($idPengajuanItem) {
                $query->where('id', $idPengajuanItem)->with('getBarang.getMerk', 'getRekomendasi.getUserNego');
            }
        ])->find($idPengajuan);

        $rekomendasi = Rekomendasi::with(
            'getRekomedasiDetail.getPerusahaan',
            'getRekomedasiDetail.getBarang',
            'getRekomedasiDetail.getNegara',
            'getUserNego',
            'getDisetujuiOleh'
        )
            ->where('PengajuanItemId', $idPengajuanItem)
            ->first();

        if (!empty($rekomendasi) && $rekomendasi->UserNego !== null) {
            $qrCode = QrCode::create($rekomendasi->id)
                ->setSize(150)
                ->setMargin(5);
            $writer = new PngWriter();
            $result = $writer->write($qrCode);
            $rekomendasi->qrCodeNego = base64_encode($result->getString());
        }

        if (!empty($rekomendasi) && $rekomendasi->DisetujuiOleh !== null) {
            $qrCode = QrCode::create($rekomendasi->id ?? '')
                ->setSize(150)
                ->setMargin(5);
            $writer = new PngWriter();
            $result = $writer->write($qrCode);
            $rekomendasi->qrCodeApprove = base64_encode($result->getString());
        }
        // dd($rekomendasi);
        $negara = Negara::get();
        $parameter = MasterParameter::get();
        return view('rekomendasi-pembelian.acc-rekomendasi', compact('data', 'parameter', 'negara', 'rekomendasi'));
    }

    public function simpanNotes(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'IdPengajuan' => 'required|integer',
            'Catatan' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $rekomendasi = Rekomendasi::where('IdPengajuan', $request->IdPengajuan)->first();
            if ($rekomendasi) {
                $rekomendasi->Catatan = $request->Catatan;
                $rekomendasi->save();
            }

            return response()->json([
                'success' => true,
                'message' => 'Catatan berhasil disimpan.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Rekomendasi $rekomendasi)
    {
        //
    }

    public function UpdateRekomendasi(Request $request)
    {
        // dd($request->all());
        $header = Rekomendasi::updateOrCreate(
            [
                'IdPengajuan' => $request->rekomendasi[0]['IdPengajuan'],
                'PengajuanItemId' => $request->rekomendasi[0]['PengajuanItemId'],
            ],
            [
                'IdPengajuan' => $request->rekomendasi[0]['IdPengajuan'],
                'PengajuanItemId' => $request->rekomendasi[0]['PengajuanItemId'],
                'VendorAcc' => $request->rekomendasi[0]['RekomendasiSelect'],
                'Presentasi' => $request->Presentasi ?? null,
                'TanggalPresentasi' => $request->TanggalPresentasi ?? null,
            ]
        );

        if (isset($request->rekomendasi) && is_array($request->rekomendasi)) {
            RekomendasiDetail::where('IdPengajuan', $header->IdPengajuan)
                ->where('PengajuanItemId', $header->PengajuanItemId)
                ->where('IdRekomendasi', $header->id)
                ->forceDelete();

            foreach ($request->rekomendasi as $key => $value) {
                $isi = RekomendasiDetail::create([
                    'IdPengajuan' => $value['IdPengajuan'],
                    'PengajuanItemId' => $value['PengajuanItemId'],
                    'IdRekomendasi' => $header->id,
                    'IdVendor' => $value['IdVendor'] ?? null,
                    'NamaPermintaan' => $value['NamaPermintaan'] ?? null,
                    'HargaAwal' => isset($value['HargaAwal']) ? preg_replace('/\D/', '', $value['HargaAwal']) : null,
                    'HargaNego' => isset($value['HargaNego']) ? preg_replace('/\D/', '', $value['HargaNego']) : null,
                    'Spesifikasi' => $value['Spesifikasi'] ?? null,
                    'NegaraProduksi' => $value['NegaraProduksi'] ?? null,
                    'Garansi' => $value['Garansi'] ?? null,
                    'Teknisi' => $value['Teknisi'] ?? null,
                    'Bmhp' => $value['Bmhp'] ?? null,
                    'SparePart' => $value['SparePart'] ?? null,
                    'BackupUnit' => $value['BackupUnit'] ?? null,
                    'Top' => $value['Top'] ?? null,
                    'Populasi' => $value['Populasi'] ?? 'Belum Isi',
                    'TimeLinePekerjaan' => $value['TimeLinePekerjaan'] ?? null,
                    'JumlahPekerja' => $value['JumlahPekerja'] ?? null,
                    'Luasan' => $value['Luasan'] ?? null,
                    'ReviewVendor' => $value['ReviewVendor'] ?? null,
                    'File' => $value['File'] ?? null,
                    'UserNego' => auth()->user()->id,
                    'Keterangan' => $value['Keterangan'] ?? null,
                    'Rekomendasi' => $value['RekomendasiSelect'] ?? null,
                    'KodePerusahaan' => $request->rekomendasi[0]['KodePerusahaan'],
                ]);

                if (($value['RekomendasiSelect'] ?? null) == 1) {
                    $pengajuanItem = PengajuanItem::find($value['PengajuanItemId']);
                    if ($pengajuanItem) {
                        $pengajuanItem->VendorAcc = $request->rekomendasi[0]['RekomendasiSelect'];
                        $pengajuanItem->HargaNegoAcc = preg_replace('/\D/', '', $value['HargaNego']);
                        $pengajuanItem->save();
                    }
                }
            }
        }
        $kodePengajuan = $request->rekomendasi[0]['IdPengajuan'] ?? null;
        $pengajuan = null;
        if ($kodePengajuan) {
            $pengajuan = PengajuanPembelian::find($kodePengajuan);
            $kodePengajuan = $pengajuan ? $pengajuan->KodePengajuan : $kodePengajuan;
        }

        AktivitasPengajuan::create([
            'KodePengajuan' => $kodePengajuan ?? null,
            'Jenis' => 'Rekomendasi',
            'Keterangan' => 'Menentukan rekomendasi vendor untuk nomor pengajuan ' . ($kodePengajuan ?? '-'),
            'UserCreate' => auth()->user()->name,
        ]);
        if (function_exists('activity')) {
            activity()
                ->causedBy(auth()->user()->id)
                ->withProperties([
                    'ip' => request()->ip(),
                    'pengajuan_id' => $request->rekomendasi[0]['IdPengajuan'] ?? null,
                    'pengajuan_item_id' => $request->rekomendasi[0]['PengajuanItemId'] ?? null,
                    'kode_perusahaan' => $request->rekomendasi[0]['KodePerusahaan'] ?? null,
                    'catatan' => 'Menentukan rekomendasi vendor',
                    'vendor_acc' => $request->rekomendasi[0]['RekomendasiSelect'] ?? null,
                ])
                ->log('Menentukan rekomendasi pada pengajuan: ' . ($request->rekomendasi[0]['IdPengajuan'] ?? '-'));
        }

        // hanya isi jika rekomendasi 1

        return redirect()->back()->with('success', 'Anda sudah menentukan rekomendasi.');
    }

    public function updateTanggalPresentasi(Request $request, $id)
    {
        $request->validate([
            'TanggalPresentasi' => 'required|date'
        ]);

        $rekomendasi = PengajuanPembelian::find($id);
        // dd($rekomendasi);
        // dd($rekomendasi);
        if (!$rekomendasi) {
            return redirect()->back()->with('error', 'Rekomendasi tidak ditemukan.');
        }
        // ========== UPDATE REKOMENDASI ==========
        $rekomendasi->TanggalPresentasi = $request->TanggalPresentasi;
        $rekomendasi->Status = 'Selesai';
        $rekomendasi->save();

        $cekdata = Rekomendasi::where('IdPengajuan', $rekomendasi->id)->first();
        // UNTUK LAMPIRAN EMAIL
        $fui = UsulanInvestasi::where('IdPengajuan', $rekomendasi->id)->first();

        // Cari DokumenApproval dengan status 'Pending' dan Urutan terkecil (prioritas terkecil)
        $approvalFUITesting = DokumenApproval::with('getUser', 'getJabatan', 'getDepartemen')
            ->where('JenisFormId', $fui->JenisForm)
            ->where('DokumenId', $fui->id)
            // ->where('Status', 'Pending')
            ->orderBy('Urutan', 'asc')
            ->first();
        $this->pdfGenerator->generateAll($rekomendasi->id);
        // ========== KIRIM EMAIL ==========
        if ($approvalFUITesting) {
            Mail::to($approvalFUITesting->Email)
                ->bcc(env('MAIL_DEV_BCC'))
                ->send(new NotifApprovalPresentasi(
                    $fui,
                    $rekomendasi,
                    $approvalFUITesting
                ));
        }

        if ($rekomendasi) {
            $rekomendasi->Status = 'Selesai';
            $rekomendasi->save();
        }

        $cariPengajuan = PengajuanPembelian::find($rekomendasi->IdPengajuan ?? null);
        $kodePengajuan = $cariPengajuan ? $cariPengajuan->KodePengajuan : ($rekomendasi->KodePengajuan ?? null);
        AktivitasPengajuan::create([
            'KodePengajuan' => $kodePengajuan ?? null,
            'Jenis' => 'Rekomendasi',
            'Keterangan' => 'Tanggal presentasi untuk nomor pengajuan ' . ($kodePengajuan ?? '-') . ' telah diperbarui dan telah dikirim ke urutan 1 (Direktur)',

            'UserCreate' => auth()->user()->name,
        ]);
        return redirect()->back()->with('success', 'Tanggal presentasi berhasil diperbarui dan email notifikasi telah dikirim.');
    }

    public function approveBulk($tokens, Request $request)
    {
        $KodePengajuan = $request->kode_pengajuan;
        // dd($KodePengajuan);
        $tokenArr = explode(',', $tokens);
        $approvedTokens = [];

        foreach ($tokenArr as $token) {
            $penilai = DokumenApproval::with('getUser')->where('ApprovalToken', trim($token))->first();
            if (!$penilai) {
                continue;
            }
            $cariPengajuan = PengajuanPembelian::where('KodePengajuan', $KodePengajuan)->first();
            if ($cariPengajuan) {
                $cariPengajuan->AccCeo = 'Y';
                $cariPengajuan->Status = 'Disetujui CEO';
                $cariPengajuan->TanggalAccCeo = Carbon::now();
                $cariPengajuan->save();
            }

            $penilai->update([
                'Status' => 'Approved',
                'TanggalApprove' => Carbon::now(),
            ]);
            $approvedTokens[] = $token;
        }

        AktivitasPengajuan::create([
            'KodePengajuan' => $KodePengajuan ?? null,
            'Jenis' => 'Persetujuan CEO',
            'Keterangan' => 'Arfan Awaloeddin (CEO) telah menyetujui Dokumen dengan Nomor Pengajuan: ' . ($KodePengajuan ?? '-'),
            'UserCreate' => 'Arfan Awaloeddin',
        ]);

        activity('approval_bulk')
            ->causedBy(auth()->user())
            ->withProperties([
                'approved_tokens' => $approvedTokens,
                'keterangan' => 'CEO Menyetujui Dokumen dengan Nomor pengajuan: ' . $KodePengajuan,
            ])
            ->log('CEO Menyetujui Dokumen dengan Nomor pengajuan: ' . $KodePengajuan);

        return view('emails.setelah-approval-bulk')->with([
            'message' => 'Terima kasih, persetujuan Anda berhasil dicatat.'
        ]);
    }

    public function approveReject($tokens, Request $request)
    {
        $KodePengajuan = $request->kode_pengajuan;
        $tokenArr = explode(',', $tokens);
        $rejectedTokens = [];
        $cariPengajuan = PengajuanPembelian::where('KodePengajuan', $KodePengajuan)->first();
        if ($cariPengajuan) {
            $cariPengajuan->AccCeo = 'N';
            $cariPengajuan->Status = 'Ditolak CEO';
            $cariPengajuan->TanggalAccCeo = Carbon::now();
            $cariPengajuan->save();
        }
        AktivitasPengajuan::create([
            'KodePengajuan' => $KodePengajuan ?? null,
            'Jenis' => 'Penolakan CEO',
            'Keterangan' => 'Arfan Awaloeddin (CEO) telah menolak Dokumen dengan Nomor Pengajuan: ' . ($KodePengajuan ?? '-'),
            'UserCreate' => 'Arfan Awaloeddin',
        ]);
        activity('approval_bulk')
            ->causedBy(auth()->user())
            ->withProperties([
                'rejected_tokens' => $rejectedTokens,
                'keterangan' => 'CEO Menolak Dokumen dengan Nomor pengajuan: ' . $KodePengajuan,
            ])
            ->log('CEO Menolak Dokumen dengan Nomor pengajuan: ' . $KodePengajuan);

        return view('emails.setelah-approval-bulk')->with([
            'message' => 'Terima kasih, penolakan Anda berhasil dicatat.'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Rekomendasi $rekomendasi)
    {
        //
    }

    /**
     * Menampilkan halaman tracking progres rekomendasi.
     *
     * @param string $id Encrypted id pengajuan pembelian
     * @return \Illuminate\View\View
     */
    public function tracking($id)
    {
        try {
            $pengajuanId = decrypt($id);
        } catch (\Exception $e) {
            abort(404, 'ID tidak valid.');
        }

        $pengajuan = PengajuanPembelian::with([
            'getPerusahaan',
            'getJenisPermintaan',
            'getPengajuanItem.getBarang.getMerk',
            'getTracking' => function ($query) {
                $query->orderBy('created_at', 'desc');
            }
        ])->findOrFail($pengajuanId);
        // dd($pengajuan);

        return view('rekomendasi-pembelian.tracking', compact('pengajuan'));
    }

    public function laporan()
    {
        $perusahaan = MasterPerusahaan::get();
        $namaBarang = MasterBarang::get();
        return view('rekomendasi-pembelian.laporan', compact('perusahaan', 'namaBarang'));
    }

    public function preview(Request $request)
    {
        $query = Rekomendasi::with(['getRekomedasiDetail', 'getPengajuan', 'getPerusahaan'])
            ->whereNotNull('DisetujuiPada')
            ->when($request->tanggal_awal, fn($q) => $q->whereDate('DisetujuiPada', '>=', $request->tanggal_awal))
            ->when($request->tanggal_akhir, fn($q) => $q->whereDate('DisetujuiPada', '<=', $request->tanggal_akhir))
            ->when($request->perusahaan, fn($q) => $q->where('KodePerusahaan', $request->perusahaan))
            ->when($request->namaBarang, function ($q) use ($request) {
                $q->whereHas('getRekomedasiDetail', function ($sub) use ($request) {
                    $sub
                        ->whereIn('NamaPermintaan', $request->namaBarang)
                        ->where('Rekomendasi', 1);
                });
            });

        $collection = $query->get();

        // Ubah: Jangan redirect jika tidak ada data, return error json
        if ($collection->isEmpty()) {
            return response()->json([
                'data' => [],
                'error' => 'Data tidak ditemukan.'
            ], 200);
        }

        $data = $collection->map(function ($item, $index) {
            $detail = $item->getRekomedasiDetail->first();
            $pengajuan = $item->getPengajuan;
            $perusahaan = $item->getPerusahaan;
            return [
                'DT_RowIndex' => $index + 1,
                'kode' => $pengajuan ? $pengajuan->KodePengajuan : null,
                'asal_pengajuan' => $perusahaan ? $perusahaan->NamaLengkap : null,
                'tanggal_pengajuan' => $item->getPengajuan->DiajukanPada ?? '-',
                'nama_barang' => $detail ? $detail->getBarang->Nama : null,
                'merek' => optional(optional($detail?->getBarang)->getMerk)->Nama,
                'tipe' => $detail && $detail->getBarang ? $detail->getBarang->Tipe : null,
                'vendor' => $detail && $detail->getNamaVendor ? $detail->getNamaVendor->Nama : '-',
                'nama_pic' => $pengajuan ? $pengajuan->getVendor[0]->NamaPic : null,
                'kontak_pic' => $pengajuan ? $pengajuan->getVendor[0]->KontakPic : null,
                'harga_awal' => $detail ? $detail->HargaAwal : null,
                'harga_rekomendasi' => $detail ? $detail->HargaNego : null,
                'status' => 'Rekomendasi Telah Dikeluarkan',
            ];
        });

        return response()->json(['data' => $data]);
    }

    public function export(Request $request)
    {
        $filters = $request->all();

        $tanggalAwal = !empty($filters['tanggal_awal']) ? date('d-m-Y', strtotime($filters['tanggal_awal'])) : null;
        $tanggalAkhir = !empty($filters['tanggal_akhir']) ? date('d-m-Y', strtotime($filters['tanggal_akhir'])) : null;

        $tanggalLabel = '';
        if ($tanggalAwal && $tanggalAkhir) {
            $tanggalLabel = "_{$tanggalAwal}_sd_{$tanggalAkhir}";
        } elseif ($tanggalAwal) {
            $tanggalLabel = "_{$tanggalAwal}";
        } elseif ($tanggalAkhir) {
            $tanggalLabel = "_sd_{$tanggalAkhir}";
        }

        $namaAlatLabel = '';
        if (!empty($filters['namaBarang']) && is_array($filters['namaBarang']) && count($filters['namaBarang']) === 1) {
            $alat = MasterBarang::find($filters['namaBarang'][0]);
            if ($alat) {
                $namaAlatLabel = '_' . str_replace(' ', '_', $alat->Nama);
            }
        }

        $filename = 'laporan-rekomendasi' . $tanggalLabel . $namaAlatLabel . '.xlsx';

        return Excel::download(new RekomendasiExport($filters), $filename);
    }

    public function savePdfToStorage($idPengajuan, $idPengajuanItem)
    {
        $rekomendasi = Rekomendasi::with(
            'getRekomedasiDetail.getPerusahaan',
            'getRekomedasiDetail.getBarang',
            'getRekomedasiDetail.getNegara',
            'getUserNego',
            'getDisetujuiOleh',
            'getPerusahaan',
            'getPengajuan.getVendor.getVendorDetail'
        )
            ->where('PengajuanItemId', $idPengajuanItem)
            ->whereNotNull('DisetujuiOleh')
            ->first();

        if (is_null($rekomendasi)) {
            return null;  // Atau throw exception jika ingin error
        }

        $jenis = PengajuanPembelian::find($rekomendasi->IdPengajuan);

        // Generate QR Codes
        if ($rekomendasi->UserNego !== null) {
            $qrCode = QrCode::create($rekomendasi->id)
                ->setSize(300)
                ->setMargin(10);
            $writer = new PngWriter();
            $result = $writer->write($qrCode);
            $rekomendasi->qrCodeNego = base64_encode($result->getString());
        }

        if ($rekomendasi->DisetujuiOleh !== null) {
            $qrCode = QrCode::create($rekomendasi->id ?? '')
                ->setSize(300)
                ->setMargin(10);
            $writer = new PngWriter();
            $result = $writer->write($qrCode);
            $rekomendasi->qrCodeApprove = base64_encode($result->getString());
        }

        // Tentukan path penyimpanan: simpan ke rekap-file/pengajuan_{idPengajuan}/nama_file
        $pdfFileName = 'rekomendasi_' . $idPengajuan . '.pdf';
        $dirPath = 'public/rekap-file/pengajuan-' . $idPengajuan;
        $storagePath = $dirPath . '/' . $pdfFileName;
        $fullDirPath = storage_path('app/' . $dirPath);

        // Pastikan direktori ada
        if (!file_exists($fullDirPath)) {
            mkdir($fullDirPath, 0777, true);
        }

        // ==========================================
        // JENIS == 1: SIMPAN PDF REVIEW SAJA
        // ==========================================
        if ($jenis->Jenis == 1) {
            $pdf = Pdf::loadView('rekomendasi-pembelian.cetak-review', [
                'rekomendasi' => $rekomendasi,
            ]);
            $pdf->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
            ]);

            // Simpan PDF ke storage
            Storage::put($storagePath, $pdf->output());

            // Return path publik
            return 'storage/rekap-file/pengajuan_' . $idPengajuan . '/' . $pdfFileName;
        }
        // ==========================================
        // JENIS != 1: SIMPAN PDF + MERGE DENGAN LAMPIRAN
        // ==========================================
        else {
            $pdf = Pdf::loadView('rekomendasi-pembelian.cetak-review-umum', [
                'rekomendasi' => $rekomendasi,
            ]);
            $pdf->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
            ]);

            // Cek apakah ada file lampiran
            $hasAttachment = !empty($rekomendasi->File) &&
                Storage::disk('public')->exists('rekomendasi_file/' . $rekomendasi->File);

            // Jika tidak ada lampiran, simpan PDF saja
            if (!$hasAttachment) {
                Storage::put($storagePath, $pdf->output());
                return 'storage/rekap-file/pengajuan_' . $idPengajuan . '/' . $pdfFileName;
            }

            // Path file lampiran
            $storedFilePath = Storage::disk('public')->path('rekomendasi_file/' . $rekomendasi->File);
            if (!file_exists($storedFilePath)) {
                Storage::put($storagePath, $pdf->output());
                return 'storage/rekap-file/pengajuan_' . $idPengajuan . '/' . $pdfFileName;
            }

            // ==========================================
            // GABUNGKAN PDF REVIEW + LAMPIRAN
            // ==========================================
            // Simpan PDF review sementara
            $generatedFullPath = $fullDirPath . '/temp_' . time() . '_' . uniqid() . '.pdf';
            $pdf->save($generatedFullPath);

            // Merge PDFs menggunakan FPDI
            $fpdi = new \setasign\Fpdi\Tcpdf\Fpdi();

            // 1. Tambahkan halaman dari PDF review
            $pageCount = $fpdi->setSourceFile($generatedFullPath);
            for ($i = 1; $i <= $pageCount; $i++) {
                $template = $fpdi->importPage($i);
                $size = $fpdi->getTemplateSize($template);
                $fpdi->AddPage($size['orientation'], [$size['width'], $size['height']]);
                $fpdi->useTemplate($template);
            }

            // 2. Tambahkan halaman dari file lampiran
            $attachCount = $fpdi->setSourceFile($storedFilePath);
            for ($i = 1; $i <= $attachCount; $i++) {
                $template = $fpdi->importPage($i);
                $size = $fpdi->getTemplateSize($template);
                $fpdi->AddPage($size['orientation'], [$size['width'], $size['height']]);
                $fpdi->useTemplate($template);
            }

            // 3. Output hasil merge ke buffer (hindari error path TCPDF)
            $mergedContent = $fpdi->Output('', 'S');

            // 4. Simpan hasil merge ke storage
            Storage::put($storagePath, $mergedContent);

            // 5. Hapus file temporary
            if (file_exists($generatedFullPath)) {
                unlink($generatedFullPath);
            }

            // Return path publik
            return 'storage/rekap-file/pengajuan_' . $idPengajuan . '/' . $pdfFileName;
        }
    }

    private function savePdfToStorageFUI($IdPengajuan, $barang)
    {
        // dd($IdPengajuan);
        $usulan = UsulanInvestasi::with(
            'getFuiDetail.getVendor',
            'getBarang',
            'getVendor',
            'getAccDirektur',
            'getAccKadiv',
            'getDepartemen',
            'getDepartemen2',
            'getNamaForm'
        )
            ->where('IdPengajuan', $IdPengajuan)
            ->first();

        if (!$usulan) {
            return null;
        }
        // dd($usulan);
        $VendorAcc = Rekomendasi::with([
            'getRekomedasiDetail' => function ($query2) {
                $query2->where('Rekomendasi', 1);
            },
            'getRekomedasiDetail.getNamaVendor'
        ])
            ->where('PengajuanItemId', $barang)
            ->first();

        $dataRekom = Rekomendasi::with('getRekomedasiDetail.getBarang', 'getRekomedasiDetail.getNamaVendor')
            ->where('IdPengajuan', $IdPengajuan)
            ->first();

        $approval = DokumenApproval::with('getUser', 'getJabatan', 'getDepartemen')
            ->where('JenisFormId', $usulan->JenisForm)
            ->where('DokumenId', $usulan->id)
            ->orderBy('Urutan', 'asc')
            ->get();

        // Generate QR Code untuk approval yang approved
        foreach ($approval as $item) {
            if ($item->Status == 'Approved') {
                $qrCode = QrCode::create(route('approval.validasi', $item->ApprovalToken))
                    ->setSize(300)
                    ->setMargin(10);

                $writer = new PngWriter();
                $result = $writer->write($qrCode);

                $item->qrCode = base64_encode($result->getString());
            }
        }

        $CariPengajuanItem = PengajuanItem::with('getRekomendasi')->find($barang);
        $Acc = $VendorAcc->getRekomedasiDetail[0]->IdVendor ?? null;
        $NamaBarangAcc = $VendorAcc->getRekomedasiDetail[0]->NamaPermintaan ?? null;

        $data2 = PengajuanPembelian::with([
            'getVendor' => function ($query2) use ($Acc) {
                $query2->where('NamaVendor', $Acc);
            },
            'getVendor.getVendorDetail' => function ($query) use ($NamaBarangAcc) {
                $query->where('NamaBarang', $NamaBarangAcc);
            },
            'getRekomendasi' => function ($query) {
                $query->with([
                    'getRekomedasiDetail' => function ($query2) {
                        $query2->where('Rekomendasi', 1);
                    }
                ]);
            }
        ])->find($IdPengajuan);

        // Generate PDF FUI
        $pdf = \PDF::loadView('form-usulan-investari.show-pdf', compact('usulan', 'VendorAcc', 'approval', 'data2', 'dataRekom'))
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'defaultFont' => 'sans-serif',
            ])
            ->setPaper('a4', 'portrait');

        // ==========================================
        // 2. PERSIAPAN FOLDER & SIMPAN PDF FUI SEMENTARA
        // ==========================================
        $dirPath = 'public/rekap-file/pengajuan-' . $IdPengajuan;
        $fullDirPath = storage_path('app/' . $dirPath);
        if (!file_exists($fullDirPath)) {
            mkdir($fullDirPath, 0777, true);
        }

        // Simpan PDF FUI sementara
        $fuiTempPath = $fullDirPath . '/fui-temp-' . $IdPengajuan . '-' . $barang . '.pdf';
        file_put_contents($fuiTempPath, $pdf->output());

        // ==========================================
        // 3. GABUNGKAN PDF: FS + FUI (FUI DI AKHIR)
        // ==========================================
        $combinedPdf = new \setasign\Fpdi\Tcpdf\Fpdi();

        // Path PDF FS yang sudah ada di storage
        $fsPath = $fullDirPath . '/fs-' . $IdPengajuan . '.pdf';

        // Daftar file PDF yang akan digabungkan
        $pdfFilesToMerge = [];

        // 1. PDF FS (Halaman Awal) - jika ada
        if (file_exists($fsPath)) {
            $pdfFilesToMerge[] = $fsPath;
        }

        // 2. PDF FUI (Halaman Paling Akhir) - yang baru di-generate
        $pdfFilesToMerge[] = $fuiTempPath;

        // Proses penggabungan
        foreach ($pdfFilesToMerge as $pdfFile) {
            try {
                $pageCount = $combinedPdf->setSourceFile($pdfFile);
                for ($i = 1; $i <= $pageCount; $i++) {
                    $tplIdx = $combinedPdf->importPage($i);
                    $size = $combinedPdf->getTemplateSize($tplIdx);

                    $combinedPdf->AddPage(
                        $size['orientation'],
                        [$size['width'], $size['height']]
                    );
                    $combinedPdf->useTemplate($tplIdx);
                }
            } catch (\Exception $e) {
                \Log::error('Error merging PDF FUI: ' . $e->getMessage() . ' - File: ' . $pdfFile);
            }
        }

        // ==========================================
        // 4. SIMPAN HASIL COMBINE & CLEANUP
        // ==========================================
        $finalFileName = 'fui-' . $IdPengajuan . '.pdf';
        $finalPath = $fullDirPath . '/' . $finalFileName;
        $finalPath = str_replace('\\', '/', $finalPath);
        $combinedPdf->Output($finalPath, 'F');
        if (file_exists($fuiTempPath)) {
            unlink($fuiTempPath);
        }
        return 'storage/rekap-file/pengajuan-' . $IdPengajuan . '/' . $finalFileName;
    }
}
