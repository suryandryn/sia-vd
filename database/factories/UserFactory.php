<?php

namespace Database\Factories;

use App\Models\User;
use App\Role;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function configure(): static
    {
        return $this->afterCreating(function (User $user): void {
            $base = [
                'tempat_lahir' => fake()->city(),
                'tanggal_lahir' => fake()->date(),
                'jenis_kelamin' => fake()->randomElement(['Laki-laki', 'Perempuan']),
                'agama' => 'Islam',
                'no_telepon' => fake()->phoneNumber(),
                'alamat' => fake()->address(),
                'kewarganegaraan' => 'Indonesia',
            ];
            if ($user->role === Role::Mahasiswa) {
                $base += [
                    'nim' => fake()->unique()->numerify('########'),
                    'angkatan' => 2024, 'semester' => 2, 'status' => 'Aktif',
                    'dosen_wali_id' => \App\Models\DosenProfile::query()->inRandomOrder()->value('id'),
                    'prodi_id' => \App\Models\ProgramStudi::query()->inRandomOrder()->value('id'),
                    'sekolah_asal' => fake()->company(),
                    'nisn' => fake()->numerify('##########'),
                    'email_alternatif' => fake()->unique()->safeEmail(),
                    'nama_ayah_kandung' => fake()->name('male'), 'tanggal_lahir_ayah' => fake()->date(), 'pendidikan_terakhir_ayah' => 'S1', 'pekerjaan_ayah' => 'PNS', 'penghasilan_ayah' => '5-10 Juta', 'no_telepon_ayah' => fake()->phoneNumber(), 'email_ayah' => fake()->unique()->safeEmail(), 'alamat_ayah' => fake()->address(),
                    'nama_ibu_kandung' => fake()->name('female'), 'tanggal_lahir_ibu' => fake()->date(), 'pendidikan_terakhir_ibu' => 'S1', 'pekerjaan_ibu' => 'Guru', 'penghasilan_ibu' => '5-10 Juta', 'no_telepon_ibu' => fake()->phoneNumber(), 'email_ibu' => fake()->unique()->safeEmail(), 'alamat_ibu' => fake()->address(),
                ];
            } elseif ($user->role === Role::Dosen) {
                $base += ['nidn' => fake()->unique()->numerify('########')];
            }

            ($user->role === Role::Admin ? $user->adminProfile() : ($user->role === Role::Dosen ? $user->dosenProfile() : $user->mahasiswaProfile()))->create($base);
        });
    }

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'username' => fake()->unique()->userName(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'role' => Role::Mahasiswa,
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
