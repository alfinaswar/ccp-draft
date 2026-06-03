<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MasterParameterSeeder extends Seeder
{
    public function run(): void
    {
        // master parameters

        DB::table('master_parameters')->insert([
            ['Nama' => 'Supplier', 'UserCreate' => 'System', 'created_at' => now()],
            ['Nama' => 'Spesifikasi', 'UserCreate' => 'System', 'created_at' => now()],
            ['Nama' => 'Populasi', 'UserCreate' => 'System', 'created_at' => now()],
            ['Nama' => 'Keamanan Pasien', 'UserCreate' => 'System', 'created_at' => now()],
            ['Nama' => 'Keamanan Petugas', 'UserCreate' => 'System', 'created_at' => now()],
            ['Nama' => 'Mudah Digunakan', 'UserCreate' => 'System', 'created_at' => now()],
            ['Nama' => 'Laporan Insiden Re/Under Call', 'UserCreate' => 'System', 'created_at' => now()],
            ['Nama' => 'Rekom. Klinis', 'UserCreate' => 'System', 'created_at' => now()],
            ['Nama' => 'Perawatan Pasca Beli', 'UserCreate' => 'System', 'created_at' => now()],
            ['Nama' => 'Fitur Khusus Emergency', 'UserCreate' => 'System', 'created_at' => now()],
            ['Nama' => 'Harga', 'UserCreate' => 'System', 'created_at' => now()],
            ['Nama' => 'BHP', 'UserCreate' => 'System', 'created_at' => now()],
            ['Nama' => 'Garansi', 'UserCreate' => 'System', 'created_at' => now()],
        ]);
    }
}
