<?php

namespace App\Http\Controllers;

use App\Exports\LaporanTotalPembelianExport;
use App\Models\AturanPengajuan;
use App\Models\AturanPengajuanPresentasi;
use App\Models\MasterBarang;
use App\Models\MasterJenisPengajuan;
use App\Models\MasterPerusahaan;
use App\Models\MasterVendor;
use App\Models\PengajuanPembelian;
use App\Models\Rekomendasi;
use App\Models\RekomendasiDetail;
use App\Models\TutupPengajuan;
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
                ->editColumn('KodePengajuan', function ($row) {
                    $kode = e($row->KodePengajuan ?? '-');
                    $idPengajuan = encrypt($row->id);
                    $idPengajuanItem = $row->getPengajuanItem[0]->id ?? null;
                    if ($row->KodePengajuan && $idPengajuanItem) {
                        $url = route('laporan.history-detail',$idPengajuan);
                        // Biru dan bold, tanpa underline
                        return '<a href="' . $url . '" class="btn-link" style="color:#007bff; font-weight:bold; text-decoration:none;" target="_blank">' . $kode . '</a>';
                    }
                    // Jika tidak ada link, tetap bold dan biru, tanpa underline
                    return '<span style="color:#007bff; font-weight:bold;">' . $kode . '</span>';
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
                ->rawColumns(['KodePengajuan','action', 'Status', 'DiajukanPada', 'NamaBarang', 'TanggalPresentasi', 'LokasiPenempatan'])
                ->make(true);
        }
        $jenis = MasterJenisPengajuan::get();
        $perusahaan = MasterPerusahaan::get();
        return view('laporan.history.index', compact('jenis', 'perusahaan'));

    }
    public function HistoryDetail($id){
        $id = decrypt($id);
        // dd($id);
        $data = PengajuanPembelian::with('getVendor.getVendorDetail', 'getJenisPermintaan', 'getPengajuanItem.getBarang', 'getPengajuanItem.getHtaGpa', 'getPengajuanItem.getRekomendasi', 'getPengajuanItem.getFui', 'getPengajuanItem.getDisposisi', 'getDepartemen', 'getPengajuanItem.getFs')->find($id);
        $tutup = TutupPengajuan::first();
        $hariBuka = AturanPengajuan::get();
        $hariBukaPresentasi = AturanPengajuanPresentasi::get();
        $vendor = MasterVendor::orderBy('Nama', 'asc')->get();
        $masterbarang = MasterBarang::get();
        return view('form.pengajuan-pembelian.show', compact('data', 'vendor', 'masterbarang', 'tutup', 'hariBuka', 'hariBukaPresentasi'));
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
                ->addColumn('action', function ($row) use ($request) {
                    // Bangun URL detail dengan menyertakan parameter filter bulan jika ada
                    $url = route('laporan.total-pembelian.detail', $row->Kode);
                    $params = [];
                    if ($request->filled('start_month'))
                        $params[] = 'start_month=' . $request->start_month;
                    if ($request->filled('end_month'))
                        $params[] = 'end_month=' . $request->end_month;

                    if (!empty($params)) {
                        $url .= '?' . implode('&', $params);
                    }

                    return '
        <a href="' . $url . '" class="btn btn-sm btn-info px-4">
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
    public function detailTotalPembelian(Request $request, $kode)
    {
        // dd($kode);
        $perusahaan = MasterPerusahaan::where('Kode', $kode)->firstOrFail();

        if ($request->ajax()) {
            // 1. Query utama untuk DataTables (hanya untuk mengambil data per halaman)
            $query = RekomendasiDetail::with('getPengajuan')
                ->select([
                    'rekomendasi_details.id',
                    'rekomendasi_details.IdPengajuan',
                    'rekomendasi_details.NamaPermintaan',
                    'rekomendasi_details.HargaAwal',
                    'rekomendasi_details.HargaNego',
                ])
                ->where('rekomendasi_details.KodePerusahaan', $kode)
                ->where('rekomendasi_details.Rekomendasi', '1')
                ->whereNull('rekomendasi_details.deleted_at');

            // 2. Query KHUSUS untuk menghitung TOTAL (SUM) secara akurat dari database
            $sumQuery = RekomendasiDetail::where('KodePerusahaan', $kode)
                ->where('Rekomendasi', '1')
                ->whereNull('deleted_at');

            // Terapkan filter bulan yang SAMA PERSIS untuk kedua query
            if ($request->filled('start_month')) {
                $start = Carbon::createFromFormat('Y-m', $request->start_month)->startOfMonth();
                $query->where('rekomendasi_details.created_at', '>=', $start);
                $sumQuery->where('created_at', '>=', $start);
            }
            if ($request->filled('end_month')) {
                $end = Carbon::createFromFormat('Y-m', $request->end_month)->endOfMonth();
                $query->where('rekomendasi_details.created_at', '<=', $end);
                $sumQuery->where('created_at', '<=', $end);
            }

            // Hitung total langsung dari database
            $totalAwal = $sumQuery->sum('HargaAwal') ?? 0;
            $totalNego = $sumQuery->sum('HargaNego') ?? 0;
            $totalSelisih = $sumQuery->sum(DB::raw('HargaAwal - HargaNego')) ?? 0;

            return DataTables::of($query)
                ->addIndexColumn()
                ->editColumn('HargaAwal', fn($row) => 'Rp ' . number_format($row->HargaAwal ?? 0, 0, ',', '.'))
                ->editColumn('HargaNego', fn($row) => 'Rp ' . number_format($row->HargaNego ?? 0, 0, ',', '.'))
                ->addColumn('Selisih', function ($row) {
                    $selisih = ($row->HargaAwal ?? 0) - ($row->HargaNego ?? 0);
                    return '<span class="text-success fw-bold">Rp ' . number_format($selisih, 0, ',', '.') . '</span>';
                })
                ->addColumn('NamaPermintaan', function ($row) {
                    $namaBarang = null;
                    if (
                        $row->getPengajuan &&
                        isset($row->getPengajuan->getVendor[0]) &&
                        isset($row->getPengajuan->getVendor[0]->getVendorDetail[0]) &&
                        isset($row->getPengajuan->getVendor[0]->getVendorDetail[0]->getNamaBarang) &&
                        isset($row->getPengajuan->getVendor[0]->getVendorDetail[0]->getNamaBarang->Nama)
                    ) {
                        $namaBarang = $row->getPengajuan->getVendor[0]->getVendorDetail[0]->getNamaBarang->Nama;
                    }
                    return '<span class="fw-semibold">' . e($namaBarang ?? $row->NamaPermintaan) . '</span>';
                })
                ->addColumn('action', function ($row) {
                    return '
                    <a href="#" class="btn btn-sm btn-info px-3" title="Lihat Spesifikasi">
                        <i class="fa fa-eye"></i>
                    </a>
                ';
                })
                // KIRIM TOTAL DARI DATABASE KE DATATABLES JSON
                ->with([
                    'sumAwal' => number_format($totalAwal, 0, ',', '.'),
                    'sumNego' => number_format($totalNego, 0, ',', '.'),
                    'sumSelisih' => number_format($totalSelisih, 0, ',', '.')
                ])
                ->rawColumns(['Selisih', 'action', 'NamaPermintaan'])
                ->make(true);
        }

        return view('laporan.total-pembelian.detail', compact('perusahaan'));
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
