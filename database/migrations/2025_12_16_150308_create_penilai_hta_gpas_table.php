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
        Schema::create('penilai_hta_gpas', function (Blueprint $table) {
            $table->id();
            $table->string('IdHtaGpa')->nullable();
            $table->string('TipeInputPenilai')->nullable();
            $table->string('IdUser')->nullable();
            $table->string('PenilaiKe')->nullable();
            $table->string('Nama')->nullable();
            $table->string('Jabatan')->nullable();
            $table->string('Email')->nullable();
            $table->string('ApprovalToken')->nullable();
            $table->string('UserAcc')->nullable();
            $table->string('AccPada')->nullable();
            $table->enum('StatusAcc', ['Y', 'N'])->nullable();
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
        Schema::dropIfExists('penilai_hta_gpas');
    }
};
