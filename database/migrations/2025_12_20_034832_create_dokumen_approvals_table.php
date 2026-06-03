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
        Schema::create('dokumen_approvals', function (Blueprint $table) {
            $table->id();
            $table->enum('JenisUser', ['Master', 'Manual'])->nullable();
            $table->string('JenisFormId')->nullable();
            $table->string('DokumenId')->nullable();
            $table->string('PerusahaanId')->nullable();
            $table->string('JabatanId')->nullable();
            $table->string('DepartemenId')->nullable();
            $table->string('UserId')->nullable();
            $table->string('Nama')->nullable();
            $table->string('Email')->nullable();
            $table->integer('Urutan')->nullable();
            $table->enum('Status', ['Pending', 'Approved', 'Rejected'])->nullable();
            $table->timestamp('TanggalApprove')->nullable();
            $table->text('Catatan')->nullable();
            $table->string('Ttd')->nullable();
            $table->text('ApprovalToken')->nullable();
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
        Schema::dropIfExists('dokumen_approvals');
    }
};
