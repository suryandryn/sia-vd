<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mahasiswa_profiles', function (Blueprint $table): void {
            $table->date('tanggal_lahir_ayah')->nullable()->after('nama_ibu_kandung');
            $table->date('tanggal_lahir_ibu')->nullable()->after('tanggal_lahir_ayah');
            $table->string('pendidikan_terakhir_ayah')->nullable()->after('tanggal_lahir_ibu');
            $table->string('pendidikan_terakhir_ibu')->nullable()->after('pendidikan_terakhir_ayah');
            $table->string('pekerjaan_ayah')->nullable()->after('pendidikan_terakhir_ibu');
            $table->string('pekerjaan_ibu')->nullable()->after('pekerjaan_ayah');
            $table->string('penghasilan_ayah')->nullable()->after('pekerjaan_ibu');
            $table->string('penghasilan_ibu')->nullable()->after('penghasilan_ayah');
            $table->string('no_telepon_ayah')->nullable()->after('penghasilan_ibu');
            $table->string('no_telepon_ibu')->nullable()->after('no_telepon_ayah');
            $table->string('email_ayah')->nullable()->after('no_telepon_ibu');
            $table->string('email_ibu')->nullable()->after('email_ayah');
        });
    }

    public function down(): void
    {
        Schema::table('mahasiswa_profiles', function (Blueprint $table): void {
            $table->dropColumn([
                'tanggal_lahir_ayah',
                'tanggal_lahir_ibu',
                'pendidikan_terakhir_ayah',
                'pendidikan_terakhir_ibu',
                'pekerjaan_ayah',
                'pekerjaan_ibu',
                'penghasilan_ayah',
                'penghasilan_ibu',
                'no_telepon_ayah',
                'no_telepon_ibu',
                'email_ayah',
                'email_ibu',
            ]);
        });
    }
};
