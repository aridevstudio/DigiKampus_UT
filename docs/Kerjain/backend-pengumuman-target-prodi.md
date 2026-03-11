# Backend TODO: Pengumuman Target Prodi (Admin)

## Status
- Frontend sudah disiapkan di halaman admin pengumuman.
- Backend **belum diimplementasi** (sesuai arahan: frontend dulu, backend ditulis di docs/Kerjain).

## Tujuan
1. Admin bisa membuat pengumuman untuk:
   - semua prodi (umum)
   - prodi tertentu
2. Admin bisa filter daftar pengumuman berdasarkan target prodi.
3. User (mahasiswa/dosen) hanya menerima pengumuman umum + sesuai prodinya.

## Rencana Implementasi Backend

### 1) Database
- Tambah kolom `target_prodi` pada tabel `news` (nullable string).
- Nilai `NULL` dianggap **umum / semua prodi**.
- Tambah index untuk query filter (`target_prodi`).

### 2) Validasi & Mapping Input
Pada `storePengumuman` dan `updatePengumuman`:
- Validasi:
  - `target_prodi` => `nullable|in:all,informatika,sistem-informasi,teknik-elektro,manajemen,akuntansi`
- Mapping:
  - input `all` disimpan sebagai `NULL`
  - selain itu simpan slug prodi

### 3) Endpoint Admin
- `showPengumuman`:
  - dukung filter query `target_prodi`
  - `all` = tanpa filter khusus
  - slug prodi = `where('target_prodi', slug)`
- `getPengumuman`:
  - return field `target_prodi` agar modal edit sinkron

### 4) Distribusi ke Mahasiswa & Dosen
Pada query list pengumuman sisi user:
- `whereNull('target_prodi')` OR `where('target_prodi', user_prodi_slug)`

### 5) Backward Compatibility
- Data lama tanpa `target_prodi` otomatis tetap tampil sebagai pengumuman umum.

## Acceptance Criteria
1. Admin create/edit pengumuman dengan target prodi berjalan.
2. Filter target prodi di halaman admin berjalan.
3. Pengumuman umum terlihat di semua prodi.
4. Pengumuman prodi-spesifik hanya terlihat di prodi terkait.
