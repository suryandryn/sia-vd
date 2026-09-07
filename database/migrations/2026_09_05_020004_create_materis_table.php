<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('materis', function (Blueprint $table): void {
            $table->id();
            $table->string('judul_materi', 255);
            $table->unsignedTinyInteger('pertemuan_ke');
            $table->string('file', 255)->nullable();
            $table->text('catatan')->nullable();
            $table->foreignId('uploaded_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('kelas_id')->constrained('kelas_kuliah')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('materis');
    }
};
