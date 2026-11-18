<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('master_parameter_komplains', function (Blueprint $table) {
            $table->id();
            $table->enum('Jenis', ['ParameterEvaluasi', 'DataPendukung', 'LangkahAwal', 'TindakanCcp'])->nullable();
            $table->string('Nama');
            $table->integer('Urutan')->default(0)->nullable();
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
        Schema::dropIfExists('master_parameter_komplains');
    }
};
