<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\KelasKuliah;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class KelasKuliahController extends Controller
{
    public function index(Request $request): Response
    {
        $dosenProfileId = $request->user()?->dosenProfile?->id;

        abort_if($dosenProfileId === null, 403);

        $search = $request->string('search')->trim()->toString();

        $kelasKuliahs = KelasKuliah::with(['mataKuliah.prodi', 'jadwals.ruang'])
            ->where('dosen_id', $dosenProfileId)
            ->when($search !== '', fn ($query) => $query->where(fn ($q) => $q->where('kode_kelas', 'like', "%{$search}%")->orWhere('tahun_ajaran', 'like', "%{$search}%")->orWhereHas('mataKuliah', fn ($q) => $q->where('kode_matkul', 'like', "%{$search}%")->orWhere('nama_matkul', 'like', "%{$search}%"))))
            ->orderBy('kode_kelas')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Dosen/KelasKuliah', [
            'kelasKuliahs' => $kelasKuliahs,
            'search' => $search,
        ]);
    }

    public function show(Request $request, KelasKuliah $kelasKuliah): Response
    {
        $dosenProfileId = $request->user()?->dosenProfile?->id;

        abort_if($dosenProfileId === null || $kelasKuliah->dosen_id !== $dosenProfileId, 403);

        $kelasKuliah->load([
            'mataKuliah.prodi.fakultas',
            'jadwals.ruang',
            'materis.uploader:id,name',
            'tugas.uploader:id,name',
            'quizzes.uploader:id,name',
        ]);

        return Inertia::render('Dosen/KelasKuliahShow', [
            'kelasKuliah' => $kelasKuliah,
        ]);
    }
}
