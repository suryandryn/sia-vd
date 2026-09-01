<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DosenProfile extends Model
{
    protected $fillable = ['user_id', 'nidn', 'tempat_lahir', 'tanggal_lahir', 'jenis_kelamin', 'agama', 'no_telepon', 'alamat', 'kewarganegaraan', 'jabatan_fungsional', 'pendidikan_terakhir', 'status_kepegawaian'];

    protected function casts(): array
    {
        return ['tanggal_lahir' => 'date'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function mahasiswaWali(): HasMany
    {
        return $this->hasMany(MahasiswaProfile::class, 'dosen_wali_id');
    }
}
