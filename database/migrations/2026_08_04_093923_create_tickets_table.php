<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('TiketTrouble', function (Blueprint $table) {
            $table->id();
            $table->string('KodeTiket')->nullable();
            $table->string('Nama');
            $table->string('NoHp')->nullable();
            $table->enum('Prioritas', [
                'Rendah',
                'Sedang',
                'Tinggi',
                'Darurat'
            ])->default('Rendah');
            $table->string('Judul');
            $table->text('Deskripsi');
            $table->enum('Status', [
                'Open',
                'In Progress',
                'Completed',
                'Closed'
            ])->default('Open');
            $table->string('FilePendukung')->nullable();
            $table->string('KodePerusahaan')->nullable();
            $table->text('Respon')->nullable();
            $table->string('LampiranRespon')->nullable();
            $table->string('DiresponOleh')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('TiketTrouble');
    }
};
