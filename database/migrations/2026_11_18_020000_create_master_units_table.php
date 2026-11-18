<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('master_units', function (Blueprint $table) {
            $table->id();
            $table->string('nama_unit');
            $table->string('kode_unit', 20)->nullable();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->enum('Aktif', ['Y', 'N'])->default('Y')->nullable();
            $table->string('UserCreate')->nullable();
            $table->string('UserUpdate')->nullable();
            $table->string('UserDelete')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('master_units');
    }
};
