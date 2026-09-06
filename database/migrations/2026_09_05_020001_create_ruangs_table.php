<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ruangs', function (Blueprint $table): void {
            $table->id();
            $table->string('kode_ruang')->unique();
            $table->string('nama_ruang');
            $table->unsignedSmallInteger('kapasitas');
            $table->text('detail')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ruangs');
    }
};
