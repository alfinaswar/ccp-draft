<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('usulan_investasis', function (Blueprint $table) {
            $table->id();
            $table->string('JenisForm')->nullable();
            $table->string('IdPengajuan')->nullable();
            $table->string('PengajuanItemId')->nullable();
            $table->string('IdVendor')->nullable();
            $table->string('IdBarang')->nullable();
            // DepartemenTerkait
            $table->date('Tanggal')->nullable();
            $table->string('NamaKadiv')->nullable();
            $table->string('Divisi')->nullable();
            $table->string('Kategori')->nullable();
            // DepartemenPembelian
            $table->date('Tanggal2')->nullable();
            $table->string('NamaKadiv2')->nullable();
            $table->string('Divisi2')->nullable();
            $table->string('Kategori2')->nullable();
            // alasn
            $table->text('Alasan')->nullable();
            $table->string('BiayaAkhir')->nullable();
            $table->string('VendorDipilih')->nullable();
            $table->string('HargaDiskonPpn')->nullable();
            $table->string('Total')->nullable();
            $table->enum('SudahRkap', ['Y', 'N'])->nullable();
            $table->string('SisaBudget')->nullable();
            $table->enum('SudahRkap2', ['Y', 'N'])->nullable();
            $table->string('SisaBudget2')->nullable();
            $table->string('DiajukanOleh')->nullable();
            $table->string('DiajukanPada')->nullable();
            $table->string('KodePerusahaan')->nullable();
            $table->enum('Status', ['Draft', 'Selesai', 'Disetujui'])->default('Draft')->nullable();
            $table->string('Direktur')->nullable();
            $table->dateTime('DirekturPada')->nullable();
            $table->string('KadivJangMed')->nullable();
            $table->dateTime('KadivJangMedPada')->nullable();
            $table->string('UserCreate')->nullable();
            $table->string('UserUpdate')->nullable();
            $table->string('UserDelete')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usulan_inventasis');
    }
};
