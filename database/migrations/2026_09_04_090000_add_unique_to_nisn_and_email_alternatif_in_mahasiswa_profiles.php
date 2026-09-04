<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Rapikan data lama yang duplikat sebelum pasang unique (DB dev bisa berisi NISN duplikat dari seeder lama).
        $rows = DB::table('mahasiswa_profiles')->orderBy('id')->get(['id', 'nisn', 'email_alternatif']);
        $nisnSeen = [];
        $emailSeen = [];
        foreach ($rows as $row) {
            $updates = [];
            if (isset($nisnSeen[$row->nisn])) {
                $updates['nisn'] = $row->nisn.'-'.$row->id;
            } else {
                $nisnSeen[$row->nisn] = true;
            }
            if (isset($emailSeen[$row->email_alternatif])) {
                $parts = explode('@', (string) $row->email_alternatif);
                $updates['email_alternatif'] = ($parts[0] ?? 'user').'+'.$row->id.'@'.($parts[1] ?? 'example.com');
            } else {
                $emailSeen[$row->email_alternatif] = true;
            }
            if ($updates !== []) {
                DB::table('mahasiswa_profiles')->where('id', $row->id)->update($updates);
            }
        }

        Schema::table('mahasiswa_profiles', function (Blueprint $table): void {
            $table->unique('nisn');
            $table->unique('email_alternatif');
        });
    }

    public function down(): void
    {
        Schema::table('mahasiswa_profiles', function (Blueprint $table): void {
            $table->dropUnique(['nisn']);
            $table->dropUnique(['email_alternatif']);
        });
    }
};
