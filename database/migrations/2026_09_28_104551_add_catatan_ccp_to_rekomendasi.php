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
        Schema::table('rekomendasis', function (Blueprint $table) {
            $table->text('CatatanReview')->nullable()->after('File');
            $table->dateTime('JamCatatanReview')->nullable()->after('CatatanReview');
            $table->dateTime('JamCatatan')->nullable()->after('Catatan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rekomendasis', function (Blueprint $table) {
            //
        });
    }
};
