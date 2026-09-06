<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kelas_kuliah', function (Blueprint $table): void {
            $table->id();
            $table->string('kode_kelas')->unique();
            $table->string('tahun_ajaran');
            $table->unsignedSmallInteger('kapasitas');
            $table->foreignId('dosen_id')->constrained('dosen_profiles')->cascadeOnDelete();
            $table->foreignId('matkul_id')->constrained('mata_kuliahs')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kelas_kuliah');
    }
};
