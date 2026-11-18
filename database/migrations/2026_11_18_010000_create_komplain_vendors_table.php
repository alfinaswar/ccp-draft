<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('komplain_vendors', function (Blueprint $table) {
            $table->id();
            $table->string('KodeKomplain')->nullable();
            $table->date('Tanggal')->nullable();

            // Kop formulir
            $table->string('NamaRS')->nullable();
            $table->string('UnitDepartemen')->nullable();

            // A. Identitas pelapor
            $table->string('NamaPelapor')->nullable();
            $table->string('Jabatan')->nullable();
            $table->string('UnitKerja')->nullable();

            // B. Data BMHP umum
            $table->string('NamaBarang')->nullable();
            $table->string('JenisKategori')->nullable();
            $table->string('Merk')->nullable();
            $table->string('Distributor')->nullable();

            // C. Parameter evaluasi (checkbox) + lainnya
            $table->json('ParameterEvaluasi')->nullable();
            $table->string('ParameterLainnya')->nullable();

            // D. Deskripsi evaluasi
            $table->text('UraianSingkat')->nullable();
            $table->json('DataPendukung')->nullable();
            $table->string('DataPendukungLainnya')->nullable();

            // E. Tindak lanjut logistik umum
            $table->json('LangkahAwal')->nullable();
            $table->string('LangkahAwalLainnya')->nullable();
            $table->string('LogistikUmumOleh')->nullable();
            $table->timestamp('TanggalLogistikUmum')->nullable();

            // F. Tindak lanjut CCP
            $table->json('TindakanCcp')->nullable();
            $table->string('TindakanCcpLainnya')->nullable();
            $table->string('CcpOleh')->nullable();
            $table->timestamp('TanggalCcp')->nullable();

            $table->enum('Status', [
                'Menunggu TTD Kepala Divisi',
                'Menunggu TTD Group Head',
                'Menunggu Tindak Lanjut Logistik Umum',
                'Menunggu Tindakan CCP',
                'Selesai',
            ])->default('Menunggu TTD Kepala Divisi')->nullable();

            $table->string('KodePerusahaan')->nullable();
            $table->string('UserCreate')->nullable();
            $table->string('UserUpdate')->nullable();
            $table->string('UserDelete')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('komplain_vendors');
    }
};
