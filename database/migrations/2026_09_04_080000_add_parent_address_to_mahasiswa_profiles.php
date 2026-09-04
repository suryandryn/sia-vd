<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mahasiswa_profiles', function (Blueprint $table): void {
            $table->text('alamat_ayah')->nullable()->after('email_ibu');
            $table->text('alamat_ibu')->nullable()->after('alamat_ayah');
        });
    }

    public function down(): void
    {
        Schema::table('mahasiswa_profiles', function (Blueprint $table): void {
            $table->dropColumn(['alamat_ayah', 'alamat_ibu']);
        });
    }
};
