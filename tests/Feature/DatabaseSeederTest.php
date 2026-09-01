<?php

use App\Models\AdminProfile;
use App\Models\DosenProfile;
use App\Models\Fakultas;
use App\Models\MahasiswaProfile;
use App\Models\ProgramStudi;
use App\Models\User;
use App\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

it('seeds role users without duplicates', function () {
    $this->seed();
    $this->seed();

    expect(User::count())->toBe(153)
        ->and(AdminProfile::count())->toBe(1)
        ->and(DosenProfile::count())->toBe(51)
        ->and(MahasiswaProfile::count())->toBe(101)
        ->and(Fakultas::count())->toBe(2)
        ->and(ProgramStudi::count())->toBe(4)
        ->and(Fakultas::where('kode_fakultas', 'FTI')->whereHas('dekan')->whereHas('programStudis', fn ($query) => $query->where('kode_prodi', 'TI-S1')->whereHas('ketuaProgramStudi'))->exists())->toBeTrue()
        ->and(ProgramStudi::where('kode_prodi', 'AK-S1')->whereHas('fakultas', fn ($query) => $query->where('kode_fakultas', 'FEB'))->whereHas('ketuaProgramStudi')->exists())->toBeTrue()
        ->and(User::where('role', Role::Dosen->value)->count())->toBe(51)
        ->and(User::where('role', Role::Mahasiswa->value)->count())->toBe(101)
        ->and(User::where('username', 'admin')->whereHas('adminProfile', fn ($query) => $query->whereKeyNot(0))->exists())->toBeTrue()
        ->and(User::where('username', 'dosen1')->whereHas('dosenProfile', fn ($query) => $query->whereNotNull('nidn')->whereNotNull('tanggal_lahir'))->exists())->toBeTrue()
        ->and(User::where('username', 'mahasiswa1')->whereHas('mahasiswaProfile', fn ($query) => $query->whereNotNull('nim')->whereNotNull('alamat'))->exists())->toBeTrue()
        ->and(Hash::check('11111', User::where('username', 'admin')->value('password')))->toBeTrue()
        ->and(Hash::check('22222', User::where('username', '22222')->value('password')))->toBeTrue()
        ->and(Hash::check('33333', User::where('username', '33333')->value('password')))->toBeTrue();
});
