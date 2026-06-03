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
        Schema::create('pengajuan_pembelians', function (Blueprint $table) {
            $table->id();
            $table->string('IdPermintaan')->nullable();
            $table->string('KodePengajuan')->nullable();
            $table->date('Tanggal')->nullable();
            $table->string('Jenis')->nullable();
            $table->string('Tujuan')->nullable();
            $table->string('PerkiraanUtilitasiBulanan')->nullable();
            $table->string('PerkiraanBepPadaTahun')->nullable();
            $table->string('Rkap')->nullable();
            $table->string('NominalRkap')->nullable();
            $table->enum('Status', [
                'Draft',
                'Diajukan',
                'Dalam Review',
                'Selesai',
                'Disetujui',
                'Siap Presentasi',
                'Selesai Review',
                'Menunggu Rekomendasi GH',
                'Ditolak',
                'Disetujui CEO',
                'Ditolak CEO',
            ])->nullable()->default('Draft');
            $table->string('DiajukanOleh', 100)->nullable();
            $table->string('DepartemenId', 100)->nullable();
            $table->timestamp('DiajukanPada')->nullable();
            $table->text('Keterangan')->nullable();
            $table->string('DisetujuiOleh')->nullable();
            $table->string('DisetujuiPada')->nullable();
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
        Schema::dropIfExists('pengajuan_pembelians');
    }
};
