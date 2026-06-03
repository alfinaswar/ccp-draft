<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MasterPerusahaanSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $data = [
            [
                'Kode' => 'RSAB-GMD',
                'Nama' => 'RSAB Gajah Mada',
                'NamaLengkap' => 'Rumah Sakit Awal Bros Gajah Mada',
                'Deskripsi' => 'Layanan kesehatan unggulan RS Awal Bros cabang Gajah Mada.',
                'Kategori' => 'ABGROUP',
                'Koneksi' => 'Batam, Kepri',
            ],
            [
                'Kode' => 'RSAB-SDR',
                'Nama' => 'RSAB Sudirman',
                'NamaLengkap' => 'Rumah Sakit Awal Bros Sudirman',
                'Deskripsi' => 'Pusat layanan kesehatan utama RS Awal Bros Sudirman.',
                'Kategori' => 'ABGROUP',
                'Koneksi' => 'Pekanbaru, Riau',
            ],
            [
                'Kode' => 'RSAB-BTN',
                'Nama' => 'RSAB Botania',
                'NamaLengkap' => 'Rumah Sakit Awal Bros Botania',
                'Deskripsi' => 'Layanan medis modern di wilayah Botania.',
                'Kategori' => 'ABGROUP',
                'Koneksi' => 'Batam, Kepri',
            ],
            [
                'Kode' => 'RSAB-PNM',
                'Nama' => 'RSAB Panam',
                'NamaLengkap' => 'Rumah Sakit Awal Bros Panam',
                'Deskripsi' => 'Layanan kesehatan untuk wilayah Panam dan sekitarnya.',
                'Kategori' => 'ABGROUP',
                'Koneksi' => 'Pekanbaru, Riau',
            ],
            [
                'Kode' => 'RSAB-UJB',
                'Nama' => 'RSAB Ujung Batu',
                'NamaLengkap' => 'Rumah Sakit Awal Bros Ujung Batu',
                'Deskripsi' => 'Rumah sakit pilihan masyarakat di Ujung Batu.',
                'Kategori' => 'ABGROUP',
                'Koneksi' => 'Rokan Hulu, Riau',
            ],
            [
                'Kode' => 'RSAB-AYN',
                'Nama' => 'RSAB Ayani',
                'NamaLengkap' => 'Rumah Sakit Awal Bros Ahmad Yani',
                'Deskripsi' => 'Fasilitas kesehatan lengkap di jalur protokol Ahmad Yani.',
                'Kategori' => 'ABGROUP',
                'Koneksi' => 'Pekanbaru, Riau',
            ],
            [
                'Kode' => 'RSAB-BGB',
                'Nama' => 'RSAB Bagan Batu',
                'NamaLengkap' => 'Rumah Sakit Awal Bros Bagan Batu',
                'Deskripsi' => 'Pelayanan kesehatan terpercaya di Bagan Batu.',
                'Kategori' => 'ABGROUP',
                'Koneksi' => 'Rokan Hilir, Riau',
            ],
            [
                'Kode' => 'RSAB-DMI',
                'Nama' => 'RSAB Dumai',
                'NamaLengkap' => 'Rumah Sakit Awal Bros Dumai',
                'Deskripsi' => 'Rumah sakit rujukan di Kota Dumai.',
                'Kategori' => 'ABGROUP',
                'Koneksi' => 'Dumai, Riau',
            ],
            [
                'Kode' => 'RSAB-HTG',
                'Nama' => 'RSAB Hangtuah',
                'NamaLengkap' => 'Rumah Sakit Awal Bros Hangtuah',
                'Deskripsi' => 'Layanan kesehatan berkualitas di wilayah Hangtuah.',
                'Kategori' => 'ABGROUP',
                'Koneksi' => 'Pekanbaru, Riau',
            ],
            [
                'Kode' => 'RSAB-BAJ',
                'Nama' => 'RSAB Batu Aji',
                'NamaLengkap' => 'Rumah Sakit Awal Bros Batu Aji',
                'Deskripsi' => 'Pelayanan medis untuk masyarakat wilayah Batu Aji.',
                'Kategori' => 'ABGROUP',
                'Koneksi' => 'Batam, Kepri',
            ],
            // --- CISCO GROUP INDONESIA ---
            [
                'Kode' => 'CSC-LBS',
                'Nama' => 'PT Langit Biru Sehat Sentosa',
                'NamaLengkap' => 'PT Langit Biru Sehat Sentosa',
                'Deskripsi' => 'Manajemen layanan kesehatan grup.',
                'Kategori' => 'CISCO',
                'Koneksi' => null,
            ],
            [
                'Kode' => 'CSC-CPN',
                'Nama' => 'PT Cahaya Perdana Nusantara',
                'NamaLengkap' => 'PT Cahaya Perdana Nusantara',
                'Deskripsi' => 'Logistik dan distribusi kesehatan.',
                'Kategori' => 'CISCO',
                'Koneksi' => null,
            ],
            [
                'Kode' => 'CSC-DIH',
                'Nama' => 'PT Digital Indonesia Hebat',
                'NamaLengkap' => 'PT Digital Indonesia Hebat',
                'Deskripsi' => 'Solusi teknologi dan informasi rumah sakit.',
                'Kategori' => 'CISCO',
                'Koneksi' => null,
            ],
            [
                'Kode' => 'CSC-DKH',
                'Nama' => 'PT Digital Kalibrasi Hebat',
                'NamaLengkap' => 'PT Digital Kalibrasi Hebat',
                'Deskripsi' => 'Layanan kalibrasi alat kesehatan.',
                'Kategori' => 'CISCO',
                'Koneksi' => null,
            ],
            [
                'Kode' => 'CSC-ABTC',
                'Nama' => 'Awal Bros Training Center',
                'NamaLengkap' => 'Awal Bros Training Center',
                'Deskripsi' => 'Pusat pelatihan SDM kesehatan.',
                'Kategori' => 'CISCO',
                'Koneksi' => 'Pekanbaru',
            ],
            [
                'Kode' => 'UAB',
                'Nama' => 'Universitas Awal Bros',
                'NamaLengkap' => 'Universitas Awal Bros',
                'Deskripsi' => 'Perguruan tinggi bidang kesehatan dan manajemen rumah sakit.',
                'Kategori' => 'ABGROUP',
                'Koneksi' => 'Pekanbaru',
            ],
        ];

        foreach ($data as $item) {
            DB::table('master_perusahaans')->updateOrInsert(
                ['Kode' => $item['Kode']],
                array_merge($item, [
                    'UserCreate' => 'System',
                    'created_at' => $now,
                    'updated_at' => $now,
                ])
            );
        }
    }
}
