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
        Schema::create('krs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('mahasiswa_id')->constrained('mahasiswa_profiles')->cascadeOnDelete();
            $table->foreignId('kelas_id')->constrained('kelas_kuliah')->cascadeOnDelete();
            $table->unsignedSmallInteger('nilai')->nullable();
            $table->string('status')->default('Aktif');
            $table->unique(['mahasiswa_id', 'kelas_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('krs');
    }
};
