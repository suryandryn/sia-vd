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
        Schema::create('program_studis', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('fakultas_id')->constrained('fakultas')->cascadeOnDelete();
            $table->string('kode_prodi')->unique();
            $table->string('nama_prodi');
            $table->string('jenjang');
            $table->string('status_akreditasi');
            $table->string('no_sk_akreditasi')->nullable();
            $table->date('tanggal_akreditasi_mulai');
            $table->date('tanggal_akreditasi_akhir');
            $table->foreignId('kaprodi')->constrained('dosen_profiles');
            $table->unsignedSmallInteger('tahun_berdiri');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('program_studis');
    }
};
