# Backend TODO: Tugas Akhir Opsional di Kelola Course (Admin & Dosen)

## Status
- Frontend sudah ditambahkan:
  - Tombol cepat `Tambah Tugas Akhir (Opsional)` pada kelola modul dosen dan admin.
  - Preselect tipe materi `tugas` saat tombol cepat dipakai.
  - Label UI diperjelas bahwa tugas akhir bersifat opsional.
- Backend belum diimplementasi (sesuai instruksi: simpan rencana di docs/Kerjain dulu).

## Tujuan
1. Dosen/Admin bebas memilih mau membuat tugas akhir atau tidak.
2. Jika tidak ada tugas akhir, alur belajar tetap normal.
3. Jika ada tugas akhir, data tersimpan konsisten dan bisa dinilai/ditracking.

## Rencana Implementasi Backend

### 1) Validasi Store/Update Material
- Endpoint material store/update (admin & dosen) harus menerima `tipe=tugas` secara resmi.
- Rule validasi untuk `tugas`:
  - `judul_material`: required
  - `konten`: nullable/string (instruksi)
  - `durasi`: nullable/numeric
  - `video_url`: nullable (abaikan untuk tugas)

### 2) Normalisasi Tipe Material
- Pastikan semua layer menggunakan tipe final `tugas` (bukan variasi lain).
- Saat serialize data modul untuk halaman kelola/learn, tipe `tugas` ikut dikirim konsisten.

### 3) Data Assignment (Opsional)
- Jika arsitektur assignment dipisah dari `course_materials`, tambahkan sinkronisasi saat `tipe=tugas` dibuat.
- Jika assignment memakai tabel sendiri, map `id_material` <-> `assignment` secara eksplisit.

### 4) Policy & Authorization
- Dosen hanya boleh create/update/delete `tugas` di course miliknya.
- Admin bisa kelola semua course.

### 5) UX API Response
- Saat create `tugas` berhasil, return payload termasuk:
  - `id_material`
  - `tipe=tugas`
  - `judul_material`
  - `modul`
- Jika gagal validasi, error message spesifik untuk field tugas akhir.

## Acceptance Criteria
1. Dosen/Admin bisa menambah tugas akhir dari tombol cepat tanpa error backend.
2. Course tanpa tugas akhir tetap valid dan tidak memengaruhi progress wajib.
3. Course dengan tugas akhir tersimpan sebagai tipe `tugas` dan tampil konsisten di semua panel.
4. Tidak ada konflik dengan materi tipe video/bacaan/kuis.
