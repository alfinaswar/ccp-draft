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
        Schema::create('rekomendasi_details', function (Blueprint $table) {
            $table->id();
            $table->string('VendorAcc')->nullable();
            $table->string('IdPengajuan')->nullable();
            $table->string('PengajuanItemId');
            $table->string('IdRekomendasi');
            $table->string('IdVendor');
            $table->string('NamaPermintaan');
            $table->string('HargaAwal')->nullable();
            $table->string('HargaNego')->nullable();
            $table->longtext('Spesifikasi')->nullable();
            $table->string('NegaraProduksi')->nullable();
            $table->string('Garansi')->nullable();
            $table->string('Teknisi')->nullable();
            $table->longtext('Bmhp')->nullable();
            $table->string('Populasi')->nullable();
            $table->string('SparePart')->nullable();
            $table->string('BackupUnit')->nullable();
            $table->text('Top')->nullable();
            $table->string('Rekomendasi')->nullable();
            $table->string('TimeLinePekerjaan')->nullable();
            $table->string('JumlahPekerja')->nullable();
            $table->string('Luasan')->nullable();
            $table->string('ReviewVendor')->nullable();
            $table->string('File')->nullable();
            $table->string('UserNego')->nullable();
            $table->text('Keterangan')->nullable();
            $table->string('Disetujui')->nullable();
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
        Schema::dropIfExists('rekomendasi_details');
    }
};
