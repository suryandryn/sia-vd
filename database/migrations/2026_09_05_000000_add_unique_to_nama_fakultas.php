<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fakultas', function (Blueprint $table): void {
            $table->unique('nama_fakultas');
        });
    }

    public function down(): void
    {
        Schema::table('fakultas', function (Blueprint $table): void {
            $table->dropUnique(['nama_fakultas']);
        });
    }
};
