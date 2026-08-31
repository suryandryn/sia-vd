<?php

use App\Models\User;
use App\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

it('seeds role users without duplicates', function () {
    $this->seed();
    $this->seed();

    expect(User::count())->toBe(153)
        ->and(User::where('role', Role::Dosen->value)->count())->toBe(51)
        ->and(User::where('role', Role::Mahasiswa->value)->count())->toBe(101)
        ->and(User::where('username', 'admin')->whereNull('nomor_induk')->exists())->toBeTrue()
        ->and(User::where('username', 'dosen1')->whereNotNull('nomor_induk')->whereNotNull('tanggal_lahir')->exists())->toBeTrue()
        ->and(User::where('username', 'mahasiswa1')->whereNotNull('nomor_induk')->whereNotNull('alamat')->exists())->toBeTrue()
        ->and(Hash::check('22222', User::where('username', '22222')->value('password')))->toBeTrue()
        ->and(Hash::check('33333', User::where('username', '33333')->value('password')))->toBeTrue();
});
