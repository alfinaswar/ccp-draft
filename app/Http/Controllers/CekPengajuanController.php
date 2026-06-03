<?php

namespace App\Http\Controllers;

use App\Models\PengajuanPembelian;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class CekPengajuanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = PengajuanPembelian::with('getPerusahaan', 'getJenisPermintaan', 'getPengajuanItem')
                ->whereIn('Status', ['Diajukan', 'Selesai Review', 'Menunggu Rekomendasi GH', 'Siap Presentasi', 'Dalam Review'])
                ->when($request->jenis, function ($query) use ($request) {
                    $query->where('Jenis', $request->jenis);
                })
                ->when($request->perusahaan, function ($query) use ($request) {
                    $query->where('KodePerusahaan', $request->perusahaan);
                })
                ->when($request->status, function ($query) use ($request) {
                    $query->where('Status', $request->status);
                })
                ->orderBy('id', 'desc')
                ->get();

            return DataTables::of($data)
                ->addIndexColumn()
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

                    return $buttonReview . ' ' . $buttonRekap;
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
                ->addColumn('DiajukanPada', function ($row) {
                    return $row->DiajukanPada
                        ? \Carbon\Carbon::parse($row->DiajukanPada)->translatedFormat('d M Y H:i')
                        : '-';
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
                ->rawColumns(['action', 'Status', 'DiajukanPada', 'NamaBarang', 'TanggalPresentasi'])
                ->make(true);
        }
        $jenis = MasterJenisPengajuan::get();
        $perusahaan = MasterPerusahaan::get();
        return view('rekomendasi-pembelian.index', compact('jenis', 'perusahaan'));
    }

    public function indexSelesai(Request $request)
    {
        if ($request->ajax()) {
            $data = PengajuanPembelian::with('getPerusahaan', 'getJenisPermintaan', 'getPengajuanItem')
                ->whereIn('Status', ['Selesai'])
                ->when($request->jenis, function ($query) use ($request) {
                    $query->where('Jenis', $request->jenis);
                })
                ->when($request->perusahaan, function ($query) use ($request) {
                    $query->where('KodePerusahaan', $request->perusahaan);
                })
                ->when($request->status, function ($query) use ($request) {
                    $query->where('Status', $request->status);
                })
                ->orderBy('id', 'desc')
                ->get();

            return DataTables::of($data)
                ->addIndexColumn()
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

                    return $buttonReview . ' ' . $buttonRekap;
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
                ->rawColumns(['action', 'Status', 'DiajukanPada', 'NamaBarang', 'TanggalPresentasi'])
                ->make(true);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
