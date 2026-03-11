# Backend TODO: Kategori Pengumuman Admin (Frontend Sudah Siap)

## Status
- Frontend sudah menampilkan kategori baru:
  - `umum`
  - `akademik`
  - `keungan`
  - `registrasi`
  - `kemahasiswaan`
- Backend belum diubah (sesuai arahan: fokus frontend dulu).

## Tujuan Backend Nanti
1. Menerima kategori baru saat create/update pengumuman.
2. Memastikan filter kategori di endpoint admin pengumuman bekerja untuk kategori baru.
3. Menyesuaikan schema database jika kolom `news.kategori` masih enum lama.

## Perubahan Backend (Nanti)
1. `app/Http/Controllers/Auth/AdminController.php`
- Ubah validasi:
  - dari: `in:pengumuman,berita,event`
  - ke: `in:umum,akademik,keungan,registrasi,kemahasiswaan`

2. Migration schema `news.kategori`
- Jika enum lama:
  - Tambah nilai enum baru sesuai kategori di atas.
- Pastikan rollback aman.

3. Seeder/fixture (opsional)
- Tambahkan contoh data pengumuman dengan kategori baru.

## Acceptance Criteria
1. Simpan/Edit pengumuman kategori baru tidak gagal validasi.
2. Filter kategori di halaman admin pengumuman mengembalikan data yang benar.
3. Data kategori tampil konsisten antara dashboard admin dan halaman pengumuman.
