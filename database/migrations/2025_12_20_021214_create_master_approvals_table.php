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
        Schema::create('master_approvals', function (Blueprint $table) {
            $table->id();
            $table->string('KodePerusahaan')->nullable();
            $table->string('JenisForm')->nullable();
            $table->string('JabatanId')->nullable();
            $table->string('DepartemenId')->nullable();
            $table->string('UserId')->nullable();
            $table->integer('Urutan')->nullable();
            $table->enum('Wajib', ['Y', 'N'])->default('N')->nullable();
            $table->enum('Aktif', ['Y', 'N'])->default('Y')->nullable();
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
        Schema::dropIfExists('master_approvals');
    }
};
