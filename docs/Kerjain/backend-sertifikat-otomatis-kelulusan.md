# Backend TODO: Sertifikat Otomatis Saat Course/Webinar Selesai

## Status
- Frontend sudah disiapkan:
  - Tombol `Cetak Sertifikat` hanya muncul saat syarat terpenuhi.
  - Syarat frontend saat ini: `course.sertifikat = true` dan enrollment `status=selesai` atau `progress >= 100`.
- Backend belum diimplementasi (sesuai instruksi: simpan dulu di docs/Kerjain).

## Tujuan
Saat mahasiswa menyelesaikan course/webinar dan fitur sertifikat diaktifkan dosen/admin, sertifikat langsung tersedia.

## Rule Bisnis Final
1. Toggle `sertifikat` dikelola dari halaman kelola course dosen/admin.
2. Jika toggle `sertifikat = off`, sertifikat tidak boleh terbit walaupun progress 100%.
3. Jika toggle `sertifikat = on`, sertifikat terbit otomatis saat syarat kelulusan terpenuhi.
4. Tugas akhir yang statusnya opsional tidak boleh menjadi blocker kelulusan/sertifikat.
5. Webinar memakai rule kehadiran tervalidasi (bukan hanya daftar/enroll).

## Rencana Implementasi Backend

### 1) Source of Truth Eligibility
Buat service/logic tunggal, misalnya `CertificateEligibilityService`, dengan rule:
- `course.sertifikat == true`
- Enrollment valid dan milik mahasiswa
- Kelulusan terpenuhi:
  - Kursus reguler: progress >= 100 atau status selesai
  - Jika ada tugas akhir opsional: status tugas akhir tidak wajib untuk kelulusan
  - Webinar: attendance/kehadiran memenuhi syarat (misalnya hadir >= X menit atau status attendance selesai)

Contoh method:
- `isEligibleForCourseCertificate(Enrollment $enrollment, Course $course): bool`
- `isEligibleForWebinarCertificate(WebinarAttendance $attendance, Course $course): bool`
- `issueCertificateIfEligible(int $mahasiswaId, int $courseId): ?Certificate`

### 2) Persisted Certificate Record
Tambahkan tabel/entitas misal `certificates`:
- `id`, `id_mahasiswa`, `id_course`, `certificate_number`, `issued_at`, `status`
- unique index `(id_mahasiswa, id_course)` agar tidak duplikat

### 3) Auto Issue Trigger
Trigger saat:
- `Enrollment` berubah ke selesai
- atau progress menyentuh 100%
- atau attendance webinar tervalidasi selesai
Lalu jalankan `issueIfEligible(mahasiswaId, courseId)`.

Tambahan trigger:
- Saat dosen/admin mengubah toggle sertifikat dari OFF ke ON, jalankan backfill untuk peserta yang sudah eligible.

### 4) Certificate Numbering
Format nomor sertifikat konsisten, contoh:
`DK-UT/{YYYY}/{COURSE_CODE}/{SEQUENCE}`

### 5) Endpoint Mahasiswa
Tambah endpoint:
- `GET /mahasiswa/course/{id}/certificate` (detail)
- `GET /mahasiswa/course/{id}/certificate/print` (render printable)
- `GET /mahasiswa/course/{id}/certificate/download` (PDF)

### 6) Security
- Pastikan mahasiswa hanya bisa akses sertifikat miliknya.
- Validasi ownership enrollment sebelum akses download/print.

## Acceptance Criteria
1. Sertifikat hanya terbit jika fitur sertifikat aktif.
2. Begitu status selesai terpenuhi, record sertifikat otomatis terbuat.
3. Webinar menggunakan syarat kehadiran, bukan sekadar enrollment aktif.
4. Tugas akhir opsional tidak memblokir terbitnya sertifikat.
5. Mahasiswa bisa print/download sertifikat tanpa manipulasi data di frontend.
