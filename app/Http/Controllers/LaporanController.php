<?php

namespace App\Http\Controllers;

use App\Exports\LaporanTotalPembelianExport;
use App\Models\MasterJenisPengajuan;
use App\Models\MasterPerusahaan;
use App\Models\PengajuanPembelian;
use App\Models\Rekomendasi;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
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
    public function totalPembelian(Request $request)
    {
        if ($request->ajax()) {
            $query = MasterPerusahaan::select([
                'master_perusahaans.id',
                'master_perusahaans.Kode',
                'master_perusahaans.Nama',
                DB::raw('COALESCE(SUM(rekomendasi_details.HargaAwal), 0) as TotalHargaAwal'),
                DB::raw('COALESCE(SUM(rekomendasi_details.HargaNego), 0) as TotalHargaNego'),
                DB::raw('COALESCE(SUM(rekomendasi_details.HargaAwal - rekomendasi_details.HargaNego), 0) as TotalSelisih')
            ])
                ->leftJoin('rekomendasi_details', 'master_perusahaans.Kode', '=', 'rekomendasi_details.KodePerusahaan')
                ->whereNull('rekomendasi_details.deleted_at')
                ->where('rekomendasi_details.Rekomendasi', '1'); // Hanya Rekomendasi 1

            // --- LOGIKA FILTER BULAN ---
            if ($request->filled('start_month')) {
                $startDate = Carbon::createFromFormat('Y-m', $request->start_month)->startOfMonth();
                $query->where('rekomendasi_details.created_at', '>=', $startDate);
            }
            if ($request->filled('end_month')) {
                $endDate = Carbon::createFromFormat('Y-m', $request->end_month)->endOfMonth();
                $query->where('rekomendasi_details.created_at', '<=', $endDate);
            }
            // ---------------------------

            $query->groupBy('master_perusahaans.id', 'master_perusahaans.Kode', 'master_perusahaans.Nama');

            // Hitung Grand Total dengan filter yang sama
            $grandTotalQuery = MasterPerusahaan::select(
                DB::raw('COALESCE(SUM(rekomendasi_details.HargaAwal - rekomendasi_details.HargaNego), 0) as total')
            )
                ->leftJoin('rekomendasi_details', 'master_perusahaans.Kode', '=', 'rekomendasi_details.KodePerusahaan')
                ->whereNull('rekomendasi_details.deleted_at')
                ->where('rekomendasi_details.Rekomendasi', '1');

            if ($request->filled('start_month')) {
                $grandTotalQuery->where('rekomendasi_details.created_at', '>=', Carbon::createFromFormat('Y-m', $request->start_month)->startOfMonth());
            }
            if ($request->filled('end_month')) {
                $grandTotalQuery->where('rekomendasi_details.created_at', '<=', Carbon::createFromFormat('Y-m', $request->end_month)->endOfMonth());
            }

            $grandTotalSelisih = $grandTotalQuery->value('total');

            return DataTables::of($query)
                ->addIndexColumn()
                ->editColumn('TotalHargaAwal', fn($row) => 'Rp ' . number_format($row->TotalHargaAwal, 0, ',', '.'))
                ->editColumn('TotalHargaNego', fn($row) => 'Rp ' . number_format($row->TotalHargaNego, 0, ',', '.'))
                ->editColumn('TotalSelisih', fn($row) => 'Rp ' . number_format($row->TotalSelisih, 0, ',', '.'))
                ->addColumn('action', function ($row) {
                    return '
                        <a href="' . route('laporan.total-pembelian.detail', $row->Kode) . '" class="btn btn-sm btn-info px-4">
                            <i class="fa fa-list"></i> Detail
                        </a>
                    ';
                })
                ->with('grandTotalSelisih', number_format($grandTotalSelisih ?? 0, 0, ',', '.'))
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('laporan.total-pembelian.index');
    }


    /**
     * Store a newly created resource in storage.
     */
    public function exportExcel(Request $request)
    {
        $filename = 'Laporan_Total_Pembelian_Rekomendasi_' . date('Ymd_His') . '.xlsx';
        return Excel::download(new LaporanTotalPembelianExport($request), $filename);
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
