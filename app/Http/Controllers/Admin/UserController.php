<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    public function index(Request $request, string $type): Response
    {
        $role = match ($type) {
            'dosen' => Role::Dosen,
            'mahasiswa' => Role::Mahasiswa,
            'karyawan' => Role::Admin,
            default => abort(404),
        };

        $users = User::query()
            ->where('role', $role)
            ->select(['id', 'name', 'username', 'email', 'nomor_induk', 'tempat_lahir', 'tanggal_lahir', 'jenis_kelamin', 'agama', 'no_telepon', 'alamat', 'kewarganegaraan'])
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString()
            ->through(fn (User $user): array => [
                'id' => $user->id,
                'name' => $user->name,
                'username' => $user->username,
                'email' => $user->email,
                'nomor_induk' => $user->nomor_induk,
                'nomor_induk' => $user->nomor_induk,
                'tempat_lahir' => $user->tempat_lahir,
                'tanggal_lahir' => $user->tanggal_lahir?->format('Y-m-d'),
                'jenis_kelamin' => $user->jenis_kelamin,
                'agama' => $user->agama,
                'no_telepon' => $user->no_telepon,
                'alamat' => $user->alamat,
                'kewarganegaraan' => $user->kewarganegaraan,
            ]);

        return Inertia::render('Admin/Users', [
            'title' => 'Manage User - '.ucfirst($type),
            'type' => $type,
            'users' => $users,
        ]);
    }

    public function create(string $type): Response
    {
        $this->role($type, ['dosen', 'mahasiswa']);

        return Inertia::render('Admin/UserForm', [
            'title' => 'Tambah User - '.ucfirst($type),
            'type' => $type,
            'user' => null,
        ]);
    }

    public function edit(string $type, User $user): Response
    {
        $role = $this->role($type);
        abort_unless($user->role === $role, 404);

        return Inertia::render('Admin/UserForm', [
            'title' => 'Edit User - '.ucfirst($type),
            'type' => $type,
            'user' => $user->only(['id', 'name', 'username', 'email', 'nomor_induk', 'tempat_lahir', 'tanggal_lahir', 'jenis_kelamin', 'agama', 'no_telepon', 'alamat', 'kewarganegaraan']),
        ]);
    }

    public function store(Request $request, string $type): RedirectResponse
    {
        $role = $this->role($type, ['dosen', 'mahasiswa']);
        $data = $request->validate($this->rules());
        $data['role'] = $role;
        User::create($data);

        return to_route('admin.users.'.$type);
    }

    public function update(Request $request, string $type, User $user): RedirectResponse
    {
        $role = $this->role($type);
        abort_unless($user->role === $role, 404);
        $data = $request->validate($this->rules($user));
        $user->update($data);

        return to_route('admin.users.'.$type);
    }

    private function role(string $type, array $allowed = ['dosen', 'mahasiswa', 'karyawan']): Role
    {
        abort_unless(in_array($type, $allowed, true), 404);

        return match ($type) {
            'dosen' => Role::Dosen,
            'mahasiswa' => Role::Mahasiswa,
            'karyawan' => Role::Admin,
        };
    }

    private function rules(?User $user = null): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', Rule::unique('users', 'username')->ignore($user)],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user)],
            'password' => [$user ? 'nullable' : 'required', 'string', 'min:8', 'confirmed'],
            'nomor_induk' => ['nullable', 'string', 'max:50', Rule::unique('users', 'nomor_induk')->ignore($user)],
            'tempat_lahir' => ['nullable', 'string', 'max:255'],
            'tanggal_lahir' => ['nullable', 'date'],
            'jenis_kelamin' => ['nullable', 'string', 'max:50'],
            'agama' => ['nullable', 'string', 'max:50'],
            'no_telepon' => ['nullable', 'string', 'max:30'],
            'alamat' => ['nullable', 'string', 'max:1000'],
            'kewarganegaraan' => ['nullable', 'string', 'max:100'],
        ];
    }
}
