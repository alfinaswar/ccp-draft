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
        Schema::create('hta_dan_gpas', function (Blueprint $table) {
            $table->id();
            $table->string('JenisForm')->nullable();
            $table->string('IdPengajuan')->nullable();
            $table->string('PengajuanItemId')->nullable();
            $table->string('IdVendor')->nullable();
            $table->string('IdBarang')->nullable();
            $table->string('DiajukanOleh')->nullable();
            $table->dateTime('DiajukanPada')->nullable();
            $table->string('File')->nullable();
            $table->string('Penilai1_Oleh')->nullable();
            $table->enum('Penilai1_Status', ['P', 'Y', 'N']);
            $table->dateTime('Penilai1_Pada')->nullable();

            $table->string('Penilai2_Oleh')->nullable();
            $table->enum('Penilai2_Status', ['P', 'Y', 'N']);
            $table->dateTime('Penilai2_Pada')->nullable();

            $table->string('Penilai3_Oleh')->nullable();
            $table->enum('Penilai3_Status', ['P', 'Y', 'N']);
            $table->dateTime('Penilai3_Pada')->nullable();

            $table->string('Penilai4_Oleh')->nullable();
            $table->enum('Penilai4_Status', ['P', 'Y', 'N']);
            $table->dateTime('Penilai4_Pada')->nullable();

            $table->string('Penilai5_Oleh')->nullable();
            $table->enum('Penilai5_Status', ['P', 'Y', 'N']);
            $table->dateTime('Penilai5_Pada')->nullable();
            $table->enum('Status', ['Draft', 'Diajukan', 'Final', 'Disetujui'])->default('Draft');
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
        Schema::dropIfExists('hta_dan_gpas');
    }
};
