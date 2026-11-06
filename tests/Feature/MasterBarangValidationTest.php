<?php

namespace Tests\Feature;

use App\Models\MasterBarang;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class MasterBarangValidationTest extends TestCase
{
    use DatabaseTransactions;

    private function authenticateUser()
    {
        $user = User::first() ?? User::factory()->create([
            'kodeperusahaan' => 'KP01',
            'name' => 'Tester',
        ]);
        $this->actingAs($user);
        return $user;
    }

    public function test_cannot_input_if_nama_merek_and_tipe_are_all_identical()
    {
        $this->authenticateUser();

        MasterBarang::create([
            'KodeBarang' => 'TEST0001',
            'Nama' => 'Laptop Pro',
            'Jenis' => '1',
            'Satuan' => '1',
            'Merek' => '10',
            'Tipe' => 'Core i7',
            'KodePerusahaan' => 'KP01',
            'UserCreate' => 'Tester',
        ]);

        $response = $this->post(route('barang.store'), [
            'Nama' => 'Laptop Pro',
            'Jenis' => '1',
            'Satuan' => '1',
            'Merek' => '10',
            'Tipe' => 'Core i7',
        ]);

        $response->assertSessionHasErrors('Nama');
    }

    public function test_can_input_if_nama_is_same_but_merek_and_tipe_are_different()
    {
        $this->authenticateUser();

        MasterBarang::create([
            'KodeBarang' => 'TEST0002',
            'Nama' => 'Laptop Pro',
            'Jenis' => '1',
            'Satuan' => '1',
            'Merek' => '10',
            'Tipe' => 'Core i7',
            'KodePerusahaan' => 'KP01',
            'UserCreate' => 'Tester',
        ]);

        $response = $this->post(route('barang.store'), [
            'Nama' => 'Laptop Pro',
            'Jenis' => '1',
            'Satuan' => '1',
            'Merek' => '20',
            'Tipe' => 'Ryzen 7',
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('master_barangs', [
            'Nama' => 'Laptop Pro',
            'Merek' => '20',
            'Tipe' => 'Ryzen 7',
        ]);
    }

    public function test_can_input_if_nama_and_merek_are_same_but_tipe_is_different()
    {
        $this->authenticateUser();

        MasterBarang::create([
            'KodeBarang' => 'TEST0003',
            'Nama' => 'Laptop Pro',
            'Jenis' => '1',
            'Satuan' => '1',
            'Merek' => '10',
            'Tipe' => 'Core i7',
            'KodePerusahaan' => 'KP01',
            'UserCreate' => 'Tester',
        ]);

        $response = $this->post(route('barang.store'), [
            'Nama' => 'Laptop Pro',
            'Jenis' => '1',
            'Satuan' => '1',
            'Merek' => '10',
            'Tipe' => 'Core i9',
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('master_barangs', [
            'Nama' => 'Laptop Pro',
            'Merek' => '10',
            'Tipe' => 'Core i9',
        ]);
    }
}
