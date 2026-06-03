<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DepartemenSeeder extends Seeder
{
    public function run(): void
    {
        $departemen = [
            ['DEP001', 1, 'Pelayanan Medis'],
            ['DEP002', 2, 'Penunjang Medis'],
            ['DEP003', 3, 'Pelayanan & Penunjang Medis'],
            ['DEP004', 4, 'Keperawatan'],
            ['DEP005', 5, 'Mutu'],
            ['DEP006', 6, 'Mutu & Keselamatan Pasien'],
            ['DEP007', 7, 'FMS & GA'],
            ['DEP008', 8, 'HCM'],
            ['DEP009', 9, 'Keuangan'],
            ['DEP010', 10, 'Keuangan & Akuntansi'],
            ['DEP011', 11, 'Marketing'],
            ['DEP012', 12, 'JKN'],
            ['DEP013', 13, 'Procurement'],
        ];

        foreach ($departemen as $d) {
            DB::table('master_departemens')->insert([
                'KodeDepartemen' => $d[0],
                'IdDepartemen' => $d[1],
                'Nama' => $d[2],
                'UserCreate' => 'Administrator',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}
