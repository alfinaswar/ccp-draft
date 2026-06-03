<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AturanPengajuanSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        DB::table('aturan_pengajuans')->insert([
            [
                'KodeHari' => 'SEN',
                'NamaHari' => 'Senin',
                'JamMulai' => '08:00:00',
                'JamSelesai' => '17:00:00',
                'isAktif' => 'Y',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'KodeHari' => 'SEL',
                'NamaHari' => 'Selasa',
                'JamMulai' => '08:00:00',
                'JamSelesai' => '17:00:00',
                'isAktif' => 'Y',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'KodeHari' => 'RAB',
                'NamaHari' => 'Rabu',
                'JamMulai' => '08:00:00',
                'JamSelesai' => '17:00:00',
                'isAktif' => 'Y',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'KodeHari' => 'KAM',
                'NamaHari' => 'Kamis',
                'JamMulai' => '08:00:00',
                'JamSelesai' => '17:00:00',
                'isAktif' => 'Y',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'KodeHari' => 'JUM',
                'NamaHari' => 'Jumat',
                'JamMulai' => '08:00:00',
                'JamSelesai' => '16:30:00',
                'isAktif' => 'Y',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'KodeHari' => 'SAB',
                'NamaHari' => 'Sabtu',
                'JamMulai' => '08:00:00',
                'JamSelesai' => '14:00:00',
                'isAktif' => 'Y',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'KodeHari' => 'MIN',
                'NamaHari' => 'Minggu',
                'JamMulai' => '00:00:00',
                'JamSelesai' => '00:00:00',
                'isAktif' => 'Y',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}
