<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class JadwalImsak extends Controller
{
    public function getJadwalImsak(Request $request)
    {
        $provinsi = $request->input('provinsi', 'riau');
        $kabkota = $request->input('kabkota', 'kota pekanbaru');

        $payload = [
            "provinsi" => $provinsi,
            "kabkota" => $kabkota
        ];

        try {
            $today = Carbon::now();
            $puasaKe = null;
            $tanggalAwalRamadhan = 19; // ubah sesuai kebutuhan

            if ($today->day >= $tanggalAwalRamadhan) {
                $puasaKe = $today->day - ($tanggalAwalRamadhan - 1);
            }
            $response = Http::withHeaders([
                'Content-Type' => 'application/json'
            ])->post('https://equran.id/api/v2/imsakiyah', $payload);

            if (!$response->successful()) {
                return response()->json([
                    "code" => $response->status(),
                    "message" => "Gagal mendapatkan data",
                    "data" => null
                ], $response->status());
            }

            $result = $response->json();

            $jadwalPuasa = null;
            if (
                isset($result['data']['imsakiyah']) &&
                is_array($result['data']['imsakiyah']) &&
                $puasaKe !== null
            ) {
                foreach ($result['data']['imsakiyah'] as $item) {
                    if ($item['tanggal'] == $puasaKe) {
                        $jadwalPuasa = $item;
                        break;
                    }
                }
            }

            return response()->json([
                'status' => 'success',
                'provinsi' => $result['data']['provinsi'] ?? null,
                'kabkota' => $result['data']['kabkota'] ?? null,
                'hijriah' => $result['data']['hijriah'] ?? null,
                'masehi' => $result['data']['masehi'] ?? null,
                'puasaKe' => $puasaKe,
                'data' => $jadwalPuasa
            ]);

        } catch (\Exception $e) {

            return response()->json([
                "code" => 500,
                "message" => $e->getMessage(),
                "data" => null
            ], 500);
        }
    }
}
