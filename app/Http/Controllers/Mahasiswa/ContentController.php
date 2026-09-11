<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\KelasKuliah;
use App\Models\Krs;
use App\Models\Materi;
use App\Models\Quiz;
use App\Models\Tugas;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ContentController extends Controller
{
    public function jadwalKuliah(Request $request): Response
    {
        $mahasiswa = $request->user()->mahasiswaProfile;
        abort_if($mahasiswa === null, 403);

        $kelasKuliahs = Krs::query()
            ->where('mahasiswa_id', $mahasiswa->id)
            ->with('kelasKuliah.mataKuliah', 'kelasKuliah.jadwals.ruang')
            ->get()
            ->pluck('kelasKuliah')
            ->filter()
            ->values();

        return Inertia::render('Mahasiswa/JadwalKuliah', ['kelasKuliahs' => $kelasKuliahs]);
    }

    public function show(Request $request, KelasKuliah $kelasKuliah): Response
    {
        $mahasiswa = $request->user()->mahasiswaProfile;
        abort_if($mahasiswa === null, 403);

        abort_unless($kelasKuliah->krs()->where('mahasiswa_id', $mahasiswa->id)->exists(), 403);

        $kelasKuliah->load([
            'mataKuliah.prodi.fakultas',
            'tahunAkademik',
            'dosen.user',
            'jadwals.ruang',
            'materis.uploader:id,name',
            'tugas.uploader:id,name',
            'quizzes.uploader:id,name',
        ]);

        return Inertia::render('Mahasiswa/KelasKuliahShow', ['kelasKuliah' => $kelasKuliah]);
    }

    public function materiShow(Request $request, Materi $materi): Response
    {
        $mahasiswa = $request->user()->mahasiswaProfile;
        abort_if($mahasiswa === null, 403);

        abort_unless($materi->kelasKuliah()->whereHas('krs', fn ($query) => $query->where('mahasiswa_id', $mahasiswa->id))->exists(), 403);

        $materi->load(['kelasKuliah.mataKuliah', 'uploader:id,name']);

        return Inertia::render('Mahasiswa/MateriShow', ['materi' => $materi]);
    }

    public function index(Request $request, string $type): Response
    {
        abort_unless(in_array($type, ['tugas', 'materi', 'quiz'], true), 404);

        $mahasiswa = $request->user()->mahasiswaProfile;
        abort_if($mahasiswa === null, 403);

        $model = ['tugas' => Tugas::class, 'materi' => Materi::class, 'quiz' => Quiz::class][$type];
        $items = $model::with('kelasKuliah.mataKuliah')
            ->whereHas('kelasKuliah.krs', fn ($query) => $query->where('mahasiswa_id', $mahasiswa->id))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Mahasiswa/ContentIndex', [
            'type' => $type,
            'items' => $items,
        ]);
    }
}
