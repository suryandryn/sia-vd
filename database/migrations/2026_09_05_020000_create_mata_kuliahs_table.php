<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mata_kuliahs', function (Blueprint $table): void {
            $table->id();
            $table->string('kode_matkul')->unique();
            $table->string('nama_matkul');
            $table->unsignedTinyInteger('sks');
            $table->unsignedTinyInteger('semester');
            $table->string('jenis');
            $table->foreignId('prodi_id')->constrained('program_studis')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mata_kuliahs');
    }
};
