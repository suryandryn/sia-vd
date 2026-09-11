<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\KelasKuliah;
use App\Models\Krs;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class KrsController extends Controller
{
    public function index(Request $request): Response
    {
        $mahasiswa = $request->user()->mahasiswaProfile;

        abort_if($mahasiswa === null, 403);

        $kelasKuliahs = KelasKuliah::query()
            ->with([
                'mataKuliah.prodi',
                'tahunAkademik',
                'dosen.user',
                'jadwals' => fn ($query) => $query->with('ruang')->orderBy('jam_mulai'),
            ])
            ->whereHas('mataKuliah', fn ($query) => $query->where('prodi_id', $mahasiswa->prodi_id)->where('semester', $mahasiswa->semester))
            ->whereHas('tahunAkademik', fn ($query) => $query->where('status', true))
            ->orderBy('kode_kelas')
            ->get();

        return Inertia::render('Mahasiswa/Krs', [
            'kelasKuliahs' => $kelasKuliahs,
            'mahasiswa' => $mahasiswa->only(['semester', 'angkatan', 'prodi_id']),
            'kelasDiambil' => $mahasiswa->krs()->pluck('kelas_id')->values(),
        ]);
    }

    public function store(Request $request, KelasKuliah $kelasKuliah): RedirectResponse
    {
        $mahasiswa = $request->user()->mahasiswaProfile;

        abort_if($mahasiswa === null, 403);

        $kelasKuliah->loadMissing('mataKuliah', 'tahunAkademik');

        abort_unless(
            $kelasKuliah->mataKuliah?->prodi_id === $mahasiswa->prodi_id
                && $kelasKuliah->mataKuliah?->semester === $mahasiswa->semester
                && $kelasKuliah->tahunAkademik?->status === true,
            404,
        );

        $alreadyTaken = $mahasiswa->krs()
            ->whereHas('kelasKuliah', fn ($query) => $query->where('matkul_id', $kelasKuliah->matkul_id))
            ->exists();

        if ($alreadyTaken) {
            return back()->with('krs_error', 'Anda sudah mengambil kelas di mata kuliah ini.');
        }

        Krs::create(['mahasiswa_id' => $mahasiswa->id, 'kelas_id' => $kelasKuliah->id, 'status' => 'Aktif']);

        return back()->with('krs_success', 'Kelas berhasil diambil.');
    }
}
