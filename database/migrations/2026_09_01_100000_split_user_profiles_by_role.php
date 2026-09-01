<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $profileFields = ['tempat_lahir', 'tanggal_lahir', 'jenis_kelamin', 'agama', 'no_telepon', 'alamat', 'kewarganegaraan'];

        Schema::create('admin_profiles', function (Blueprint $table): void {
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
        Schema::create('dosen_profiles', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('nidn')->nullable()->unique();
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('jenis_kelamin')->nullable();
            $table->string('agama')->nullable();
            $table->string('no_telepon')->nullable();
            $table->text('alamat')->nullable();
            $table->string('kewarganegaraan')->nullable();
            $table->string('jabatan_fungsional')->nullable();
            $table->string('pendidikan_terakhir')->nullable();
            $table->string('status_kepegawaian')->nullable();
            $table->timestamps();
        });
        Schema::create('mahasiswa_profiles', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('nim')->nullable()->unique();
            $table->unsignedSmallInteger('angkatan')->nullable();
            $table->unsignedTinyInteger('semester')->nullable();
            $table->string('status')->nullable();
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('jenis_kelamin')->nullable();
            $table->string('agama')->nullable();
            $table->string('no_telepon')->nullable();
            $table->text('alamat')->nullable();
            $table->string('kewarganegaraan')->nullable();
            $table->string('sekolah_asal')->nullable();
            $table->string('nisn')->nullable();
            $table->string('email_alternatif')->nullable();
            $table->string('nama_ayah_kandung')->nullable();
            $table->string('nama_ibu_kandung')->nullable();
            $table->foreignId('dosen_wali_id')->nullable()->constrained('dosen_profiles')->nullOnDelete();
            $table->timestamps();
        });

        if (Schema::hasTable('user_profiles')) {
            DB::table('user_profiles')->orderBy('id')->get()->each(function (object $profile) use ($profileFields): void {
                $user = DB::table('users')->where('id', $profile->user_id)->first();
                $data = collect($profileFields)->mapWithKeys(fn (string $field): array => [$field => $profile->{$field}])->all();
                $data['user_id'] = $profile->user_id;
                $data['created_at'] = $profile->created_at;
                $data['updated_at'] = $profile->updated_at;
                $table = $user->role === 'admin' ? 'admin_profiles' : ($user->role === 'dosen' ? 'dosen_profiles' : 'mahasiswa_profiles');
                $data[$user->role === 'dosen' ? 'nidn' : 'nim'] = $profile->nomor_induk;
                DB::table($table)->insert($data);
            });
            Schema::drop('user_profiles');
        }

        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn(['nomor_induk', 'tempat_lahir', 'tanggal_lahir', 'jenis_kelamin', 'agama', 'no_telepon', 'alamat', 'kewarganegaraan']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mahasiswa_profiles');
        Schema::dropIfExists('dosen_profiles');
        Schema::dropIfExists('admin_profiles');
    }
};
