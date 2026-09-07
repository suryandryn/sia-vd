<?php

use App\Models\Fakultas;
use App\Models\KelasKuliah;
use App\Models\MataKuliah;
use App\Models\Materi;
use App\Models\ProgramStudi;
use App\Models\User;
use App\Role;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

function createMateriKelasKuliah(): KelasKuliah
{
    $dosen = User::factory()->create(['role' => Role::Dosen]);
    $fakultas = Fakultas::create(['kode_fakultas' => 'FTI', 'nama_fakultas' => 'Fakultas Teknologi Informasi', 'dekan_id' => $dosen->dosenProfile->id, 'tanggal_berdiri' => '2001-08-17', 'no_telp' => '021-5551001', 'email' => 'fti@example.ac.id']);
    $prodi = ProgramStudi::create(['fakultas_id' => $fakultas->id, 'kode_prodi' => 'TI-S1', 'nama_prodi' => 'Teknik Informatika', 'jenjang' => 'S1', 'status_akreditasi' => 'Unggul', 'tanggal_akreditasi_mulai' => '2022-06-01', 'tanggal_akreditasi_akhir' => '2027-06-01', 'kaprodi' => $dosen->dosenProfile->id, 'tahun_berdiri' => 2001]);
    $matkul = MataKuliah::create(['kode_matkul' => 'IF101', 'nama_matkul' => 'Algoritma dan Pemrograman', 'sks' => 3, 'semester' => 1, 'jenis' => 'Wajib', 'prodi_id' => $prodi->id]);

    return KelasKuliah::create(['kode_kelas' => 'IF101-A', 'tahun_ajaran' => '2025/2026 Ganjil', 'kapasitas' => 30, 'dosen_id' => $dosen->dosenProfile->id, 'matkul_id' => $matkul->id]);
}

it('stores materi files with sanitized original name plus random suffix', function () {
    Storage::fake('public');
    $admin = User::factory()->create(['role' => Role::Admin]);
    $kelas = createMateriKelasKuliah();

    $this->actingAs($admin)->post(route('admin.kelas-kuliah.materi.store', $kelas), [
        'judul_materi' => 'Pengantar',
        'pertemuan_ke' => 1,
        'jenis' => 'Materi',
        'file' => [UploadedFile::fake()->create('Modul Pertemuan 1.pdf', 100)],
    ])->assertRedirect()->assertSessionHas('materi_success', 'Materi berhasil ditambahkan.');

    $materi = Materi::firstOrFail();
    expect($materi->jenis)->toBe('Materi');

    $files = $materi->file;
    expect($files)->toBeArray()->toHaveCount(1);

    $path = $files[0];
    $basename = basename($path);
    expect($path)->toStartWith('materis/');
    Storage::disk('public')->assertExists($path);
    expect($basename)->toContain('modul_pertemuan_1')
        ->toEndWith('.pdf')
        ->not->toBe('Modul Pertemuan 1.pdf')
        ->toMatch('/^modul_pertemuan_1-[a-z0-9]{6}\.pdf$/');
});

it('stores distinct paths when uploading the same original name twice', function () {
    Storage::fake('public');
    $admin = User::factory()->create(['role' => Role::Admin]);
    $kelas = createMateriKelasKuliah();

    $this->actingAs($admin)->post(route('admin.kelas-kuliah.materi.store', $kelas), [
        'judul_materi' => 'Pengantar',
        'pertemuan_ke' => 1,
        'jenis' => 'Pengumuman',
        'file' => [UploadedFile::fake()->create('Tugas Akhir.pdf', 100), UploadedFile::fake()->create('Tugas Akhir.pdf', 100)],
    ])->assertRedirect()->assertSessionHas('materi_success', 'Materi berhasil ditambahkan.');

    $materi = Materi::firstOrFail();
    expect($materi->jenis)->toBe('Pengumuman');
    $files = $materi->file;
    expect($files)->toBeArray()->toHaveCount(2);
    expect($files[0])->not->toBe($files[1]);

    foreach ($files as $path) {
        Storage::disk('public')->assertExists($path);
        expect(basename($path))->toMatch('/^tugas_akhir-[a-z0-9]{6}\.pdf$/');
    }
});
