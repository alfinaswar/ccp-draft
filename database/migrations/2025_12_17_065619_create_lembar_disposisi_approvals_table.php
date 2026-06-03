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
        Schema::create('lembar_disposisi_approvals', function (Blueprint $table) {
            $table->id();
            $table->string('IdLembarDisposisi')->nullable();
            $table->string('IdUser')->nullable();
            $table->string('Nama')->nullable();
            $table->string('Email')->nullable();
            $table->string('Jabatan')->nullable();
            $table->string('Departemen')->nullable();
            $table->string('Justifikasi')->nullable();
            $table->enum('Status', ['Y', 'N'])->nullable();
            $table->dateTime('ApprovePada')->nullable();
            $table->string('ApprovalToken')->nullable();
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
        Schema::dropIfExists('lembar_disposisi_approvals');
    }
};
