<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jadwals', function (Blueprint $table): void {
            $table->id();
            $table->string('hari', 20);
            $table->time('jam_mulai');
            $table->time('jam_akhir');
            $table->foreignId('kelas_id')->constrained('kelas_kuliah')->cascadeOnDelete();
            $table->foreignId('ruang_id')->constrained('ruangs')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jadwals');
    }
};
