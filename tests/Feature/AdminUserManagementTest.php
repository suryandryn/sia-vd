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

    $payload = [
        'name' => 'Dosen Baru', 'username' => 'dosen-baru', 'email' => 'baru@example.com',
        'password' => 'password123', 'password_confirmation' => 'password123',
        'nidn' => '99999999', 'tempat_lahir' => 'Bandung', 'tanggal_lahir' => '2000-01-02',
        'jenis_kelamin' => 'Laki-laki', 'agama' => 'Islam', 'no_telepon' => '08123456789',
        'alamat' => 'Jl. Merdeka', 'kewarganegaraan' => 'Indonesia',
        'jabatan_fungsional' => 'Asisten Ahli', 'pendidikan_terakhir' => 'S2',
        'status_kepegawaian' => 'Tetap',
    ];

    $this->actingAs($admin)->post(route('admin.users.dosen.store'), $payload)->assertRedirect()->assertSessionHas('success', 'User berhasil ditambahkan.');

    $this->actingAs($admin)->put(route('admin.users.dosen.update', $dosen), array_replace($payload, [
        'name' => 'Dosen Diubah', 'username' => $dosen->username, 'email' => $dosen->email, 'nidn' => $dosen->dosenProfile->nidn,
    ]))->assertRedirect()->assertSessionDoesntHaveErrors()->assertSessionHas('success', 'User berhasil diperbarui.');

    expect(User::find($dosen->id)->name)->toBe('Dosen Diubah');

    $this->actingAs($admin)->delete(route('admin.users.dosen.destroy', $dosen))->assertRedirect()->assertSessionHas('success', 'User berhasil dihapus.');
});

it('validates required profile fields and new dropdown values', function () {
    $admin = User::factory()->create(['role' => Role::Admin]);
    $this->actingAs($admin)->post(route('admin.users.dosen.store'), [])->assertSessionHasErrors([
        'name', 'username', 'email', 'tempat_lahir', 'tanggal_lahir', 'jenis_kelamin', 'agama',
        'no_telepon', 'alamat', 'kewarganegaraan', 'nidn', 'jabatan_fungsional',
        'pendidikan_terakhir', 'status_kepegawaian', 'password',
    ]);

    $dosenWali = User::factory()->create(['role' => Role::Dosen]);
    $mahasiswaPayload = [
        'name' => 'Mahasiswa Lengkap', 'username' => 'mhs-lengkap', 'email' => 'lengkap@example.com',
        'nim' => '11111111', 'angkatan' => 2024, 'semester' => 2, 'status' => 'Aktif',
        'dosen_wali_id' => $dosenWali->dosenProfile->id, 'sekolah_asal' => 'SMA Negeri 1',
        'nisn' => '1234567890', 'email_alternatif' => 'alt@example.com',
        'nama_ayah_kandung' => 'Ayah', 'nama_ibu_kandung' => 'Ibu',
        'tempat_lahir' => 'Jakarta', 'tanggal_lahir' => '2001-03-04', 'jenis_kelamin' => 'Perempuan',
        'agama' => 'Kristen Protestan', 'no_telepon' => '08123456789', 'alamat' => 'Jl. Baru',
        'kewarganegaraan' => 'Indonesia', 'password' => 'password123', 'password_confirmation' => 'password123',
    ];
    $this->actingAs($admin)->post(route('admin.users.mahasiswa.store'), $mahasiswaPayload)->assertRedirect();

    $existing = User::factory()->create(['role' => Role::Mahasiswa]);
    $existing->profile->update(['nim' => '12345678']);

    $this->actingAs($admin)->post(route('admin.users.mahasiswa.store'), array_replace($mahasiswaPayload, [
        'nim' => $existing->profile->nim, 'username' => 'mhs-baru', 'email' => 'mhs@example.com',
    ]))->assertSessionHasErrors('nim');

    $this->actingAs($admin)->post(route('admin.users.mahasiswa.store'), array_replace($mahasiswaPayload, [
        'nim' => '87654321', 'username' => 'mhs-baru', 'email' => 'mhs@example.com',
        'name' => 'Mahasiswa Baru',
    ]))->assertRedirect();

    $user = User::where('username', 'mhs-baru')->firstOrFail();
    expect($user->profile->nim)->toBe('87654321')->and($user->profile->tanggal_lahir->toDateString())->toBe('2001-03-04')->and($user->profile->alamat)->toBe('Jl. Baru');
});

it('blocks non-admin users from user management pages', function () {
    $user = User::factory()->create(['role' => Role::Dosen]);

    foreach (['dosen', 'mahasiswa', 'karyawan'] as $type) {
        $this->actingAs($user)->get(route('admin.users.'.$type))->assertForbidden();
    }
});
