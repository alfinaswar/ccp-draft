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
        Schema::create('master_vendors', function (Blueprint $table) {
            $table->id();
            $table->string('Jenis', 255)->nullable();
            $table->string('Nama', 255)->nullable();
            $table->string('Alamat', 255)->nullable();
            $table->string('NoHp', 100)->nullable();
            $table->string('Email', 150)->nullable();
            $table->string('NamaPic', 150)->nullable();
            $table->string('NoHpPic', 100)->nullable();
            $table->enum('Status', ['Y', 'N'])->nullable();
            $table->string('Npwp')->nullable();
            $table->string('Nib')->nullable();
            $table->string('UserCreate', 255)->nullable();
            $table->string('UserUpdate', 255)->nullable();
            $table->string('UserDelete', 255)->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_vendors');
    }
};
