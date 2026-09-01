# Sistem Informasi Akademik Tiga Role Spec

## Why
Aplikasi membutuhkan fondasi Sistem Informasi Akademik dengan akses terpisah untuk admin, dosen, dan mahasiswa. Struktur menu harus jelas agar pengembangan modul akademik, pembelajaran, administrasi, dan layanan mahasiswa dapat dilakukan bertahap.

## What Changes
- Menetapkan tiga role utama: admin, dosen, dan mahasiswa.
- Menetapkan navigasi dan batas akses dasar untuk setiap role.
- Menyediakan area dosen untuk profile, KHS, jadwal kuliah, tugas, materi, dan quiz.
- Menyediakan area mahasiswa untuk akademik, pembelajaran, KRS/KHS, wisuda, perpustakaan, dan biaya kuliah.
- Menyiapkan fondasi autentikasi dan otorisasi berbasis role.
- Admin dapat melihat seluruh data yang tersedia dalam sistem.

## Impact
- Affected specs: autentikasi, otorisasi role, navigasi dashboard, akademik mahasiswa, pembelajaran dosen/mahasiswa, perpustakaan, keuangan mahasiswa.
- Affected code: model dan migration user/role, middleware/policy akses, routes, controller, halaman Inertia Vue, komponen navigasi.

## ADDED Requirements
### Requirement: Role dan akses pengguna
Sistem SHALL mengenali role admin, dosen, dan mahasiswa serta hanya menampilkan dan mengizinkan akses menu sesuai role pengguna.

#### Scenario: Pengguna login
- **WHEN** pengguna berhasil login
- **THEN** sistem mengarahkan pengguna ke dashboard sesuai role dan menampilkan navigasi role tersebut.

#### Scenario: Akses menu role lain
- **WHEN** pengguna mencoba membuka URL yang bukan milik rolenya
- **THEN** sistem menolak akses dan tidak membocorkan data menu tersebut.

### Requirement: Navigasi dan input KHS dosen
Sistem SHALL menyediakan menu dosen: Beranda, Profile, KHS, Jadwal Kuliah, Tugas, Materi, dan Quiz. Menu KHS memungkinkan dosen menginput nilai KHS mahasiswa yang berada dalam kewenangan dosen.

#### Scenario: Dosen membuka aplikasi
- **WHEN** dosen melihat navigasi
- **THEN** seluruh menu dosen tersedia dan menu role mahasiswa/admin tidak ditampilkan.

#### Scenario: Dosen menginput nilai KHS
- **WHEN** dosen mengisi dan menyimpan nilai KHS mahasiswa yang diampu
- **THEN** sistem memvalidasi data, menyimpan nilai, dan menampilkan hasil tersimpan.

#### Scenario: Dosen menginput nilai mahasiswa yang tidak diampu
- **WHEN** dosen mencoba menyimpan nilai untuk mahasiswa di luar kewenangannya
- **THEN** sistem menolak permintaan.

### Requirement: Navigasi mahasiswa
Sistem SHALL menyediakan menu mahasiswa: Dashboard, Profile, Jadwal Kuliah, Info Perkuliahan, Tugas, Materi, Quiz, KRS, KHS, Pendaftaran Wisuda, Perpustakaan, dan Info Biaya Kuliah.

#### Scenario: Mahasiswa membuka KHS
- **WHEN** mahasiswa memilih KHS
- **THEN** sistem menyediakan submenu KHS dan Transkrip Nilai.

#### Scenario: Mahasiswa membuka perpustakaan
- **WHEN** mahasiswa memilih Perpustakaan
- **THEN** sistem menyediakan submenu Pinjaman Aktif dan Riwayat Pinjaman.

### Requirement: Area admin
Sistem SHALL menyediakan role admin yang dapat melihat seluruh data yang tersedia dalam sistem, termasuk data pengguna, akademik, pembelajaran, perpustakaan, wisuda, dan biaya kuliah.

#### Scenario: Admin login
- **WHEN** admin berhasil login
- **THEN** sistem mengarahkan admin ke area administrasi dan membatasi area tersebut untuk admin.

## MODIFIED Requirements
### Requirement: Area admin
Admin dapat melihat seluruh data sistem, tetapi struktur data user tetap mengikuti satu sumber autentikasi dan profil terpisah sesuai kebutuhan domain.

## REMOVED Requirements
Tidak ada.

## Asumsi dan keputusan terbuka
- Admin memiliki akses lihat seluruh data; hak admin untuk menambah, mengubah, atau menghapus data belum ditetapkan.
- Detail field, workflow persetujuan, status, dan aturan bisnis tiap modul perlu ditetapkan sebelum modul CRUD dibuat.
- Dosen hanya dapat menginput KHS mahasiswa dan mata kuliah yang berada dalam kewenangannya.
- Jadwal kuliah dosen menampilkan jadwal yang terkait dengan dosen tersebut.
