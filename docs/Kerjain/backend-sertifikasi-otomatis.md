# Backend Requirement - Sertifikasi Otomatis (Frontend-Only)

Dokumen ini mencatat kebutuhan backend untuk mengaktifkan fitur UI `Sertifikasi Otomatis` yang saat ini dibuat frontend-only.

## 1) Endpoint Blangko Sertifikat
- `GET /admin/sertifikat/blangko`
- `POST /admin/sertifikat/blangko`
- `PUT /admin/sertifikat/blangko/{id}`
- `DELETE /admin/sertifikat/blangko/{id}`

Data minimum blangko:
- `id_blangko`
- `nama_blangko`
- `file_path`
- `is_default`
- timestamps

## 2) Endpoint Sertifikat Otomatis
- `GET /admin/sertifikat/otomatis`
- `POST /admin/sertifikat/otomatis`
- `GET /admin/sertifikat/otomatis/{id}`
- `PUT /admin/sertifikat/otomatis/{id}`
- `DELETE /admin/sertifikat/otomatis/{id}`

Data minimum sertifikat:
- `id_sertifikat`
- `nomor_sertifikat` (unique)
- `nama_peserta`
- `nama_program`
- `tanggal_terbit`
- `id_blangko`
- `font_size_nomor`
- `font_size_nama`
- timestamps

## 3) Auto Numbering Service
Butuh service generator nomor otomatis, contoh format:
- `SRT-YYYY-0001`

Validasi:
- nomor harus unik
- fallback autogenerate jika kosong

## 4) PDF Generation Endpoint
- `GET /admin/sertifikat/otomatis/{id}/pdf`

Output:
- file PDF siap download/preview
- render dari blangko + overlay data (nomor, nama, program, tanggal)

## 5) Integrasi dengan Kelulusan Kursus
Opsional lanjutan:
- auto create sertifikat ketika mahasiswa memenuhi syarat kelulusan
- endpoint trigger/manual regenerate sertifikat

## 6) Validasi & Keamanan
- validasi mime upload blangko (`jpg/png/webp`)
- limit ukuran file upload
- autorisasi admin
- audit log aksi create/edit/delete sertifikat

## Catatan
Implementasi UI saat ini berada di:
- `resources/views/Auth/admin/sertifikasi.blade.php`

UI ini berjalan frontend-only (simulasi data lokal) sesuai arahan untuk tidak mengubah backend.
