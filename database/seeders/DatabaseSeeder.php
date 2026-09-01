<?php

namespace Database\Seeders;

use App\Models\User;
use App\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $kota = ['Jakarta', 'Bandung', 'Surabaya', 'Yogyakarta', 'Semarang', 'Malang', 'Bogor', 'Depok', 'Tangerang', 'Makassar'];
        $agama = ['Islam', 'Kristen Protestan', 'Katolik', 'Hindu', 'Buddha'];
        $jenisKelamin = ['Laki-laki', 'Perempuan'];
        $sekolah = ['SMA Negeri 1 Bandung', 'SMA Negeri 3 Jakarta', 'SMK Negeri 2 Yogyakarta', 'SMA Negeri 5 Surabaya'];

        $admin = User::updateOrCreate(['username' => 'admin'], [
            'name' => 'Administrator',
            'email' => 'admin@example.com',
            'password' => Hash::make('11111'),
            'role' => Role::Admin,
        ]);
        $admin->adminProfile()->updateOrCreate([], [
            'tempat_lahir' => 'Jakarta', 'tanggal_lahir' => '1985-01-10', 'jenis_kelamin' => 'Laki-laki',
            'agama' => 'Islam', 'no_telepon' => '081234567890', 'alamat' => 'Jl. Merdeka No. 1, Jakarta', 'kewarganegaraan' => 'Indonesia',
        ]);

        $dosenProfiles = [];
        $dosen = User::updateOrCreate(['username' => '22222'], ['name' => 'Budi Santoso', 'email' => '22222@example.com', 'password' => Hash::make('22222'), 'role' => Role::Dosen]);
        $dosenProfiles[] = $dosen->dosenProfile()->updateOrCreate([], ['nidn' => 'D22222', 'tempat_lahir' => 'Bandung', 'tanggal_lahir' => '1980-02-22', 'jenis_kelamin' => 'Laki-laki', 'agama' => 'Islam', 'no_telepon' => '081222222222', 'alamat' => 'Jl. Dosen No. 22, Bandung', 'kewarganegaraan' => 'Indonesia', 'jabatan_fungsional' => 'Lektor', 'pendidikan_terakhir' => 'S3', 'status_kepegawaian' => 'Tetap']);

        for ($index = 1; $index <= 50; $index++) {
            $username = 'dosen'.$index;
            $user = User::updateOrCreate(['username' => $username], ['name' => ['Andi', 'Siti', 'Rizky', 'Dewi', 'Agus'][$index % 5].' '.['Pratama', 'Lestari', 'Wijaya', 'Permata', 'Hidayat'][$index % 5], 'email' => $username.'@example.com', 'password' => Hash::make($username), 'role' => Role::Dosen]);
            $dosenProfiles[] = $user->dosenProfile()->updateOrCreate([], ['nidn' => 'D'.str_pad((string) $index, 5, '0', STR_PAD_LEFT), 'tempat_lahir' => $kota[$index % count($kota)], 'tanggal_lahir' => '198'.($index % 10).'-'.str_pad((string) (($index - 1) % 12 + 1), 2, '0', STR_PAD_LEFT).'-15', 'jenis_kelamin' => $jenisKelamin[$index % 2], 'agama' => $agama[$index % count($agama)], 'no_telepon' => '0812'.str_pad((string) $index, 8, '0', STR_PAD_LEFT), 'alamat' => 'Jl. Pendidikan No. '.$index.', '.$kota[$index % count($kota)], 'kewarganegaraan' => 'Indonesia', 'jabatan_fungsional' => 'Asisten Ahli', 'pendidikan_terakhir' => $index % 2 ? 'S2' : 'S3', 'status_kepegawaian' => 'Tetap']);
        }

        $mahasiswa = User::updateOrCreate(['username' => '33333'], ['name' => 'Citra Maharani', 'email' => '33333@example.com', 'password' => Hash::make('33333'), 'role' => Role::Mahasiswa]);
        $mahasiswa->mahasiswaProfile()->updateOrCreate([], ['nim' => '33333', 'angkatan' => 2023, 'semester' => 6, 'status' => 'Aktif', 'tempat_lahir' => 'Jakarta', 'tanggal_lahir' => '2003-03-03', 'jenis_kelamin' => 'Perempuan', 'agama' => 'Islam', 'no_telepon' => '081333333333', 'alamat' => 'Jl. Mahasiswa No. 33, Jakarta', 'kewarganegaraan' => 'Indonesia', 'dosen_wali_id' => $dosenProfiles[0]->id, 'sekolah_asal' => $sekolah[0], 'nisn' => '0033333333', 'email_alternatif' => 'citra@gmail.com', 'nama_ayah_kandung' => 'Hendra Maharani', 'nama_ibu_kandung' => 'Lina Maharani']);

        for ($index = 1; $index <= 100; $index++) {
            $username = 'mahasiswa'.$index;
            $user = User::updateOrCreate(['username' => $username], ['name' => ['Fajar', 'Nabila', 'Dimas', 'Putri', 'Bagas'][$index % 5].' '.['Saputra', 'Anggraini', 'Kurniawan', 'Salsabila', 'Ramadhan'][$index % 5], 'email' => $username.'@example.com', 'password' => Hash::make($username), 'role' => Role::Mahasiswa]);
            $user->mahasiswaProfile()->updateOrCreate([], ['nim' => 'M'.str_pad((string) $index, 5, '0', STR_PAD_LEFT), 'angkatan' => 2022 + ($index % 3), 'semester' => 2 + ($index % 8), 'status' => 'Aktif', 'tempat_lahir' => $kota[$index % count($kota)], 'tanggal_lahir' => '200'.($index % 6).'-'.str_pad((string) (($index - 1) % 12 + 1), 2, '0', STR_PAD_LEFT).'-'.str_pad((string) (($index - 1) % 25 + 1), 2, '0', STR_PAD_LEFT), 'jenis_kelamin' => $jenisKelamin[$index % 2], 'agama' => $agama[$index % count($agama)], 'no_telepon' => '0821'.str_pad((string) $index, 8, '0', STR_PAD_LEFT), 'alamat' => 'Jl. Pelajar No. '.$index.', '.$kota[$index % count($kota)], 'kewarganegaraan' => 'Indonesia', 'dosen_wali_id' => $dosenProfiles[$index % count($dosenProfiles)]->id, 'sekolah_asal' => $sekolah[$index % count($sekolah)], 'nisn' => '00'.str_pad((string) $index, 8, '0', STR_PAD_LEFT), 'email_alternatif' => $username.'@mail.com', 'nama_ayah_kandung' => 'Joko '.$user->name, 'nama_ibu_kandung' => 'Sari '.$user->name]);
        }
    }
}
