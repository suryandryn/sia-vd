<?php

namespace Database\Seeders;

use App\Models\User;
use App\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::upsert([
            ['name' => 'Administrator', 'username' => 'admin', 'email' => 'admin@example.com', 'password' => Hash::make('11111'), 'role' => Role::Admin, 'nomor_induk' => null, 'tempat_lahir' => null, 'tanggal_lahir' => null, 'jenis_kelamin' => null, 'agama' => null, 'no_telepon' => null, 'alamat' => null, 'kewarganegaraan' => null],
            ['name' => 'Dosen', 'username' => '22222', 'email' => '22222@example.com', 'password' => Hash::make('22222'), 'role' => Role::Dosen, 'nomor_induk' => 'D22222', 'tempat_lahir' => 'Bandung', 'tanggal_lahir' => '1980-02-22', 'jenis_kelamin' => 'Laki-laki', 'agama' => 'Islam', 'no_telepon' => '081222222222', 'alamat' => 'Jl. Dosen 22222', 'kewarganegaraan' => 'Indonesia'],
            ['name' => 'Mahasiswa', 'username' => '33333', 'email' => '33333@example.com', 'password' => Hash::make('33333'), 'role' => Role::Mahasiswa, 'nomor_induk' => '33333', 'tempat_lahir' => 'Jakarta', 'tanggal_lahir' => '2003-03-03', 'jenis_kelamin' => 'Perempuan', 'agama' => 'Islam', 'no_telepon' => '081333333333', 'alamat' => 'Jl. Mahasiswa 33333', 'kewarganegaraan' => 'Indonesia'],
        ], ['username'], ['name', 'email', 'password', 'role', 'nomor_induk', 'tempat_lahir', 'tanggal_lahir', 'jenis_kelamin', 'agama', 'no_telepon', 'alamat', 'kewarganegaraan']);

        foreach ([
            Role::Dosen->value => 50,
            Role::Mahasiswa->value => 100,
        ] as $role => $count) {
            for ($index = 1; $index <= $count; $index++) {
                $username = $role.$index;
                $user = User::factory()->make([
                    'username' => $username,
                    'email' => $username.'@example.com',
                    'role' => Role::from($role),
                    'nomor_induk' => $role === Role::Dosen->value ? 'D'.str_pad((string) $index, 5, '0', STR_PAD_LEFT) : 'M'.str_pad((string) $index, 5, '0', STR_PAD_LEFT),
                    'tempat_lahir' => 'Bandung',
                    'tanggal_lahir' => $role === Role::Dosen->value ? '1980-01-'.str_pad((string) (($index - 1) % 28 + 1), 2, '0', STR_PAD_LEFT) : '2003-01-'.str_pad((string) (($index - 1) % 28 + 1), 2, '0', STR_PAD_LEFT),
                    'jenis_kelamin' => $index % 2 === 0 ? 'Perempuan' : 'Laki-laki',
                    'agama' => 'Islam',
                    'no_telepon' => '0812'.str_pad((string) $index, 8, '0', STR_PAD_LEFT),
                    'alamat' => 'Jl. Pendidikan No. '.$index,
                    'kewarganegaraan' => 'Indonesia',
                ]);

                User::updateOrCreate(['username' => $username], $user->getAttributes());
            }
        }
    }
}
