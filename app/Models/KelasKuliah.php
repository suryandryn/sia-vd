<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KelasKuliah extends Model
{
    protected $table = 'kelas_kuliah';

    protected $fillable = ['kode_kelas', 'tahun_ajaran', 'kapasitas', 'dosen_id', 'matkul_id'];

    protected function casts(): array
    {
        return [
            'kapasitas' => 'integer',
        ];
    }

    public function dosen(): BelongsTo
    {
        return $this->belongsTo(DosenProfile::class, 'dosen_id');
    }

    public function mataKuliah(): BelongsTo
    {
        return $this->belongsTo(MataKuliah::class, 'matkul_id');
    }
}
