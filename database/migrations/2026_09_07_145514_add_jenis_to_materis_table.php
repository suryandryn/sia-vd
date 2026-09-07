<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('materis', function (Blueprint $table): void {
            $table->string('jenis', 20)->default('Materi')->after('pertemuan_ke');
        });
    }

    public function down(): void
    {
        Schema::table('materis', function (Blueprint $table): void {
            $table->dropColumn('jenis');
        });
    }
};
