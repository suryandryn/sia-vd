<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ruang extends Model
{
    protected $table = 'ruangs';

    protected $fillable = ['kode_ruang', 'nama_ruang', 'kapasitas', 'detail'];

    protected function casts(): array
    {
        return [
            'kapasitas' => 'integer',
        ];
    }
}
