<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\MasterPerusahaan;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            PermissionTableSeeder::class,
            CreateAdminUserSeeder::class,
            MasterSatuanSeeder::class,
            MasterMerekSeeder::class,
            MasterParameterSeeder::class,
            MasterPerusahaanSeeder::class,
            DepartemenSeeder::class,
            JabatanSeeder::class,
            CountriesTableSeeder::class,
            JenisPengajuanSeeder::class,
            MasterFormSeeder::class,
            UserSeeder::class,
            MasterVendorSeeder::class,

        ]);
    }
}
