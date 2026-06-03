<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MasterSatuanSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('master_satuans')->insert([
            // --- Satuan Umum ---
            ['NamaSatuan' => 'Pcs', 'UserCreate' => 'System', 'UserUpdate' => 'System'],
            ['NamaSatuan' => 'Box', 'UserCreate' => 'System', 'UserUpdate' => 'System'],
            ['NamaSatuan' => 'Pack', 'UserCreate' => 'System', 'UserUpdate' => 'System'],
            ['NamaSatuan' => 'Unit', 'UserCreate' => 'System', 'UserUpdate' => 'System'],
            ['NamaSatuan' => 'Set', 'UserCreate' => 'System', 'UserUpdate' => 'System'],
            ['NamaSatuan' => 'Roll', 'UserCreate' => 'System', 'UserUpdate' => 'System'],
            ['NamaSatuan' => 'Dus', 'UserCreate' => 'System', 'UserUpdate' => 'System'],
            ['NamaSatuan' => 'Lembar', 'UserCreate' => 'System', 'UserUpdate' => 'System'],
            ['NamaSatuan' => 'Rim', 'UserCreate' => 'System', 'UserUpdate' => 'System'],
            ['NamaSatuan' => 'Karton', 'UserCreate' => 'System', 'UserUpdate' => 'System'],

            // --- Satuan Cair & Berat ---
            ['NamaSatuan' => 'Ml', 'UserCreate' => 'System', 'UserUpdate' => 'System'],
            ['NamaSatuan' => 'Liter', 'UserCreate' => 'System', 'UserUpdate' => 'System'],
            ['NamaSatuan' => 'Gram', 'UserCreate' => 'System', 'UserUpdate' => 'System'],
            ['NamaSatuan' => 'Kg', 'UserCreate' => 'System', 'UserUpdate' => 'System'],

            // --- Satuan Medis ---
            ['NamaSatuan' => 'Ampul', 'UserCreate' => 'System', 'UserUpdate' => 'System'],
            ['NamaSatuan' => 'Vial', 'UserCreate' => 'System', 'UserUpdate' => 'System'],
            ['NamaSatuan' => 'Tablet', 'UserCreate' => 'System', 'UserUpdate' => 'System'],
            ['NamaSatuan' => 'Kapsul', 'UserCreate' => 'System', 'UserUpdate' => 'System'],
            ['NamaSatuan' => 'Botol', 'UserCreate' => 'System', 'UserUpdate' => 'System'],
            ['NamaSatuan' => 'Tube', 'UserCreate' => 'System', 'UserUpdate' => 'System'],
            ['NamaSatuan' => 'Sachet', 'UserCreate' => 'System', 'UserUpdate' => 'System'],
            ['NamaSatuan' => 'Strip', 'UserCreate' => 'System', 'UserUpdate' => 'System'],
            ['NamaSatuan' => 'Blister', 'UserCreate' => 'System', 'UserUpdate' => 'System'],
            ['NamaSatuan' => 'Jarum', 'UserCreate' => 'System', 'UserUpdate' => 'System'],
            ['NamaSatuan' => 'Masker', 'UserCreate' => 'System', 'UserUpdate' => 'System'],
            ['NamaSatuan' => 'Sarung Tangan', 'UserCreate' => 'System', 'UserUpdate' => 'System'],
            ['NamaSatuan' => 'Kantong', 'UserCreate' => 'System', 'UserUpdate' => 'System'],
            ['NamaSatuan' => 'Meter', 'UserCreate' => 'System', 'UserUpdate' => 'System'],
            ['NamaSatuan' => 'Cm', 'UserCreate' => 'System', 'UserUpdate' => 'System'],
        ]);
    }
}
