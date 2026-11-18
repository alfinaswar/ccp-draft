<?php

namespace Database\Seeders;

use App\Models\MasterParameterKomplain;
use Illuminate\Database\Seeder;

/**
 * Parameter awal formulir komplain, mengikuti FORMULIR EVALUASI BMHP UMUM 2026.
 */
class ParameterKomplainSeeder extends Seeder
{
    public function run(): void
    {
        $daftar = [
            'ParameterEvaluasi' => [
                'Cacat Produksi',
                'Warna/Penampilan tidak sesuai',
                'Kualitas Produk Tidak Bagus',
                'Fungsi Produk tidak optimal',
                'Keluhan Pengguna berulang',
                'Lainnya',
            ],
            'DataPendukung' => [
                'Foto',
                'Sampel Barang/BMHP Umum',
                'Lainnya',
            ],
            'LangkahAwal' => [
                'Tidak digunakan sementara',
                'Dilaporkan ke Distributor/Penyedia',
                'Dikembalikan ke Distributor/Penyedia',
                'Lainnya',
            ],
            'TindakanCcp' => [
                'Dihentikan',
                'Verifikasi ulang',
                'Diganti BMHP',
                'Dikembalikan ke Distributor/Penyedia',
                'Investigasi Kualitas',
                'Lainnya',
            ],
        ];

        foreach ($daftar as $jenis => $items) {
            foreach (array_values($items) as $urutan => $nama) {
                MasterParameterKomplain::firstOrCreate(
                    ['Jenis' => $jenis, 'Nama' => $nama],
                    [
                        'Urutan' => $urutan + 1,
                        'Aktif' => 'Y',
                        'UserCreate' => 'Administrator',
                    ]
                );
            }
        }
    }
}
