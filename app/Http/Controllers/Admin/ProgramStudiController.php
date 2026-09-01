<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DosenProfile;
use App\Models\Fakultas;
use App\Models\ProgramStudi;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class ProgramStudiController extends Controller
{
    public function index(Request $request): Response
    {
        $search = $request->string('search')->trim()->toString();
        $programStudis = ProgramStudi::with(['fakultas', 'ketuaProgramStudi.user:id,name'])->when($search !== '', fn ($query) => $query->where(fn ($query) => $query->where('kode_prodi', 'like', "%{$search}%")->orWhere('nama_prodi', 'like', "%{$search}%")))->orderBy('nama_prodi')->paginate(10)->withQueryString();

        return Inertia::render('Admin/ProgramStudi', ['programStudis' => $programStudis, 'search' => $search]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/ProgramStudiForm', ['programStudi' => null, 'fakultas' => Fakultas::orderBy('nama_fakultas')->get(['id', 'nama_fakultas']), 'dosen' => $this->dosen()]);
    }

    public function edit(ProgramStudi $programStudi): Response
    {
        return Inertia::render('Admin/ProgramStudiForm', ['programStudi' => $programStudi, 'fakultas' => Fakultas::orderBy('nama_fakultas')->get(['id', 'nama_fakultas']), 'dosen' => $this->dosen()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->save($request, new ProgramStudi);

        return to_route('admin.program-studi.index')->with('success', 'Program Studi berhasil ditambahkan.');
    }

    public function update(Request $request, ProgramStudi $programStudi): RedirectResponse
    {
        $this->save($request, $programStudi);

        return to_route('admin.program-studi.index')->with('success', 'Program Studi berhasil diperbarui.');
    }

    public function destroy(ProgramStudi $programStudi): RedirectResponse
    {
        try {
            $programStudi->delete();
        } catch (Throwable) {
            return to_route('admin.program-studi.index')->with('error', 'Program Studi gagal dihapus.');
        }

        return to_route('admin.program-studi.index')->with('success', 'Program Studi berhasil dihapus.');
    }

    private function dosen(): array
    {
        return DosenProfile::with('user:id,name')->get(['id', 'user_id'])->map(fn (DosenProfile $dosen): array => ['id' => $dosen->id, 'name' => $dosen->user->name])->all();
    }

    private function save(Request $request, ProgramStudi $model): void
    {
        $data = $request->validate(['fakultas_id' => ['required', 'exists:fakultas,id'], 'kode_prodi' => ['required', 'string', Rule::unique('program_studis')->ignore($model)], 'nama_prodi' => ['required', 'string'], 'jenjang' => ['required', 'string'], 'status_akreditasi' => ['required', 'string'], 'no_sk_akreditasi' => ['nullable', 'string'], 'tanggal_akreditasi_mulai' => ['required', 'date'], 'tanggal_akreditasi_akhir' => ['required', 'date'], 'kaprodi' => ['required', 'exists:dosen_profiles,id'], 'tahun_berdiri' => ['required', 'integer']]);
        $model->fill($data)->save();
    }
}
