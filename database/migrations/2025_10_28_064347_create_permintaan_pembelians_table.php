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
        Schema::create('permintaan_pembelians', function (Blueprint $table) {
            $table->id();
            $table->string('JenisForm')->nullable();
            $table->string('NomorPermintaan')->nullable();
            $table->string('Jenis')->nullable();
            $table->string('Tujuan')->nullable();
            $table->string('Departemen')->nullable();
            $table->date('Tanggal')->nullable();
            $table->string('DiajukanOleh')->nullable();
            $table->dateTime('DiajukanPada')->nullable();
            $table->enum('Status', ['Draft', 'Sudah Diajukan', 'Telah Disetujui', 'Dalam Proses Review', 'Selesai'])->default('Draft');
            $table->dateTime('StatusUpdate')->nullable();

            // --- Mengetahui Kepala Divisi 1 ---
            $table->unsignedBigInteger('KepalaDivisi_Oleh')->nullable();
            $table->enum('KepalaDivisi_Status', ['P', 'Y', 'N'])->default('P');
            $table->dateTime('KepalaDivisi_Pada')->nullable();

            // --- Mengetahui Kepala Divisi Penunjang Medis / Umum ---
            $table->unsignedBigInteger('Penunjang_Oleh')->nullable();
            $table->enum('Penunjang_Status', ['P', 'Y', 'N'])->default('P');
            $table->dateTime('Penunjang_Pada')->nullable();

            // --- Disetujui oleh Direktur ---
            $table->unsignedBigInteger('Direktur_Oleh')->nullable();
            $table->enum('Direktur_Status', ['P', 'Y', 'N'])->default('P');
            $table->dateTime('Direktur_Pada')->nullable();

            // --- Diterima oleh Logistik/SMI ---
            $table->unsignedBigInteger('Logistik_Oleh')->nullable();
            $table->enum('Logistik_Status', ['P', 'Y', 'N'])->default('P');
            $table->dateTime('Logistik_Pada')->nullable();

            $table->string('KodePerusahaan')->nullable();
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
        Schema::dropIfExists('permintaan_pembelians');
    }
};
