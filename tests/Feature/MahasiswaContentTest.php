<?php

use App\Models\Krs;
use App\Models\Materi;
use App\Models\Quiz;
use App\Models\Tugas;
use App\Models\User;
use App\Role;

it('shows only content from classes taken by mahasiswa', function (string $type, string $model, string $route) {
    $takenClass = createMateriKelasKuliah();
    $otherClass = createMateriKelasKuliah();
    $mahasiswa = User::factory()->create(['role' => Role::Mahasiswa]);

    Krs::create(['mahasiswa_id' => $mahasiswa->mahasiswaProfile->id, 'kelas_id' => $takenClass->id]);
    $model::create(['kelas_id' => $takenClass->id, 'uploaded_by' => $takenClass->dosen->user_id] + match ($type) {
        'tugas' => ['judul_tugas' => 'Tugas Diambil'],
        'materi' => ['judul_materi' => 'Materi Diambil', 'pertemuan_ke' => 1, 'jenis' => 'Materi'],
        'quiz' => ['nama_quiz' => 'Quiz Diambil'],
    });

    $model::create(['kelas_id' => $otherClass->id, 'uploaded_by' => $otherClass->dosen->user_id] + match ($type) {
        'tugas' => ['judul_tugas' => 'Tugas Lain'],
        'materi' => ['judul_materi' => 'Materi Lain', 'pertemuan_ke' => 1, 'jenis' => 'Materi'],
        'quiz' => ['nama_quiz' => 'Quiz Lain'],
    });

    $this->actingAs($mahasiswa)
        ->get(route($route))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Mahasiswa/ContentIndex')
            ->where('items.total', 1)
            ->where('items.data.0.kelas_id', $takenClass->id));
})->with([
    'tugas' => ['tugas', Tugas::class, 'mahasiswa.tugas'],
    'materi' => ['materi', Materi::class, 'mahasiswa.materi'],
    'quiz' => ['quiz', Quiz::class, 'mahasiswa.quiz'],
]);

it('shows taken materi detail with its files', function () {
    $kelas = createMateriKelasKuliah();
    $mahasiswa = User::factory()->create(['role' => Role::Mahasiswa]);
    Krs::create(['mahasiswa_id' => $mahasiswa->mahasiswaProfile->id, 'kelas_id' => $kelas->id]);
    $materi = Materi::create(['kelas_id' => $kelas->id, 'uploaded_by' => $kelas->dosen->user_id, 'judul_materi' => 'Materi Detail', 'pertemuan_ke' => 2, 'jenis' => 'Materi', 'file' => ['materis/file.pdf'], 'catatan' => 'Catatan detail']);

    $this->actingAs($mahasiswa)->get(route('mahasiswa.materi.show', $materi))->assertOk()->assertInertia(fn ($page) => $page->component('Mahasiswa/MateriShow')->where('materi.id', $materi->id)->where('materi.file.0', 'materis/file.pdf'));
});

it('denies materi detail from classes not taken', function () {
    $kelas = createMateriKelasKuliah();
    $mahasiswa = User::factory()->create(['role' => Role::Mahasiswa]);
    $materi = Materi::create(['kelas_id' => $kelas->id, 'uploaded_by' => $kelas->dosen->user_id, 'judul_materi' => 'Materi Terlarang', 'pertemuan_ke' => 1, 'jenis' => 'Materi']);

    $this->actingAs($mahasiswa)->get(route('mahasiswa.materi.show', $materi))->assertForbidden();
});
