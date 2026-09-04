<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('program_studis', function (Blueprint $table): void {
            $table->date('tanggal_akreditasi_mulai')->nullable()->change();
            $table->date('tanggal_akreditasi_akhir')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('program_studis', function (Blueprint $table): void {
            $table->date('tanggal_akreditasi_mulai')->nullable(false)->change();
            $table->date('tanggal_akreditasi_akhir')->nullable(false)->change();
        });
    }
};
