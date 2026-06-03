<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JabatanSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['Nama' => 'Direktur', 'UserCreate' => 'system', 'UserUpdate' => null, 'UserDelete' => null],
            ['Nama' => 'Group Head (GH)', 'UserCreate' => 'system', 'UserUpdate' => null, 'UserDelete' => null],
            ['Nama' => 'Kepala Divisi', 'UserCreate' => 'system', 'UserUpdate' => null, 'UserDelete' => null],
            ['Nama' => 'Ketua Komite', 'UserCreate' => 'system', 'UserUpdate' => null, 'UserDelete' => null],
            ['Nama' => 'Manajer', 'UserCreate' => 'system', 'UserUpdate' => null, 'UserDelete' => null],
            ['Nama' => 'Staff', 'UserCreate' => 'system', 'UserUpdate' => null, 'UserDelete' => null],
            ['Nama' => 'CEO', 'UserCreate' => 'system', 'UserUpdate' => null, 'UserDelete' => null],
            ['Nama' => 'Direktur Group', 'UserCreate' => 'system', 'UserUpdate' => null, 'UserDelete' => null],
        ];

        DB::table('master_jabatans')->insert($data);
    }
}
