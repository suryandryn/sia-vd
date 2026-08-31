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
        Schema::table('users', function (Blueprint $table) {
            $table->string('nomor_induk')->nullable()->after('username');
            $table->string('tempat_lahir')->nullable()->after('nomor_induk');
            $table->date('tanggal_lahir')->nullable()->after('tempat_lahir');
            $table->string('jenis_kelamin')->nullable()->after('tanggal_lahir');
            $table->string('agama')->nullable()->after('jenis_kelamin');
            $table->string('no_telepon')->nullable()->after('agama');
            $table->text('alamat')->nullable()->after('no_telepon');
            $table->string('kewarganegaraan')->nullable()->after('alamat');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'nomor_induk', 'tempat_lahir', 'tanggal_lahir', 'jenis_kelamin',
                'agama', 'no_telepon', 'alamat', 'kewarganegaraan',
            ]);
        });
    }
};
