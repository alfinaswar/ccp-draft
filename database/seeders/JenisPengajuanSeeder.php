<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JenisPengajuanSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('master_jenis_pengajuans')->insert([
            [
                'id' => 1,
                'Nama' => 'Medis',
                'Form' => 1,
                'UserCreate' => 'Administrator',
                'UserUpdate' => null,
                'UserDelete' => null,
                'deleted_at' => null,
                'created_at' => Carbon::parse('2025-12-10 07:35:51'),
                'updated_at' => Carbon::parse('2025-12-10 07:35:51'),
            ],
            [
                'id' => 2,
                'Nama' => 'Umum (Logum)',
                'Form' => 2,
                'UserCreate' => 'Administrator',
                'UserUpdate' => null,
                'UserDelete' => null,
                'deleted_at' => null,
                'created_at' => Carbon::parse('2025-12-10 07:35:59'),
                'updated_at' => Carbon::parse('2025-12-10 07:35:59'),
            ],
            [
                'id' => 3,
                'Nama' => 'Umum (Proyek)',
                'Form' => 2,
                'UserCreate' => 'Administrator',
                'UserUpdate' => null,
                'UserDelete' => null,
                'deleted_at' => null,
                'created_at' => Carbon::parse('2025-12-10 07:35:59'),
                'updated_at' => Carbon::parse('2025-12-10 07:35:59'),
            ],
        ]);
    }
}
