<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        return; /* legacy migration replaced by role profile migration */
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('nomor_induk')->nullable()->unique();
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('jenis_kelamin')->nullable();
            $table->string('agama')->nullable();
            $table->string('no_telepon')->nullable();
            $table->text('alamat')->nullable();
            $table->string('kewarganegaraan')->nullable();
            $table->timestamps();
        });

        $columns = ['nomor_induk', 'tempat_lahir', 'tanggal_lahir', 'jenis_kelamin', 'agama', 'no_telepon', 'alamat', 'kewarganegaraan'];
        DB::table('users')->select(['id', ...$columns])->orderBy('id')->chunkById(100, function ($users) use ($columns): void {
            foreach ($users as $user) {
                DB::table('user_profiles')->insert(array_merge(['user_id' => $user->id], collect($columns)->mapWithKeys(fn (string $column): array => [$column => $user->{$column}])->all(), ['created_at' => now(), 'updated_at' => now()]));
            }
        });

        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn(['nomor_induk', 'tempat_lahir', 'tanggal_lahir', 'jenis_kelamin', 'agama', 'no_telepon', 'alamat', 'kewarganegaraan']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_profiles');
    }
};
