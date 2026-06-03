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
        Schema::create('rekomendasis', function (Blueprint $table) {
            $table->id();
            $table->string('JenisForm')->nullable();
            $table->string('IdPengajuan')->nullable();
            $table->string('PengajuanItemId')->nullable();
            $table->date('TanggalPresentasi')->nullable();
            $table->string('VendorAcc')->nullable();
            $table->string('UserNego')->nullable();
            $table->string('DisetujuiOleh')->nullable();
            $table->timestamp('DisetujuiPada')->nullable();
            $table->enum('Status', ['Draft', 'Disetujui'])->nullable();
            $table->string('File')->nullable();
            $table->string('KodePerusahaan')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rekomendasis');
    }
};
