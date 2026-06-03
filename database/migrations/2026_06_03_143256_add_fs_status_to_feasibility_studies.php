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
        Schema::table('feasibility_studies', function (Blueprint $table) {
            $table->enum('Status', ['Draft', 'Final'])->nullable()->default('Draft')->after('KodePerusahaan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('feasibility_studies', function (Blueprint $table) {
            //
        });
    }
};
