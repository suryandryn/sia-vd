<?php

use App\Http\Controllers\Admin\FakultasController;
use App\Http\Controllers\Admin\KelasKuliahController;
use App\Http\Controllers\Admin\MataKuliahController;
use App\Http\Controllers\Admin\ProgramStudiController;
use App\Http\Controllers\Admin\RuangController;
use App\Http\Controllers\Admin\UserController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return redirect()->route('login');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('admin', fn () => Inertia::render('Dashboard', ['role' => 'admin']))
    ->middleware(['auth', 'verified', 'role:admin'])
    ->name('admin.dashboard');

Route::get('admin/data', fn () => Inertia::render('Dashboard', ['role' => 'admin', 'viewAllData' => true]))
    ->middleware(['auth', 'verified', 'role:admin'])
    ->name('admin.data');

Route::prefix('admin/users')->middleware(['auth', 'verified', 'role:admin'])->group(function () {
    foreach (['dosen', 'mahasiswa', 'karyawan'] as $type) {
        Route::get($type, fn (Request $request) => app(UserController::class)->index($request, $type))
            ->name('admin.users.'.$type);
        Route::get($type.'/create', fn () => app(UserController::class)->create($type))
            ->name('admin.users.'.$type.'.create');
        Route::post($type, fn (Request $request) => app(UserController::class)->store($request, $type))
            ->name('admin.users.'.$type.'.store');
        Route::get($type.'/{user}', fn (User $user) => app(UserController::class)->show($type, $user))
            ->name('admin.users.'.$type.'.show');
        Route::get($type.'/{user}/edit', fn (User $user) => app(UserController::class)->edit($type, $user))
            ->name('admin.users.'.$type.'.edit');
        Route::put($type.'/{user}', fn (Request $request, User $user) => app(UserController::class)->update($request, $type, $user))
            ->name('admin.users.'.$type.'.update');
        Route::delete($type.'/{user}', fn (User $user) => app(UserController::class)->destroy($type, $user))
            ->name('admin.users.'.$type.'.destroy');
    }
});

Route::prefix('admin')->middleware(['auth', 'verified', 'role:admin'])->group(function (): void {
    Route::resource('fakultas', FakultasController::class)->parameters(['fakultas' => 'fakulta'])->names('admin.fakultas');
    Route::resource('program-studi', ProgramStudiController::class)->names('admin.program-studi');
    Route::resource('mata-kuliah', MataKuliahController::class)->parameters(['mata_kuliah' => 'mataKuliah'])->names('admin.mata-kuliah');
    Route::resource('ruang', RuangController::class)->parameters(['ruang' => 'ruang'])->names('admin.ruang');
    Route::resource('kelas-kuliah', KelasKuliahController::class)->parameters(['kelas_kuliah' => 'kelasKuliah'])->names('admin.kelas-kuliah');
});

Route::prefix('dosen')->middleware(['auth', 'verified', 'role:dosen'])->group(function () {
    Route::get('/', fn () => Inertia::render('Dashboard'))->name('dosen.dashboard');
    Route::get('profile', fn () => Inertia::render('DosenPlaceholder', ['title' => 'Profile']))->name('dosen.profile');
    Route::get('khs', fn () => Inertia::render('DosenKhs'))->name('dosen.khs');
    Route::get('jadwal-kuliah', fn () => Inertia::render('DosenPlaceholder', ['title' => 'Jadwal Kuliah']))->name('dosen.jadwal-kuliah');
    Route::get('tugas', fn () => Inertia::render('DosenPlaceholder', ['title' => 'Tugas']))->name('dosen.tugas');
    Route::get('materi', fn () => Inertia::render('DosenPlaceholder', ['title' => 'Materi']))->name('dosen.materi');
    Route::get('quiz', fn () => Inertia::render('DosenPlaceholder', ['title' => 'Quiz']))->name('dosen.quiz');
});

Route::prefix('mahasiswa')->middleware(['auth', 'verified', 'role:mahasiswa'])->group(function () {
    Route::get('/', fn () => Inertia::render('Dashboard'))->name('mahasiswa.dashboard');
    foreach ([
        'profile' => 'Profile', 'jadwal-kuliah' => 'Jadwal Kuliah', 'info-perkuliahan' => 'Info Perkuliahan',
        'tugas' => 'Tugas', 'materi' => 'Materi', 'quiz' => 'Quiz', 'krs' => 'KRS',
        'khs' => 'KHS', 'pendaftaran-wisuda' => 'Pendaftaran Wisuda', 'perpustakaan' => 'Perpustakaan',
        'info-biaya-kuliah' => 'Info Biaya Kuliah', 'khs/transkrip-nilai' => 'Transkrip Nilai',
        'perpustakaan/pinjaman-aktif' => 'Pinjaman Aktif', 'perpustakaan/riwayat-pinjaman' => 'Riwayat Pinjaman',
    ] as $path => $title) {
        Route::get($path, fn () => Inertia::render('MahasiswaPlaceholder', ['title' => $title]))
            ->name('mahasiswa.'.str_replace('/', '.', $path));
    }
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
