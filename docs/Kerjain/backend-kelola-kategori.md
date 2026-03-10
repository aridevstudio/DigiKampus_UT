# Backend Requirement - Kelola Kategori (Frontend-Only Request)

Dokumen ini dibuat karena implementasi `Kelola Kategori` diminta fokus frontend saja.
Jika nanti perlu diaktifkan penuh, backend minimal yang dibutuhkan adalah:

## 1) Route
Tambahkan route admin:
- `GET /admin/kategori`
- `POST /admin/kategori`
- `GET /admin/kategori/{id}`
- `PUT /admin/kategori/{id}`
- `DELETE /admin/kategori/{id}`

## 2) Controller Method
Di `AdminController` dibutuhkan method:
- `showKategori()` untuk list + filter + pagination
- `storeKategori()` untuk create
- `getKategori()` untuk API edit modal
- `updateKategori()` untuk update
- `deleteKategori()` untuk delete dengan validasi relasi

## 3) Data Model
Sediakan tabel dan model kategori, minimal field:
- `id_kategori` (PK)
- `kode_kategori` (unique)
- `nama_kategori`
- `tipe` (mis. `kursus` / `pengumuman`)
- `is_active` (boolean)
- timestamps

## 4) Validasi
Validasi minimal:
- `kode_kategori` wajib + unique
- `nama_kategori` wajib
- `tipe` wajib, nilai terbatas

## 5) Endpoint JSON Edit Modal
`GET /admin/kategori/{id}` mengembalikan JSON:
- `id_kategori`
- `kode_kategori`
- `nama_kategori`
- `tipe`
- `is_active`

## 6) Integrasi Form
Form frontend membutuhkan:
- CSRF token aktif
- method spoofing `PUT` dan `DELETE`
- flash message success/error untuk feedback UI

## Catatan
Saat ini implementasi yang dipertahankan adalah frontend-only (`resources/views/Auth/admin/kategori.blade.php`) tanpa aktivasi backend baru.
