# Tasks
- [x] Task 1: Tetapkan fondasi role admin, dosen, dan mahasiswa pada autentikasi pengguna.
  - [ ] Tambahkan representasi role pada user dan data awal role.
  - [ ] Terapkan pembatasan akses berbasis role pada route/server.
- [ ] Task 2: Bangun layout dan navigasi area admin, dosen, dan mahasiswa.
  - [ ] Buat dashboard tujuan tiap role.
  - [ ] Tampilkan menu dosen sesuai spesifikasi.
  - [ ] Tampilkan menu mahasiswa beserta submenu KHS dan Perpustakaan.
  - [ ] Sediakan area admin dengan akses lihat seluruh data sistem.
- [ ] Task 3: Siapkan route/halaman placeholder untuk menu dosen dan mahasiswa.
  - [ ] Pastikan setiap menu memiliki route bernama dan halaman Inertia yang dapat dibuka oleh role tepat.
  - [ ] Pastikan akses URL lintas role ditolak.
- [x] Task 4: Tambahkan pengujian feature untuk login, redirect dashboard, navigasi, dan pembatasan akses.
  - [x] Jalankan test terfokus dan formatter PHP bila ada perubahan PHP.

- [x] Task 5: Tetapkan arsitektur penyimpanan user sebelum modul akademik berkembang.
  - [x] Pertahankan satu tabel `users` untuk autentikasi seluruh role.
  - [x] Pisahkan profil umum ke tabel `user_profiles` dengan relasi one-to-one.
  - [x] Tidak ada tabel domain mahasiswa/dosen spekulatif; atribut khusus akademik belum ada.
  - [x] Migrasikan field profil umum saat penerapan skema baru tanpa menghapus data user.

# Task Dependencies
- Task 2 depends on Task 1.
- Task 3 depends on Task 1 and Task 2.
- Task 4 depends on Task 1, Task 2, and Task 3.
