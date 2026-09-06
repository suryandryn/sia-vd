<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MataKuliah extends Model
{
    protected $table = 'mata_kuliahs';

    protected $fillable = ['kode_matkul', 'nama_matkul', 'sks', 'semester', 'jenis', 'prodi_id'];

    protected function casts(): array
    {
        return [
            'sks' => 'integer',
            'semester' => 'integer',
        ];
    }

    public function prodi(): BelongsTo
    {
        return $this->belongsTo(ProgramStudi::class, 'prodi_id');
    }
}
