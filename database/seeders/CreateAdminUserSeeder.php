<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class CreateAdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Daftar RS dari MasterPerusahaanSeeder
        $companies = [
            [
                'Kode' => 'RSAB-GMD',
                'Nama' => 'RSAB Gajah Mada',
            ],
            [
                'Kode' => 'RSAB-SDR',
                'Nama' => 'RSAB Sudirman',
            ],
            [
                'Kode' => 'RSAB-BTN',
                'Nama' => 'RSAB Botania',
            ],
            [
                'Kode' => 'RSAB-PNM',
                'Nama' => 'RSAB Panam',
            ],
            [
                'Kode' => 'RSAB-UJB',
                'Nama' => 'RSAB Ujung Batu',
            ],
            [
                'Kode' => 'RSAB-AYN',
                'Nama' => 'RSAB Ayani',
            ],
            [
                'Kode' => 'RSAB-BGB',
                'Nama' => 'RSAB Bagan Batu',
            ],
            [
                'Kode' => 'RSAB-DMI',
                'Nama' => 'RSAB Dumai',
            ],
            [
                'Kode' => 'RSAB-HTG',
                'Nama' => 'RSAB Hangtuah',
            ],
            [
                'Kode' => 'RSAB-BAJ',
                'Nama' => 'RSAB Batu Aji',
            ],
            [
                'Kode' => 'CSC-LBS',
                'Nama' => 'PT Langit Biru Sehat Sentosa',
            ],
            [
                'Kode' => 'CSC-CPN',
                'Nama' => 'PT Cahaya Perdana Nusantara',
            ],
            [
                'Kode' => 'CSC-DIH',
                'Nama' => 'PT Digital Indonesia Hebat',
            ],
            [
                'Kode' => 'CSC-DKH',
                'Nama' => 'PT Digital Kalibrasi Hebat',
            ],
            [
                'Kode' => 'CSC-ABTC',
                'Nama' => 'Awal Bros Training Center',
            ],
            [
                'Kode' => 'UAB',
                'Nama' => 'Universitas Awal Bros',
            ],
        ];

        $role = Role::firstOrCreate(['name' => 'Admin Perusahaan']);
        $permissions = Permission::pluck('id', 'id')->all();
        $role->syncPermissions($permissions);

        foreach ($companies as $company) {
            $email = strtolower(str_replace([' ', '-'], ['', ''], $company['Kode'])) . '@admin.com';
            $user = User::firstOrCreate(
                [
                    'email' => $email,
                ],
                [
                    'name' => 'Administrator ' . $company['Nama'],
                    'password' => bcrypt('123456'),
                    'kodeperusahaan' => $company['Kode'],
                ]
            );
            $user->assignRole([$role->id]);
        }
    }
}
