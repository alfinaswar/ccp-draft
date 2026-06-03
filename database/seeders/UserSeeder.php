<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        // Data personil lengkap sesuai gambar (Nama, KodePerusahaan, JabatanAsli)
        $users = [
            ['dr. Widya Putri, MARS', 'RSAB-GMD', 'Direktur RS Awal Bros Group'],
            ['dr. Ryan Mutiara, MKK', 'RSAB-SDR', 'GH Pelayanan & Penunjang Medis'],
            ['Ir. Arief Iskandar, MM', 'RSAB-GMD', 'GH FMS & GA'],
            ['dr. Putri Yuliani, MARS., CRMP', 'RSAB-GMD', 'GH Mutu'],
            ['dr. Sheandra Feibrano Takayama', 'RSAB-BTN', 'GH JKN'],
            ['Ns. Umi Eliawati, S.Kep, MARS, FISQua', 'RSAB-SDR', 'GH KEPERAWATAN'],
            ['dr. Dian Marsudiwati Ali, MARS', 'RSAB-SDR', 'GH PENUNJANG MEDIS'],
            ['Anissa Setyoningthias, SE, MM', 'RSAB-SDR', 'GH HCM'],
            ['dr. Jimmy Kurniawan, MM', 'RSAB-SDR', 'GH Marketing'],
            ['Aisyah Juliana, SE, Ak, CA', 'RSAB-SDR', 'GH Keuangan'],
            ['dr. Putri Ingen Setiasih', 'RSAB-PNM', 'GH Procurement'],
            ['dr. Ruth Hutagalung', 'RSAB-SDR', 'Ka Divisi Pelayanan Medis'],
            ['dr. Linda Devita, MH.Kes', 'RSAB-SDR', 'Ketua Komite Mutu'],
            ['Ns. Agnes Feria Dwianti Kusuma Dewi, S.Kep', 'RSAB-SDR', 'Ka Divisi Keperawatan'],
            ['Anissa Setyoningthias2, SE., MM', 'RSAB-SDR', 'Ka Divisi HCM'],
            ['Abdul Muhaimin, ST, MARS', 'RSAB-SDR', 'Ka Divisi FMS & GA'],
            ['Era Novita, SE', 'RSAB-SDR', 'Ka Divisi Keuangan & Akuntansi'],
            ['dr. Titis Nizar Elvira, MARS', 'RSAB-GMD', 'Ka Divisi Pelayanan Medis'],
            ['dr. Shinta Trilusita, MARS', 'RSAB-GMD', 'Ka Divisi Marketing'],
            ['Ns. Sri Rejeki,, S.Kep, MM', 'RSAB-GMD', 'Ka Divisi Keperawatan'],
            ['Arief Muharsyahbana, SE, Ak', 'RSAB-GMD', 'Ka Divisi Keuangan & Akuntansi'],
            ['Yayuk Dwi Ningrum, S.Psi, Psi', 'RSAB-GMD', 'Ka Divisi HCM'],
            ['dr. Mutiara Arcan, MARS', 'RSAB-PNM', 'Direktur'],
            ['dr. Era Nurissama', 'RSAB-PNM', 'Ka Divisi Pelayanan Medis'],
            ['dr. Ayu Adillah Putri N', 'RSAB-PNM', 'Ketua Komite Mutu & Keselamatan Pasien'],
            ['Lukmanul Hakim, A.md', 'RSAB-PNM', 'Ka Divisi Marketing'],
            ['Ns. Syafni, S.Kep., M. Kep', 'RSAB-PNM', 'Ka Divisi Keperawatan'],
            ['Shinta Dwi Astuti, SE', 'RSAB-PNM', 'Ka Divisi Keuangan & Akuntansi'],
            ['Wiza Rahadiani, S.S', 'RSAB-PNM', 'Ka Divisi HCM'],
            ['dr. Fani Farhansyah, MARS', 'RSAB-UJB', 'Direktur'],
            ['dr. Arbi Rahmatullah', 'RSAB-UJB', 'Ka Divisi Pelayanan Medis & Penunjang Medis'],
            ['dr. Lanisa Nauli Sitorus, MARS, FISQua', 'RSAB-UJB', 'Ketua Komite Mutu & Keselamatan Pasien'],
            ['Ns. Satri Yunita, S.Kep', 'RSAB-UJB', 'Ka Divisi Keperawatan'],
            ['Abdul Hafis, ST', 'RSAB-UJB', 'Ka Divisi Marketing'],
            ['Al Fahrul Rozi, SE', 'RSAB-UJB', 'Ka Divisi Keuangan dan Akuntansi'],
            ['Afriani Ade Putri, S.Psi, M.Psi, Psikolog', 'RSAB-UJB', 'Ka Divisi HCM'],
            ['Zulharbi, Amd', 'RSAB-UJB', 'Ka Divisi FMS & GA'],
            ['dr. Prima Aprianti', 'RSAB-AYN', 'Direktur'],
            ['dr. Helen Pratiwi Ulandari', 'RSAB-AYN', 'Ka Divisi Pelayanan Medis'],
            ['dr. Deriani Simatupang, MARS', 'RSAB-AYN', 'Komite Mutu dan Keselamatan Pasien'],
            ['Ns. Masniari Juliana Siregar, S.Kep., M.Kep', 'RSAB-AYN', 'Ka Divisi Keperawatan'],
            ['Nora Sariana, SE', 'RSAB-AYN', 'Ka Divisi Keuangan & Akuntansi'],
            ['dr. Noni Rahmawati', 'RSAB-AYN', 'Ka Divisi Marketing'],
            ['Febby Triani, MBA', 'RSAB-AYN', 'Ka Divisi HCM'],
            ['dr. Retno Kusumo, MARS', 'RSAB-BTN', 'Direktur'],
            ['dr. Deasy Larasandi', 'RSAB-BTN', 'Ka Divisi Pelayanan Medis & Penunjang Medis'],
            ['dr. Sheila Primaditya Haedi, M.K.M.', 'RSAB-BTN', 'Ketua Komite Mutu & Keselamatan Pasien'],
            ['Ns. Siska Afri Novita, S.Kep', 'RSAB-BTN', 'Ka Divisi Keperawatan'],
            ['drg. Abdul Roviq, MARS', 'RSAB-BTN', 'Ka Divisi Marketing'],
            ['Don Okki Rihhandini, SE., M.Ak', 'RSAB-BTN', 'Ka Divisi Keuangan & Akuntansi'],
            ['Deny Maisyah', 'RSAB-BTN', 'Ka Divisi FMS & GA'],
            ['dr. Fitriya Revina Sari', 'RSAB-BTN', 'Ka Divisi JKN'],
            ['dr. Suri Putri Nandita', 'RSAB-BGB', 'Direktur'],
            ['dr. Ricky Imran', 'RSAB-BGB', 'Ka Divisi Pelayanan Medis & Penunjang Medis'],
            ['dr. Hety Yunita Claudia', 'RSAB-BGB', 'Ketua Komite Mutu & Keselamatan Pasien'],
            ['Ns. R.R Woro Sulistiyowati, S. Kep', 'RSAB-BGB', 'Ka Divisi Keperawatan'],
            ['dr. Dwi Okta Lestari', 'RSAB-BGB', 'Ka Divisi Marketing'],
            ['Anshori Syaferna Putra, SE, M.Ak', 'RSAB-BGB', 'Ka Divisi Keuangan & Akuntansi'],
            ['Irena Puspi Hastuti, SE, MSi', 'RSAB-BGB', 'Ka Divisi HCM'],
            ['dr. Vandra Yovano, MARS', 'RSAB-DMI', 'Direktur'],
            ['dr. Fitria Najib', 'RSAB-DMI', 'Ka Divisi Pelayanan Medis & Penunjang Medis'],
            ['dr. Elsa Diana Fiari', 'RSAB-DMI', 'Ketua Komite Mutu & Keselamatan Pasien'],
            ['Ns. Dewi Halim Hutasuhut, S.Kep', 'RSAB-DMI', 'Ka Divisi Keperawatan'],
            ['Renaldo Munzir, SE', 'RSAB-DMI', 'Ka Divisi Marketing'],
            ['Hasna Aulia, SE', 'RSAB-DMI', 'Ka Divisi Keuangan & Akuntansi'],
            ['Joko Triono, S.Psi', 'RSAB-DMI', 'Ka Divisi HCM'],
            ['dr. Eriex Fernando Suka, MARS', 'RSAB-HTG', 'Direktur'],
            ['dr. Gusliani', 'RSAB-HTG', 'Ka Divisi Pelayanan & Penunjang Medis'],
            ['dr. Anggi Arum Sari', 'RSAB-HTG', 'Ketua Komite Mutu & Keselamatan Pasien'],
            ['Ns. Yeni Susanti, S.Kep', 'RSAB-HTG', 'Manajer Keperawatan'],
            ['Bayu Rizki Dewangga, S.M.', 'RSAB-HTG', 'Manajer Marketing'],
            ['dr. Irwin Kurniadi', 'RSAB-BAJ', 'Direktur'],
            ['dr. Hafifie Mardhiata', 'RSAB-BAJ', 'Ka Divisi Pelayanan & Penunjang Medis'],
            ['dr. Laura Nurul Alfiola', 'RSAB-BAJ', 'Ketua Komite Mutu & Keselamatan Pasien'],
            ['Ns. Yanti Sinaga, S.Kep', 'RSAB-BAJ', 'Ka Divisi Keperawatan'],
            ['Ferdy Permana, S.Kom', 'RSAB-BAJ', 'Ka Divisi HCM'],
            ['Febriana Pranesti, S.Kom', 'RSAB-AYN', 'Staff Penunjang Medis'],
            ['Rian Made Pratama, Amd', 'RSAB-AYN', 'Staff Keuangan'],
            ['Dicky, A.Md', 'RSAB-AYN', 'Staff Penunjang Medis'],
            ['Ir. H. Arfan Awaloeddin', 'RSAB-SDR', 'CEO'],
        ];

        foreach ($users as $data) {
            $namaFull = $data[0];
            $kodePerusahaan = $data[1];
            $jabatanAsli = $data[2];

            $jabatanId = 6;  // Default Staff
            if (str_contains($jabatanAsli, 'Direktur'))
                $jabatanId = 1;
            elseif (str_contains($jabatanAsli, 'GH '))
                $jabatanId = 2;
            elseif (str_contains($jabatanAsli, 'Divisi'))
                $jabatanId = 3;
            elseif (str_contains($jabatanAsli, 'Komite'))
                $jabatanId = 4;
            elseif (str_contains($jabatanAsli, 'Manajer'))
                $jabatanId = 5;
            elseif (str_contains($jabatanAsli, 'CEO'))
                $jabatanId = 7;

            $deptId = null;
            if (str_contains($jabatanAsli, 'Pelayanan & Penunjang Medis') || str_contains($jabatanAsli, 'Pelayanan dan Penunjang Medis'))
                $deptId = 3;
            elseif (str_contains($jabatanAsli, 'Pelayanan Medis'))
                $deptId = 1;
            elseif (str_contains($jabatanAsli, 'Penunjang Medis'))
                $deptId = 2;
            elseif (str_contains($jabatanAsli, 'Keperawatan'))
                $deptId = 4;
            elseif (str_contains($jabatanAsli, 'Mutu & Keselamatan') || str_contains($jabatanAsli, 'Mutu dan Keselamatan'))
                $deptId = 6;
            elseif (str_contains($jabatanAsli, 'Mutu'))
                $deptId = 5;
            elseif (str_contains($jabatanAsli, 'FMS & GA'))
                $deptId = 7;
            elseif (str_contains($jabatanAsli, 'HCM'))
                $deptId = 8;
            elseif (str_contains($jabatanAsli, 'Keuangan'))
                $deptId = 10;
            elseif (str_contains($jabatanAsli, 'Marketing'))
                $deptId = 11;
            elseif (str_contains($jabatanAsli, 'JKN'))
                $deptId = 12;
            elseif (str_contains($jabatanAsli, 'Procurement'))
                $deptId = 13;

            // Generate email sederhana (dr. Widya -> dr.widya@awalbros.com)
            $email = strtolower(str_replace([' ', '.'], '', explode(',', $namaFull)[0])) . '@awalbros.com';

            DB::table('users')->insert([
                'name' => $namaFull,
                'email' => $email,
                'password' => Hash::make('123456'),
                'kodeperusahaan' => $kodePerusahaan,
                'departemen' => $deptId,
                'jabatan' => $jabatanId,
                'UserCreate' => 'Administrator',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
}
