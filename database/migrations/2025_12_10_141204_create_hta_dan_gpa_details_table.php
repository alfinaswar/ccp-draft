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
        Schema::create('hta_dan_gpa_details', function (Blueprint $table) {
            $table->id();
            $table->string('IdHtaGpa')->nullable();
            $table->string('IdPengajuan')->nullable();
            $table->string('PengajuanItemId')->nullable();
            $table->string('IdVendor')->nullable();
            $table->string('IdBarang')->nullable();
            $table->json('IdParameter')->nullable();
            $table->json('Parameter')->nullable();
            $table->json('Deskripsi')->nullable();
            $table->json('Nilai1')->nullable();
            $table->json('Nilai2')->nullable();
            $table->json('Nilai3')->nullable();
            $table->json('Nilai4')->nullable();
            $table->json('Nilai5')->nullable();
            $table->json('SubTotal')->nullable();
            $table->string('UmurEkonomis')->nullable();
            $table->string('BuybackPeriod')->nullable();
            $table->string('TarifDiusulkan')->nullable();
            $table->string('TargetPemakaianBulanan')->nullable();
            $table->text('Keterangan')->nullable();
            $table->text('File')->nullable();
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
        Schema::dropIfExists('hta_dan_gpa_details');
    }
};
