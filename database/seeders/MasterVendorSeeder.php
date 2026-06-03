<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MasterVendorSeeder extends Seeder
{
    public function run(): void
    {
        $vendors = [
            ['Nama' => 'PT. Adidarma Mitra Sejahtera', 'Alamat' => 'Jl. Green Ville Blok A No. 6, Jakarta Barat 11510', 'NoHp' => '021-5633093', 'Email' => 'adidarma.mitrasejahtera@gmail.com', 'NamaPic' => 'Ryan', 'NoHpPic' => '08128330081'],
            ['Nama' => 'PT. Delta Wijaya Sejahtera', 'Alamat' => 'Jalan Palmerah Barat No. 31, Palmerah, Jakarta Barat 11480', 'NoHp' => '021 53600100', 'Email' => 'salesteam@deltawijaya.co.id', 'NamaPic' => 'Ani', 'NoHpPic' => '08112338812'],
            ['Nama' => 'PT. Mulia Jaya Industri', 'Alamat' => '-', 'NoHp' => '-', 'Email' => '-', 'NamaPic' => 'Jen. Budi', 'NoHpPic' => '0811400030'],
            ['Nama' => 'PT. Pelita Maritim Indo-Asia', 'Alamat' => 'Jalan Marunda Raya, Bekasi, Jawa Barat', 'NoHp' => '082210137537', 'Email' => 'filip.maritima@yahoo.co.id', 'NamaPic' => 'Budiman', 'NoHpPic' => '08115629125'],
            ['Nama' => 'PT. Astragraphia X-Press', 'Alamat' => 'Jalan Kramat Raya No. 43, Jakarta Pusat', 'NoHp' => '0213909100', 'Email' => 'astra.mail@agx.co.id', 'NamaPic' => 'Pak Dani', 'NoHpPic' => '08129302549'],
            ['Nama' => 'PT. Mitrabahtera Segara Sejati', 'Alamat' => 'Jalan Jendral Gatot Subroto Kav. 21, Jakarta Selatan', 'NoHp' => '0215257859', 'Email' => 'mbss@mbss.co.id', 'NamaPic' => 'Vita', 'NoHpPic' => '0811874400'],
            ['Nama' => 'PT. Baruna Adipraya', 'Alamat' => '-', 'NoHp' => '-', 'Email' => '-', 'NamaPic' => 'Herman', 'NoHpPic' => '08119782510'],
            ['Nama' => 'PT. Bahana Lines', 'Alamat' => 'Jalan Kebon Sirih Barat, Jakarta Pusat', 'NoHp' => '02131930235', 'Email' => 'bahana.marina@gmail.com', 'NamaPic' => 'Angel', 'NoHpPic' => '081210082749'],
            ['Nama' => 'PT. Pelayaran Nasional Indonesia', 'Alamat' => 'Jl. Gajah Mada No. 14, Jakarta Pusat', 'NoHp' => '0216334342', 'Email' => 'kontak@pelni.co.id', 'NamaPic' => 'Indra', 'NoHpPic' => '081219846744'],
            ['Nama' => 'PT. Logistik Indonesia', 'Alamat' => 'Jalan Perdana Kav. B, Jakarta Timur', 'NoHp' => '02129033344', 'Email' => 'logistik@indo.com', 'NamaPic' => 'Kiki', 'NoHpPic' => '081114552234'],
            ['Nama' => 'PT. Krakatau Steel', 'Alamat' => 'Jalan Industri No. 5, Cilegon, Banten', 'NoHp' => '0254 392122', 'Email' => 'marketing@krakatausteel.com', 'NamaPic' => 'Bambang', 'NoHpPic' => '08111223344'],
            ['Nama' => 'PT. Indonesia Fiberboard', 'Alamat' => 'Jalan Sudirman Kav. 10-11, Jakarta Pusat', 'NoHp' => '021 5205500', 'Email' => 'indonesia@fiber.co.id', 'NamaPic' => 'Andri', 'NoHpPic' => '08111776534'],
            ['Nama' => 'PT. Armada Maritim', 'Alamat' => 'Jalan Sultan Agung, Jakarta Selatan', 'NoHp' => '021 8305000', 'Email' => 'armada@maritim.com', 'NamaPic' => 'Iwan', 'NoHpPic' => '0811900088'],
            ['Nama' => 'PT. Anugerah Jaya', 'Alamat' => 'Jalan Letjen Suprapto, Jakarta Pusat', 'NoHp' => '021 4205566', 'Email' => 'anugerah@jaya.com', 'NamaPic' => 'Syarif', 'NoHpPic' => '081233445566'],
            ['Nama' => 'PT. Deli Surya', 'Alamat' => 'Jalan MT Haryono Kav. 8, Jakarta Selatan', 'NoHp' => '021 8295577', 'Email' => 'deli@surya.co.id', 'NamaPic' => 'Maya', 'NoHpPic' => '081155667788'],
            ['Nama' => 'PT. Sinar Mas', 'Alamat' => 'Jalan MH Thamrin No. 51, Jakarta Pusat', 'NoHp' => '021 31922233', 'Email' => 'sinar@mas.com', 'NamaPic' => 'Ferry', 'NoHpPic' => '081288990011'],
            ['Nama' => 'PT. Mitrabahtera', 'Alamat' => '-', 'NoHp' => '-', 'Email' => '-', 'NamaPic' => 'Putri', 'NoHpPic' => '-'],
            ['Nama' => 'PT. Pelayaran Tamarin', 'Alamat' => 'Jalan HR Rasuna Said, Jakarta Selatan', 'NoHp' => '021 52960111', 'Email' => 'tamarin@tamarin.co.id', 'NamaPic' => 'Dina', 'NoHpPic' => '081133221144'],
            ['Nama' => 'PT. Marunda Jaya', 'Alamat' => 'Jalan Marunda Makmur, Bekasi', 'NoHp' => '021 8899001', 'Email' => 'marunda@jaya.com', 'NamaPic' => 'Peter', 'NoHpPic' => '081255443322'],
            ['Nama' => 'PT. Marine Tech', 'Alamat' => 'Jalan Gunung Sahari, Jakarta Pusat', 'NoHp' => '021 62304455', 'Email' => 'marine@tech.com', 'NamaPic' => 'Kurnia', 'NoHpPic' => '081199887766'],
            ['Nama' => 'PT. Gurita Lintas', 'Alamat' => 'Jalan Gatot Subroto, Jakarta Selatan', 'NoHp' => '021 5253344', 'Email' => 'gurita@lintas.co.id', 'NamaPic' => 'Martin', 'NoHpPic' => '081155446677'],
            ['Nama' => 'PT. Global Marine', 'Alamat' => 'Jalan Jend. Sudirman, Jakarta Pusat', 'NoHp' => '021 5703344', 'Email' => 'global@marine.com', 'NamaPic' => 'Erwin', 'NoHpPic' => '081244556677'],
            ['Nama' => 'PT. Pelayaran Meratus', 'Alamat' => 'Jalan Alun-Alun Priok, Surabaya', 'NoHp' => '031 3293344', 'Email' => 'meratus@meratus.com', 'NamaPic' => 'Richard', 'NoHpPic' => '081133445566'],
            ['Nama' => 'PT. Samudera Indonesia', 'Alamat' => '-', 'NoHp' => '-', 'Email' => '-', 'NamaPic' => 'Suryadi', 'NoHpPic' => '081111223344'],
            ['Nama' => 'PT. Adidarma Mitra', 'Alamat' => 'Jl. Green Ville Blok A, Jakarta Barat', 'NoHp' => '021 5633093', 'Email' => 'adidarma@gmail.com', 'NamaPic' => 'Hasan', 'NoHpPic' => '081111445566'],
            ['Nama' => 'PT. Bangka Belitung', 'Alamat' => 'Jalan Raya Pelabuhan, Bangka', 'NoHp' => '0717 421122', 'Email' => 'bangka@belitung.com', 'NamaPic' => 'Anton', 'NoHpPic' => '081211223344'],
            ['Nama' => 'PT. ADI Ship Management', 'Alamat' => 'Jalan Ahmad Yani, Jakarta Timur', 'NoHp' => '021 4893344', 'Email' => 'adi@ship.com', 'NamaPic' => 'Willy', 'NoHpPic' => '081122334455'],
            ['Nama' => 'PT. Master Ship', 'Alamat' => 'Jalan Kebon Bawang, Jakarta Utara', 'NoHp' => '021 4390011', 'Email' => 'master@ship.com', 'NamaPic' => 'Benny', 'NoHpPic' => '081133445566'],
            ['Nama' => 'PT. Marina Mandarin', 'Alamat' => 'Jalan Marina Raya, Jakarta Utara', 'NoHp' => '021 6621122', 'Email' => 'marina@mandarin.com', 'NamaPic' => 'Oscar', 'NoHpPic' => '081144556677'],
            ['Nama' => 'PT. Bangka Alumindo', 'Alamat' => 'Jalan Industri, Bangka', 'NoHp' => '0717 431122', 'Email' => 'bangka@alumindo.com', 'NamaPic' => 'Baim', 'NoHpPic' => '081155667788'],
            ['Nama' => 'PT. Prima Maritim', 'Alamat' => 'Jalan Raya Pelabuhan, Jakarta Utara', 'NoHp' => '021 4301122', 'Email' => 'prima@maritim.com', 'NamaPic' => 'Donny', 'NoHpPic' => '081166778899'],
            ['Nama' => 'PT. Delta Marine', 'Alamat' => 'Jalan Palmerah, Jakarta Barat', 'NoHp' => '021 5361122', 'Email' => 'delta@marine.com', 'NamaPic' => 'Ida', 'NoHpPic' => '081177889900'],
            ['Nama' => 'PT. Samudera Lautan', 'Alamat' => 'Jalan Yos Sudarso, Jakarta Utara', 'NoHp' => '021 4391122', 'Email' => 'samudera@lautan.com', 'NamaPic' => 'Said', 'NoHpPic' => '081188990011'],
            ['Nama' => 'PT. Multi Jasa Riva', 'Alamat' => '-', 'NoHp' => '-', 'Email' => '-', 'NamaPic' => 'Rudi', 'NoHpPic' => '081199001122'],
            ['Nama' => 'PT. Mitrabahtera Segara', 'Alamat' => 'Jalan Gatot Subroto, Jakarta Selatan', 'NoHp' => '021 5251122', 'Email' => 'mbss@segara.com', 'NamaPic' => 'Vita', 'NoHpPic' => '081111223344'],
            ['Nama' => 'PT. Indonesia Media', 'Alamat' => 'Jalan Sudirman, Jakarta Pusat', 'NoHp' => '021 5201122', 'Email' => 'indo@media.com', 'NamaPic' => 'Hadi', 'NoHpPic' => '081122334455'],
            ['Nama' => 'PT. Mulia Keramik', 'Alamat' => 'Jalan Industri, Cikarang', 'NoHp' => '021 8971122', 'Email' => 'mulia@keramik.com', 'NamaPic' => 'Eko', 'NoHpPic' => '081133445566'],
            ['Nama' => 'PT. Bahari Lines', 'Alamat' => 'Jalan Kebon Sirih, Jakarta Pusat', 'NoHp' => '021 3191122', 'Email' => 'bahari@lines.com', 'NamaPic' => 'Ani', 'NoHpPic' => '081144556677'],
            ['Nama' => 'PT. Transcoal', 'Alamat' => 'Jalan HR Rasuna Said, Jakarta Selatan', 'NoHp' => '021 5291122', 'Email' => 'trans@coal.com', 'NamaPic' => 'Budi', 'NoHpPic' => '081155667788'],
            ['Nama' => 'PT. Pelita Air', 'Alamat' => 'Jalan Abdul Muis, Jakarta Pusat', 'NoHp' => '021 3811122', 'Email' => 'pelita@air.com', 'NamaPic' => 'Sari', 'NoHpPic' => '081166778899'],
            ['Nama' => 'PT. Gurita Lintas', 'Alamat' => 'Jalan Gatot Subroto, Jakarta Selatan', 'NoHp' => '021 5251122', 'Email' => 'gurita@lintas.com', 'NamaPic' => 'Andi', 'NoHpPic' => '081177889900'],
            ['Nama' => 'PT. Maju Bersama', 'Alamat' => 'Jalan Ahmad Yani, Jakarta Timur', 'NoHp' => '021 4891122', 'Email' => 'maju@bersama.com', 'NamaPic' => 'Joni', 'NoHpPic' => '081188990011'],
            ['Nama' => 'PT. Mitra Indah', 'Alamat' => 'Jalan Kebon Bawang, Jakarta Utara', 'NoHp' => '021 4391122', 'Email' => 'mitra@indah.com', 'NamaPic' => 'Siska', 'NoHpPic' => '081199001122'],
            ['Nama' => 'PT. Marina Mandarin', 'Alamat' => 'Jalan Marina Raya, Jakarta Utara', 'NoHp' => '021 6621122', 'Email' => 'marina@mandarin.com', 'NamaPic' => 'Oscar', 'NoHpPic' => '081111223344'],
            ['Nama' => 'PT. Pelayaran Meratus', 'Alamat' => 'Jalan Alun-Alun Priok, Surabaya', 'NoHp' => '031 3291122', 'Email' => 'meratus@meratus.com', 'NamaPic' => 'Richard', 'NoHpPic' => '081122334455'],
            ['Nama' => 'PT. Samudera Indonesia', 'Alamat' => 'Jalan Letjen Suprapto, Jakarta Pusat', 'NoHp' => '021 4201122', 'Email' => 'samudera@indo.com', 'NamaPic' => 'Hasan', 'NoHpPic' => '081133445566'],
            ['Nama' => 'PT. Philip Maritima', 'Alamat' => 'Jalan Marunda Raya, Bekasi', 'NoHp' => '021 8891122', 'Email' => 'philip@maritima.com', 'NamaPic' => 'Budiman', 'NoHpPic' => '081144556677'],
            ['Nama' => 'PT. Astragraphia', 'Alamat' => 'Jalan Kramat Raya, Jakarta Pusat', 'NoHp' => '021 3901122', 'Email' => 'astra@graphia.com', 'NamaPic' => 'Dani', 'NoHpPic' => '081155667788'],
            ['Nama' => 'PT. Mitrabahtera', 'Alamat' => 'Jalan Gatot Subroto, Jakarta Selatan', 'NoHp' => '021 5251122', 'Email' => 'mbss@mbss.com', 'NamaPic' => 'Vita', 'NoHpPic' => '081166778899'],
            ['Nama' => 'PT. Baruna Adipraya', 'Alamat' => 'Jalan Kebon Sirih, Jakarta Pusat', 'NoHp' => '021 3191122', 'Email' => 'baruna@adipraya.com', 'NamaPic' => 'Herman', 'NoHpPic' => '081177889900'],
            ['Nama' => 'PT. Bahana Lines', 'Alamat' => 'Jalan Gajah Mada, Jakarta Pusat', 'NoHp' => '021 6331122', 'Email' => 'bahana@lines.com', 'NamaPic' => 'Angel', 'NoHpPic' => '081188990011'],
            ['Nama' => 'PT. Pelayaran Nasional', 'Alamat' => 'Jalan Gajah Mada, Jakarta Pusat', 'NoHp' => '021 6331122', 'Email' => 'pelni@pelni.com', 'NamaPic' => 'Indra', 'NoHpPic' => '081199001122'],
            ['Nama' => 'PT. Logistik Indonesia', 'Alamat' => 'Jalan Perdana, Jakarta Timur', 'NoHp' => '021 2901122', 'Email' => 'logistik@indo.com', 'NamaPic' => 'Kiki', 'NoHpPic' => '081111223344'],
            ['Nama' => 'PT. Krakatau Steel', 'Alamat' => 'Jalan Industri, Cilegon', 'NoHp' => '0254 391122', 'Email' => 'krakatau@steel.com', 'NamaPic' => 'Bambang', 'NoHpPic' => '081122334455'],
            ['Nama' => 'PT. Indonesia Fiber', 'Alamat' => 'Jalan Sudirman, Jakarta Pusat', 'NoHp' => '021 5201122', 'Email' => 'indo@fiber.com', 'NamaPic' => 'Andri', 'NoHpPic' => '081133445566'],
            ['Nama' => 'PT. Armada Maritim', 'Alamat' => 'Jalan Sultan Agung, Jakarta Selatan', 'NoHp' => '021 8301122', 'Email' => 'armada@maritim.com', 'NamaPic' => 'Iwan', 'NoHpPic' => '081144556677'],
            ['Nama' => 'PT. Anugerah Jaya', 'Alamat' => 'Jalan Letjen Suprapto, Jakarta Pusat', 'NoHp' => '021 4201122', 'Email' => 'anugerah@jaya.com', 'NamaPic' => 'Syarif', 'NoHpPic' => '081155667788'],
            ['Nama' => 'PT. Deli Surya', 'Alamat' => 'Jalan MT Haryono, Jakarta Selatan', 'NoHp' => '021 8291122', 'Email' => 'deli@surya.com', 'NamaPic' => 'Maya', 'NoHpPic' => '081166778899'],
            ['Nama' => 'PT. Sinar Mas', 'Alamat' => 'Jalan MH Thamrin, Jakarta Pusat', 'NoHp' => '021 3191122', 'Email' => 'sinar@mas.com', 'NamaPic' => 'Ferry', 'NoHpPic' => '081177889900'],
            ['Nama' => 'PT. Media Utama', 'Alamat' => 'Jalan HR Rasuna Said, Jakarta Selatan', 'NoHp' => '021 5291122', 'Email' => 'media@utama.com', 'NamaPic' => 'Dina', 'NoHpPic' => '081188990011'],
        ];

        foreach ($vendors as $data) {
            DB::table('master_vendors')->insert([
                'Jenis' => 'Medis',
                'Nama' => $data['Nama'],
                'Alamat' => $data['Alamat'],
                'NoHp' => $data['NoHp'],
                'Email' => $data['Email'],
                'NamaPic' => $data['NamaPic'],
                'NoHpPic' => $data['NoHpPic'],
                'Status' => 'Y',
                'UserCreate' => 'system',
                'UserUpdate' => 'system',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
