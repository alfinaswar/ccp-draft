<?php

namespace App\Http\Controllers;

use App\Models\MasterJenisPengajuan;
use App\Models\MasterPerusahaan;
use App\Models\PengajuanPembelian;
use App\Models\Rekomendasi;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Yajra\DataTables\DataTables;

class LaporanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function History(Request $request)
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
                    $idPengajuan = encrypt($row->id);
                    $idPengajuanItem = $row->getPengajuanItem[0]->id ?? null;
                    $buttonRekap = '
                        <a href="' . route('rekomendasi.rekap', [$idPengajuan, encrypt($idPengajuanItem)]) . '" class="btn btn-sm btn-success" title="Rekap" target="_blank">
                            <i class="fa fa-file-alt"></i> Rekap
                        </a>
                    ';
                    return $buttonRekap;
                })


                ->addColumn('DiajukanPada', function ($row) {
                    return $row->DiajukanPada
                        ? Carbon::parse($row->DiajukanPada)->translatedFormat('d M Y H:i')
                        : '-';
                })
                ->addColumn('LokasiPenempatan', function ($row) {
                    $detail = $row->getPermintaan->getDetail->first() ?? null;
                    return $detail->RencanaPenempatan ?? '-';
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
        $jenis = MasterJenisPengajuan::get();
        $perusahaan = MasterPerusahaan::get();
        return view('laporan.history.index', compact('jenis', 'perusahaan'));

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
