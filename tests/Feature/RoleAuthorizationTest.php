<?php

use App\Models\User;
use App\Role;

it('redirects users to role dashboard after login', function (Role $role, string $route) {
    $user = User::factory()->create(['role' => $role]);

    $response = $this->post('/login', [
        'username' => $user->username,
        'password' => 'password',
    ]);

    $response->assertRedirect(route($route, absolute: false));
})->with([
    [Role::Admin, 'admin.dashboard'],
    [Role::Dosen, 'dosen.dashboard'],
    [Role::Mahasiswa, 'mahasiswa.dashboard'],
]);

it('rejects users from other role areas', function () {
    $user = User::factory()->create(['role' => Role::Dosen]);

    $this->actingAs($user)->get('/admin')->assertForbidden();
    $this->actingAs($user)->get('/mahasiswa')->assertForbidden();
    $this->actingAs($user)->get('/dosen')->assertOk();
});

it('exposes admin user management routes only to admins', function () {
    $admin = User::factory()->create(['role' => Role::Admin]);
    $dosen = User::factory()->create(['role' => Role::Dosen]);

    foreach (['dosen', 'mahasiswa', 'karyawan'] as $userType) {
        $this->actingAs($admin)->get(route('admin.users.'.$userType))->assertOk();
        $this->actingAs($dosen)->get(route('admin.users.'.$userType))->assertForbidden();
    }
});

it('exposes role menu placeholder routes only to matching role', function () {
    $dosen = User::factory()->create(['role' => Role::Dosen]);
    $mahasiswa = User::factory()->create(['role' => Role::Mahasiswa]);

    $this->actingAs($dosen)->get(route('dosen.khs'))->assertOk();
    $this->actingAs($dosen)->get(route('dosen.kelas-kuliah.index'))->assertOk();
    $this->actingAs($dosen)->get(route('dosen.jadwal-kuliah'))->assertRedirect(route('dosen.kelas-kuliah.index', absolute: false));
    $this->actingAs($dosen)->get(route('mahasiswa.khs'))->assertForbidden();
    $this->actingAs($mahasiswa)->get(route('mahasiswa.khs'))->assertOk();
    $this->actingAs($mahasiswa)->get(route('mahasiswa.khs.transkrip-nilai'))->assertOk();
    $this->actingAs($mahasiswa)->get(route('mahasiswa.perpustakaan.pinjaman-aktif'))->assertOk();
    $this->actingAs($mahasiswa)->get(route('dosen.jadwal-kuliah'))->assertForbidden();
});
