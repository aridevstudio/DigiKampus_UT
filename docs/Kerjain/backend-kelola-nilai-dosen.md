# Backend Kelola Nilai Dosen

## Tujuan
Menyediakan backend untuk dosen mengelola nilai mahasiswa dari:

1. Pretest
2. Tugas / penugasan modul
3. Tugas akhir (opsional per course)
4. Nilai akhir terakumulasi

## Scope yang Dibutuhkan

### 1. Sumber Data Nilai
Perlu sumber data yang konsisten untuk tiap komponen:

1. `pretest`
   Diambil dari attempt quiz dengan flag `is_pretest = true`.
2. `tugas`
   Diambil dari submission tugas yang dinilai dosen.
3. `tugas_akhir`
   Diambil dari submission tugas akhir jika fitur tugas akhir diaktifkan pada course.

### 2. Konfigurasi Bobot per Course
Setiap course perlu punya konfigurasi:

1. `bobot_pretest`
2. `bobot_tugas`
3. `bobot_tugas_akhir`
4. `is_tugas_akhir_enabled`
5. `is_nilai_published`

Catatan:
Jika `is_tugas_akhir_enabled = false`, bobot akhir harus dinormalisasi. Jangan biarkan perhitungan tetap membagi 100 jika komponen akhir dimatikan.

### 3. Tabel / Entitas yang Dibutuhkan

Minimal:

1. `course_grade_settings`
   Menyimpan bobot dan status publish nilai per course.
2. `assignment_submissions`
   Sudah atau perlu diperjelas agar bisa menyimpan:
   - skor
   - feedback dosen
   - graded_by
   - graded_at
   - status review
3. `final_grade_snapshots`
   Menyimpan nilai akhir final saat dosen publish agar histori tidak berubah jika bobot/course berubah di kemudian hari.

## Endpoint yang Dibutuhkan

### Dosen

1. `GET /dosen/kelola-nilai`
   Mengambil daftar course yang bisa dinilai.
2. `GET /dosen/kelola-nilai/{courseId}`
   Mengambil daftar mahasiswa dan breakdown nilai.
3. `PUT /dosen/kelola-nilai/{courseId}/settings`
   Update bobot nilai dan status tugas akhir.
4. `PUT /dosen/kelola-nilai/{courseId}/students/{mahasiswaId}`
   Update nilai manual / feedback dosen.
5. `POST /dosen/kelola-nilai/{courseId}/publish`
   Publish nilai akhir.
6. `POST /dosen/kelola-nilai/{courseId}/unpublish`
   Tarik kembali nilai jika perlu revisi.

### Admin

1. Read-only overview untuk audit nilai.
2. Opsi override jika memang dibutuhkan secara bisnis.

## Aturan Logic

### 1. Nilai Akhir

Rumus dasar:

`nilai_akhir = (pretest * bobot_pretest + tugas * bobot_tugas + tugas_akhir * bobot_tugas_akhir) / total_bobot_aktif`

### 2. Tugas Akhir Opsional

1. Jika dosen mengaktifkan tugas akhir:
   - mahasiswa wajib submit agar status nilai lengkap
2. Jika dosen menonaktifkan tugas akhir:
   - komponen ini diabaikan total
   - total bobot aktif harus dinormalisasi

### 3. Status Nilai

Disarankan:

1. `draft`
2. `pending_review`
3. `complete`
4. `published`
5. `revision_requested`

### 4. Publish

Saat publish:

1. simpan snapshot final
2. lock nilai final agar tidak berubah diam-diam
3. tampilkan ke mahasiswa
4. jika course lulus dan sertifikat aktif, trigger evaluasi kelulusan

## Integrasi Dengan Fitur Lain

### 1. Sertifikat
Jika nilai akhir memenuhi syarat lulus dan semua syarat course selesai:

1. mahasiswa ditandai lulus
2. sertifikat otomatis muncul jika fitur sertifikat course aktif

### 2. Progress Mahasiswa
Halaman progres dosen sebaiknya menampilkan:

1. progress belajar
2. status submission
3. nilai sementara
4. status publish nilai

### 3. Mahasiswa
Mahasiswa hanya melihat:

1. nilai final setelah dipublish
2. feedback dosen
3. status revisi jika ada

## Risiko Jika Tidak Dirapikan

1. Nilai akhir bisa berubah-ubah tanpa jejak audit.
2. Tugas akhir opsional bisa salah hitung.
3. Mahasiswa melihat nilai yang belum final.
4. Sertifikat bisa terbit saat nilai belum valid.

## Prioritas Implementasi

1. Model + migration `course_grade_settings`
2. Penyimpanan skor submission yang benar
3. Endpoint daftar nilai per course
4. Publish snapshot nilai akhir
5. Sinkronisasi ke halaman mahasiswa dan sertifikat
