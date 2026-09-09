<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tahun_akademik', function (Blueprint $table): void {
            $table->date('tanggal_krs_awal')->nullable()->after('tanggal_akhir');
            $table->date('tanggal_krs_akhir')->nullable()->after('tanggal_krs_awal');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tahun_akademik', function (Blueprint $table) {
            //
        });
    }
};
