<?php

use App\Models\User;
use App\Role;

it('shows filtered users with pagination and admin under karyawan', function () {
    $admin = User::factory()->create(['role' => Role::Admin]);
    User::factory()->count(11)->create(['role' => Role::Dosen]);
    User::factory()->create(['role' => Role::Mahasiswa]);

    $response = $this->actingAs($admin)->get(route('admin.users.dosen'));

    $response->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Admin/Users')
            ->has('users.data', 10)
            ->where('users.total', 11)
            ->where('users.data.0.email', fn ($email) => is_string($email))
        );

    $response = $this->actingAs($admin)->get(route('admin.users.karyawan'));

    $response->assertInertia(fn ($page) => $page
        ->has('users.data', 1)
        ->where('users.data.0.email', $admin->email)
    );
});

it('creates dosen and edits users with self-excluded unique fields', function () {
    $admin = User::factory()->create(['role' => Role::Admin]);
    $dosen = User::factory()->create(['role' => Role::Dosen]);

    $this->actingAs($admin)->post(route('admin.users.dosen.store'), [
        'name' => 'Dosen Baru', 'username' => 'dosen-baru', 'email' => 'baru@example.com',
        'password' => 'password123', 'password_confirmation' => 'password123',
        'nomor_induk' => '19876543', 'tempat_lahir' => 'Bandung', 'tanggal_lahir' => '2000-01-02',
        'jenis_kelamin' => 'Laki-laki', 'agama' => 'Islam', 'no_telepon' => '08123456789',
        'alamat' => 'Jl. Merdeka', 'kewarganegaraan' => 'Indonesia',
    ])->assertRedirect();

    $this->actingAs($admin)->put(route('admin.users.dosen.update', $dosen), [
        'name' => 'Dosen Diubah', 'username' => $dosen->username, 'email' => $dosen->email,
    ])->assertRedirect();

    expect(User::find($dosen->id)->name)->toBe('Dosen Diubah');
});

it('validates unique nomor induk and persists profile fields', function () {
    $admin = User::factory()->create(['role' => Role::Admin]);
    $existing = User::factory()->create(['role' => Role::Dosen, 'nomor_induk' => '12345678']);

    $this->actingAs($admin)->post(route('admin.users.mahasiswa.store'), [
        'name' => 'Mahasiswa Baru', 'username' => 'mhs-baru', 'email' => 'mhs@example.com',
        'nomor_induk' => $existing->nomor_induk, 'password' => 'password123',
        'password_confirmation' => 'password123',
    ])->assertSessionHasErrors('nomor_induk');

    $this->actingAs($admin)->post(route('admin.users.mahasiswa.store'), [
        'name' => 'Mahasiswa Baru', 'username' => 'mhs-baru', 'email' => 'mhs@example.com',
        'nomor_induk' => '87654321', 'tanggal_lahir' => '2001-03-04', 'alamat' => 'Jl. Baru',
        'password' => 'password123', 'password_confirmation' => 'password123',
    ])->assertRedirect();

    $user = User::where('username', 'mhs-baru')->firstOrFail();
    expect($user->nomor_induk)->toBe('87654321')->and($user->tanggal_lahir->toDateString())->toBe('2001-03-04')->and($user->alamat)->toBe('Jl. Baru');
});

it('blocks non-admin users from user management pages', function () {
    $user = User::factory()->create(['role' => Role::Dosen]);

    foreach (['dosen', 'mahasiswa', 'karyawan'] as $type) {
        $this->actingAs($user)->get(route('admin.users.'.$type))->assertForbidden();
    }
});
