<?php

namespace App\Http\Controllers;

use App\Models\PengajuanPembelian;
use App\Models\PermintaanPembelian;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(): View
    {
        $sixMonthsAgo = now()->subMonths(5)->startOfMonth();
        $permintaanMedis = [];
        $permintaanUmum = [];
        $permintaanProyek = [];
        $bulanLabels = [];

        $user = auth()->user();

        // Pakai auth()->user()->hasRole (harus ada Spatie/laravel-permission installed)
        $isSpecialRole = auth()->user()->hasRole(['Admin', 'CEO', 'Group Head', 'CCP']);

        for ($i = 5; $i >= 0; $i--) {
            $startDate = now()->subMonths($i)->startOfMonth();
            $endDate = now()->subMonths($i)->endOfMonth();

            if ($isSpecialRole) {
                $medis = PermintaanPembelian::where('Jenis', 1)
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->count();

                $umum = PermintaanPembelian::where('Jenis', 2)
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->count();

                $proyek = PermintaanPembelian::where('Jenis', 3)
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->count();
            } else {
                $medis = PermintaanPembelian::where('Jenis', 1)
                    ->where('KodePerusahaan', $user->kodeperusahaan)
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->count();

                $umum = PermintaanPembelian::where('Jenis', 2)
                    ->where('KodePerusahaan', $user->kodeperusahaan)
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->count();

                $proyek = PermintaanPembelian::where('Jenis', 3)
                    ->where('KodePerusahaan', $user->kodeperusahaan)
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->count();
            }

            $permintaanMedis[] = $medis;
            $permintaanUmum[] = $umum;
            $permintaanProyek[] = $proyek;
            $bulanLabels[] = $startDate->format('F');
        }

        if (auth()->id() == 4) {
            if ($isSpecialRole) {
                $TotalPermintaan = PermintaanPembelian::where('Jenis', '!=', 1)->count();
                $TotalSelesai = PengajuanPembelian::where('Status', 'Selesai')->count();
            } else {
                $TotalPermintaan = PermintaanPembelian::where('Jenis', '!=', 1)
                    ->where('KodePerusahaan', $user->kodeperusahaan)
                    ->count();
                $TotalSelesai = PengajuanPembelian::where('Status', 'Selesai')
                    ->where('KodePerusahaan', $user->kodeperusahaan)
                    ->count();
            }

            return view('home-pak-arief', compact(
                'TotalPermintaan',
                'TotalSelesai',
                'permintaanMedis',
                'permintaanUmum',
                'permintaanProyek',
                'bulanLabels'
            ));
        } else {
            if ($isSpecialRole) {
                $TotalPermintaan = PermintaanPembelian::count();
                $TotalSelesai = PengajuanPembelian::where('Status', 'Selesai')->count();
            } else {
                $TotalPermintaan = PermintaanPembelian::where('KodePerusahaan', $user->kodeperusahaan)->count();
                $TotalSelesai = PengajuanPembelian::where('Status', 'Selesai')
                    ->where('KodePerusahaan', $user->kodeperusahaan)
                    ->count();
            }
            $avgResponseTime = $this->getAvgResponseTimeMedis();
            $avgResponseTimeUmum = $this->getAvgResponseTimeUmum();
            return view('home', compact(
                'TotalPermintaan',
                'TotalSelesai',
                'permintaanMedis',
                'permintaanUmum',
                'permintaanProyek',
                'bulanLabels',
                'avgResponseTime',
                'avgResponseTimeUmum'
            ));
        }
    }

    private function getAvgResponseTimeMedis()
    {
        $months = [];
        $results = [];

        for ($i = 5; $i >= 0; $i--) {
            $start = now()->subMonths($i)->startOfMonth();
            $end = now()->subMonths($i)->endOfMonth();
            $months[] = [
                'label' => $start->format('F'),
                'start' => $start->copy(),
                'end' => $end->copy(),
            ];
        }

        // Rangkuman rata-rata proses TANPA FILTER bulan (total semua)
        $allData = PengajuanPembelian::with([
            'getRekomendasi' => function ($q) {
                $q->whereNotNull('DisetujuiOleh');
            }
        ])
            ->where('Jenis', 1)
            ->whereIn('Status', ['Selesai Review', 'Siap Presentasi', 'Selesai', 'Disetujui CEO'])
            ->get();

        $allTotalSeconds = 0;
        $allCountValid = 0;
        foreach ($allData as $pengajuan) {
            $diajukanPada = $pengajuan->DiajukanPada;
            $disetujuiPada = null;
            if (
                isset($pengajuan->getRekomendasi[0]) &&
                isset($pengajuan->getRekomendasi[0]->DisetujuiPada) &&
                $pengajuan->getRekomendasi[0]->DisetujuiPada !== null
            ) {
                $disetujuiPada = $pengajuan->getRekomendasi[0]->DisetujuiPada;
            }
            if ($diajukanPada && $disetujuiPada) {
                $start = Carbon::parse($diajukanPada);
                $end = Carbon::parse($disetujuiPada);

                $diffInSeconds = $start->diffInSeconds($end);
                $allTotalSeconds += $diffInSeconds;
                $allCountValid++;
            }
        }
        // Hitung rata-rata seluruh pengajuan (tanpa filter bulan)
        $rataRataProsesSemua = '-';
        $avgDaysSemua = null;
        $avgHoursSemua = null;
        if ($allCountValid > 0) {
            $avgSecondsSemua = (int) round($allTotalSeconds / $allCountValid);
            $avgDaysSemua = intdiv($avgSecondsSemua, 86400);
            $remSemua = $avgSecondsSemua % 86400;
            $avgHoursSemua = intdiv($remSemua, 3600);
            $rataRataProsesSemua = "{$avgDaysSemua} hari {$avgHoursSemua} jam";
        }

        foreach ($months as $month) {
            $data = PengajuanPembelian::with([
                'getRekomendasi' => function ($q) {
                    $q->whereNotNull('DisetujuiOleh');
                }
            ])
                ->where('Jenis', 1)
                ->whereBetween('DiajukanPada', [$month['start'], $month['end']])
                ->whereIn('Status', ['Selesai Review', 'Siap Presentasi', 'Selesai', 'Disetujui CEO'])
                ->get();

            $lebihDari2Minggu = 0;
            $kurangDari2Minggu = 0;
            $totalSeconds = 0;
            $countValid = 0;

            foreach ($data as $pengajuan) {
                $diajukanPada = $pengajuan->DiajukanPada;
                $disetujuiPada = null;
                if (
                    isset($pengajuan->getRekomendasi[0]) &&
                    isset($pengajuan->getRekomendasi[0]->DisetujuiPada) &&
                    $pengajuan->getRekomendasi[0]->DisetujuiPada !== null
                ) {
                    $disetujuiPada = $pengajuan->getRekomendasi[0]->DisetujuiPada;
                }

                if ($diajukanPada && $disetujuiPada) {
                    $start = Carbon::parse($diajukanPada);
                    $end = Carbon::parse($disetujuiPada);
                    $diffInDays = $start->diffInDays($end);

                    if ($diffInDays > 14) {
                        $lebihDari2Minggu++;
                    } else {
                        $kurangDari2Minggu++;
                    }

                    $diffInSeconds = $start->diffInSeconds($end);
                    $totalSeconds += $diffInSeconds;
                    $countValid++;
                }
            }

            // Hitung rata-rata dalam format hari jam
            $rataRataProses = '-';
            $avgDays = null;
            $avgHours = null;
            if ($countValid > 0) {
                $avgSeconds = (int) round($totalSeconds / $countValid);
                $avgDays = intdiv($avgSeconds, 86400);
                $rem = $avgSeconds % 86400;
                $avgHours = intdiv($rem, 3600);
                $rataRataProses = "{$avgDays} hari {$avgHours} jam";
            }

            $results[] = [
                'bulan' => $month['label'],
                'jumlah' => $countValid,
                'avg_days' => $avgDays,
                'avg_hours' => $avgHours,
                'lebih_dari_2minggu' => $lebihDari2Minggu,
                'kurang_dari_2minggu' => $kurangDari2Minggu,
                'rata_rata_proses' => $rataRataProses,
                'avg_all' => $rataRataProsesSemua,
            ];
        }
        return $results;
    }

    private function getAvgResponseTimeUmum()
    {
        $months = [];
        $results = [];

        for ($i = 5; $i >= 0; $i--) {
            $start = now()->subMonths($i)->startOfMonth();
            $end = now()->subMonths($i)->endOfMonth();
            $months[] = [
                'label' => $start->format('F'),
                'start' => $start->copy(),
                'end' => $end->copy(),
            ];
        }

        // Rangkuman rata-rata proses TANPA FILTER bulan (total semua)
        $allData = PengajuanPembelian::with([
            'getRekomendasi' => function ($q) {
                $q->whereNotNull('DisetujuiOleh');
            }
        ])
            ->where('Jenis', '!=', 1)
            ->whereIn('Status', ['Selesai Review', 'Siap Presentasi', 'Selesai', 'Disetujui CEO'])
            ->get();

        $allTotalSeconds = 0;
        $allCountValid = 0;
        foreach ($allData as $pengajuan) {
            $diajukanPada = $pengajuan->DiajukanPada;
            $disetujuiPada = null;
            if (
                isset($pengajuan->getRekomendasi[0]) &&
                isset($pengajuan->getRekomendasi[0]->DisetujuiPada) &&
                $pengajuan->getRekomendasi[0]->DisetujuiPada !== null
            ) {
                $disetujuiPada = $pengajuan->getRekomendasi[0]->DisetujuiPada;
            }
            if ($diajukanPada && $disetujuiPada) {
                $start = Carbon::parse($diajukanPada);
                $end = Carbon::parse($disetujuiPada);

                $diffInSeconds = $start->diffInSeconds($end);
                $allTotalSeconds += $diffInSeconds;
                $allCountValid++;
            }
        }
        // Hitung rata-rata seluruh pengajuan (tanpa filter bulan)
        $rataRataProsesSemua = '-';
        $avgDaysSemua = null;
        $avgHoursSemua = null;
        if ($allCountValid > 0) {
            $avgSecondsSemua = (int) round($allTotalSeconds / $allCountValid);
            $avgDaysSemua = intdiv($avgSecondsSemua, 86400);
            $remSemua = $avgSecondsSemua % 86400;
            $avgHoursSemua = intdiv($remSemua, 3600);
            $rataRataProsesSemua = "{$avgDaysSemua} hari {$avgHoursSemua} jam";
        }

        foreach ($months as $month) {
            $data = PengajuanPembelian::with([
                'getRekomendasi' => function ($q) {
                    $q->whereNotNull('DisetujuiOleh');
                }
            ])
                ->where('Jenis', '!=', 1)
                ->whereBetween('DiajukanPada', [$month['start'], $month['end']])
                ->whereIn('Status', ['Selesai Review', 'Siap Presentasi', 'Selesai', 'Disetujui CEO'])
                ->get();

            $lebihDari2Minggu = 0;
            $kurangDari2Minggu = 0;
            $totalSeconds = 0;
            $countValid = 0;

            foreach ($data as $pengajuan) {
                // $diajukanPada = $pengajuan->;

                $disetujuiPada = null;
                if (
                    isset($pengajuan->getRekomendasi[0]) &&
                    isset($pengajuan->getRekomendasi[0]->DisetujuiPada) &&
                    $pengajuan->getRekomendasi[0]->DisetujuiPada !== null
                ) {
                    $disetujuiPada = $pengajuan->getRekomendasi[0]->DisetujuiPada;
                }

                if ($diajukanPada && $disetujuiPada) {
                    $start = Carbon::parse($diajukanPada);
                    $end = Carbon::parse($disetujuiPada);
                    $diffInDays = $start->diffInDays($end);

                    if ($diffInDays > 14) {
                        $lebihDari2Minggu++;
                    } else {
                        $kurangDari2Minggu++;
                    }

                    $diffInSeconds = $start->diffInSeconds($end);
                    $totalSeconds += $diffInSeconds;
                    $countValid++;
                }
            }

            // Hitung rata-rata dalam format hari jam
            $rataRataProses = '-';
            $avgDays = null;
            $avgHours = null;
            if ($countValid > 0) {
                $avgSeconds = (int) round($totalSeconds / $countValid);
                $avgDays = intdiv($avgSeconds, 86400);
                $rem = $avgSeconds % 86400;
                $avgHours = intdiv($rem, 3600);
                $rataRataProses = "{$avgDays} hari {$avgHours} jam";
            }

            $results[] = [
                'bulan' => $month['label'],
                'jumlah' => $countValid,
                'avg_days' => $avgDays,
                'avg_hours' => $avgHours,
                'lebih_dari_2minggu' => $lebihDari2Minggu,
                'kurang_dari_2minggu' => $kurangDari2Minggu,
                'rata_rata_proses' => $rataRataProses,
                'avg_all' => $rataRataProsesSemua,
            ];
        }
        return $results;
    }

    // public function getJadwalImsak(Request $request)
    // {
    //     $provinsi = $request->input('provinsi', 'riau');
    //     $kabkota = $request->input('kabkota', 'kota pekanbaru');

    //     $payload = [
    //         "provinsi" => $provinsi,
    //         "kabkota" => $kabkota
    //     ];

    //     try {
    //         $today = Carbon::now();

    //         $tanggalAwalRamadhan = Carbon::create($today->year, 3, 19); // asumsikan ramadhan mulai 19 maret
    //         // Jika ramadhan bisa beda bulan/tahun, disini bisa diubah dari config/setting lain sesuai kebutuhan

    //         // Hitung puasaKe berdasarkan selisih hari dari tanggal mulai ramadhan (cross-month)
    //         if ($today->greaterThanOrEqualTo($tanggalAwalRamadhan)) {
    //             $puasaKe = $tanggalAwalRamadhan->diffInDays($today) + 1;
    //         } else {
    //             $puasaKe = null;
    //         }

    //         $response = Http::withHeaders([
    //             'Content-Type' => 'application/json'
    //         ])->post('https://equran.id/api/v2/imsakiyah', $payload);

    //         if (!$response->successful()) {
    //             return response()->json([
    //                 "code" => $response->status(),
    //                 "message" => "Gagal mendapatkan data",
    //                 "data" => null
    //             ], $response->status());
    //         }

    //         $result = $response->json();

    //         $jadwalPuasa = null;
    //         if (
    //             isset($result['data']['imsakiyah']) &&
    //             is_array($result['data']['imsakiyah']) &&
    //             $puasaKe !== null
    //         ) {
    //             foreach ($result['data']['imsakiyah'] as $item) {
    //                 if (isset($item['puasa']) && $item['puasa'] == $puasaKe) {
    //                     $jadwalPuasa = $item;
    //                     break;
    //                 }
    //             }
    //         }

    //         return response()->json([
    //             'status' => 'success',
    //             'provinsi' => $result['data']['provinsi'] ?? null,
    //             'kabkota' => $result['data']['kabkota'] ?? null,
    //             'hijriah' => $result['data']['hijriah'] ?? null,
    //             'masehi' => $result['data']['masehi'] ?? null,
    //             'puasaKe' => $puasaKe,
    //             'data' => $jadwalPuasa
    //         ]);

    //     } catch (\Exception $e) {

    //         return response()->json([
    //             "code" => 500,
    //             "message" => $e->getMessage(),
    //             "data" => null
    //         ], 500);
    //     }
    // }
}
