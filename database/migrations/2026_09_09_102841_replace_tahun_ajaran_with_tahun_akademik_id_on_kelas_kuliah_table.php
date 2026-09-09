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
        Schema::table('kelas_kuliah', function (Blueprint $table): void {
            $table->foreignId('tahun_akademik_id')->nullable()->after('kode_kelas')->constrained('tahun_akademik');
        });

        $tahunAkademikIds = [];
        foreach (DB::table('kelas_kuliah')->select('tahun_ajaran')->distinct()->pluck('tahun_ajaran') as $tahunAjaran) {
            [$tahun, $semester] = array_pad(explode(' ', $tahunAjaran, 2), 2, 'Ganjil');
            $tahunAkademikIds[$tahunAjaran] = DB::table('tahun_akademik')->insertGetId([
                'tahun' => $tahun,
                'semester' => $semester,
                'tanggal_mulai' => $semester === 'Genap' ? $tahun.'-02-01' : substr($tahun, 0, 4).'-08-01',
                'tanggal_akhir' => $semester === 'Genap' ? substr($tahun, -4).'-07-31' : substr($tahun, -4).'-01-31',
                'status' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        foreach ($tahunAkademikIds as $tahunAjaran => $id) {
            DB::table('kelas_kuliah')->where('tahun_ajaran', $tahunAjaran)->update(['tahun_akademik_id' => $id]);
        }

        Schema::table('kelas_kuliah', function (Blueprint $table): void {
            $table->dropColumn('tahun_ajaran');
        });
    }

    public function down(): void
    {
        Schema::table('kelas_kuliah', function (Blueprint $table): void {
            $table->string('tahun_ajaran')->nullable();
        });

        DB::table('kelas_kuliah')->orderBy('id')->each(function (object $kelas): void {
            $tahunAkademik = DB::table('tahun_akademik')->where('id', $kelas->tahun_akademik_id)->first();
            DB::table('kelas_kuliah')->where('id', $kelas->id)->update(['tahun_ajaran' => $tahunAkademik?->tahun.' '.$tahunAkademik?->semester]);
        });

        Schema::table('kelas_kuliah', function (Blueprint $table): void {
            $table->dropForeign(['tahun_akademik_id']);
            $table->dropColumn('tahun_akademik_id');
        });
    }
};
