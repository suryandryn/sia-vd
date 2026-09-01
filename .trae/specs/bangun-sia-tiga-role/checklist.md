# Checklist

- [x] Sistem mengenali tiga role: admin, dosen, mahasiswa.
- [x] Login mengarahkan pengguna ke dashboard sesuai role.
- [x] Akses route lintas role ditolak.
- [x] Navigasi dosen berisi Beranda, Profile, KHS, Jadwal Kuliah, Tugas, Materi, Quiz.
- [x] Dosen dapat menginput nilai KHS mahasiswa sesuai kewenangan.
- [x] Navigasi mahasiswa berisi Dashboard, Profile, Jadwal Kuliah, Info Perkuliahan, Tugas, Materi, Quiz, KRS, KHS, Pendaftaran Wisuda, Perpustakaan, Info Biaya Kuliah.
- [x] KHS mahasiswa memiliki submenu KHS dan Transkrip Nilai.
- [x] Perpustakaan mahasiswa memiliki submenu Pinjaman Aktif dan Riwayat Pinjaman.
- [x] Area admin tersedia, dapat melihat seluruh data, dan tidak dapat diakses role lain.
- [x] Test feature terkait role dan akses lulus.
- [x] Autentikasi seluruh role memakai satu tabel `users`.
- [x] Profil umum tersimpan di `user_profiles` melalui relasi one-to-one.
- [x] Tidak ada tabel domain spekulatif; tabel dibuat saat aturan bisnis membutuhkannya.
- [x] Password tidak diduplikasi di tabel profil atau tabel role.
