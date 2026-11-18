<?php

namespace Database\Seeders;

use App\Models\MasterForm;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class KomplainVendorSeeder extends Seeder
{
    public function run(): void
    {
        // Form baru agar Kepala Divisi dan Group Head dapat diatur lewat menu Master Approval
        MasterForm::firstOrCreate(
            ['Nama' => 'Komplain Vendor'],
            ['UserCreate' => 'Administrator']
        );

        // Role GH CCP (Group Head Corporate Procurement)
        Role::firstOrCreate(['name' => 'GH CCP', 'guard_name' => 'web']);
    }
}
