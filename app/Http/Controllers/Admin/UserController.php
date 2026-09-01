<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DosenProfile;
use App\Models\User;
use App\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class UserController extends Controller
{
    private const COMMON_PROFILE_FIELDS = ['tempat_lahir', 'tanggal_lahir', 'jenis_kelamin', 'agama', 'no_telepon', 'alamat', 'kewarganegaraan'];

    public function index(Request $request, string $type): Response
    {
        $role = $this->role($type);
        $users = User::query()->where('role', $role)
            ->with($this->profileRelation($role))
            ->select(['id', 'name', 'username', 'email', 'role'])->orderBy('name')->paginate(10)->withQueryString()
            ->through(fn (User $user): array => $user->only(['id', 'name', 'username', 'email']) + ['profile' => $user->profile?->toArray()]);

        return Inertia::render('Admin/Users', ['title' => 'Manage User - '.ucfirst($type), 'type' => $type, 'users' => $users]);
    }

    public function create(string $type): Response
    {
        $this->role($type);

        return Inertia::render('Admin/UserForm', ['title' => 'Tambah User - '.ucfirst($type), 'type' => $type, 'user' => null, 'dosenWali' => $type === 'mahasiswa' ? DosenProfile::with('user:id,name')->get(['id', 'user_id'])->map(fn (DosenProfile $profile): array => ['id' => $profile->id, 'name' => $profile->user->name]) : []]);
    }

    public function edit(string $type, User $user): Response
    {
        $role = $this->role($type);
        abort_unless($user->role === $role, 404);
        $user->load($this->profileRelation($role));

        return Inertia::render('Admin/UserForm', ['title' => 'Edit User - '.ucfirst($type), 'type' => $type, 'user' => $user->only(['id', 'name', 'username', 'email']) + ($user->profile?->toArray() ?? []), 'dosenWali' => $type === 'mahasiswa' ? DosenProfile::with('user:id,name')->get(['id', 'user_id'])->map(fn (DosenProfile $profile): array => ['id' => $profile->id, 'name' => $profile->user->name]) : []]);
    }

    public function store(Request $request, string $type): RedirectResponse
    {
        $role = $this->role($type);
        $data = $request->validate($this->rules(null, $role));
        try {
            $user = User::create($this->userData($data) + ['role' => $role]);
            $user->profile()->create($this->profileData($data, $role));
        } catch (Throwable) {
            return to_route('admin.users.'.$type)->with('error', 'User gagal ditambahkan.');
        }

        return to_route('admin.users.'.$type)->with('success', 'User berhasil ditambahkan.');
    }

    public function update(Request $request, string $type, User $user): RedirectResponse
    {
        $role = $this->role($type);
        abort_unless($user->role === $role, 404);
        $data = $request->validate($this->rules($user, $role));
        try {
            DB::transaction(function () use ($user, $data, $role): void {
                $user->update($this->userData($data));
                $user->profile()->updateOrCreate([], $this->profileData($data, $role));
            });
        } catch (Throwable) {
            return to_route('admin.users.'.$type)->with('error', 'User gagal diperbarui.');
        }

        return to_route('admin.users.'.$type)->with('success', 'User berhasil diperbarui.');
    }

    public function destroy(string $type, User $user): RedirectResponse
    {
        $role = $this->role($type);
        abort_unless($user->role === $role, 404);

        try {
            DB::transaction(fn (): ?bool => $user->delete());
        } catch (Throwable) {
            return to_route('admin.users.'.$type)->with('error', 'User gagal dihapus.');
        }

        return to_route('admin.users.'.$type)->with('success', 'User berhasil dihapus.');
    }

    private function role(string $type): Role
    {
        return match ($type) {
            'dosen' => Role::Dosen,
            'mahasiswa' => Role::Mahasiswa,
            'karyawan' => Role::Admin,
            default => abort(404),
        };
    }

    private function profileRelation(Role $role): string
    {
        return match ($role) {
            Role::Admin => 'adminProfile', Role::Dosen => 'dosenProfile', Role::Mahasiswa => 'mahasiswaProfile'
        };
    }

    private function userData(array $data): array
    {
        return array_intersect_key($data, array_flip(['name', 'username', 'email', 'password']));
    }

    private function profileData(array $data, Role $role): array
    {
        $fields = self::COMMON_PROFILE_FIELDS;
        $fields[] = match ($role) {
            Role::Admin => 'nomor_induk', Role::Dosen => 'nidn', Role::Mahasiswa => 'nim'
        };
        if ($role === Role::Dosen) {
            $fields = [...$fields, 'jabatan_fungsional', 'pendidikan_terakhir', 'status_kepegawaian'];
        }
        if ($role === Role::Mahasiswa) {
            $fields = [...$fields, 'angkatan', 'semester', 'status', 'dosen_wali_id', 'sekolah_asal', 'nisn', 'email_alternatif', 'nama_ayah_kandung', 'nama_ibu_kandung'];
        }

        return array_intersect_key($data, array_flip(array_filter($fields)));
    }

    private function rules(?User $user, Role $role): array
    {
        $rules = ['name' => ['required', 'string', 'max:255'], 'username' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($user)], 'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user)], 'password' => [$user ? 'nullable' : 'required', 'string', 'min:8', 'confirmed']];
        foreach (self::COMMON_PROFILE_FIELDS as $field) {
            $rules[$field] = ['required', 'string', 'max:1000'];
        }
        $rules['jenis_kelamin'] = ['required', 'in:Laki-laki,Perempuan'];
        $rules['agama'] = ['required', 'in:Islam,Kristen Protestan,Katolik,Hindu,Buddha,Konghucu'];
        if ($role === Role::Dosen) {
            $rules += ['nidn' => ['required', 'string', 'max:50', Rule::unique('dosen_profiles')->ignore($user?->dosenProfile?->id)], 'jabatan_fungsional' => ['required', 'string', 'max:100'], 'pendidikan_terakhir' => ['required', 'string', 'max:100'], 'status_kepegawaian' => ['required', 'string', 'max:100']];
        }
        if ($role === Role::Mahasiswa) {
            $rules += ['nim' => ['nullable', 'string', 'max:50', Rule::unique('mahasiswa_profiles')->ignore($user?->mahasiswaProfile?->id)], 'angkatan' => ['required', 'integer'], 'semester' => ['required', 'integer'], 'status' => ['required', 'in:Aktif,Nonaktif,Lulus,Dropout,Cuti,Mengundurkan Diri,Meninggal,Transfer Masuk'], 'dosen_wali_id' => ['required', 'exists:dosen_profiles,id'], 'sekolah_asal' => ['required', 'string', 'max:255'], 'nisn' => ['required', 'string', 'max:50'], 'email_alternatif' => ['required', 'email', 'max:255'], 'nama_ayah_kandung' => ['required', 'string', 'max:255'], 'nama_ibu_kandung' => ['required', 'string', 'max:255']];
        }
        $rules['tanggal_lahir'] = ['required', 'date'];

        return $rules;
    }
}
