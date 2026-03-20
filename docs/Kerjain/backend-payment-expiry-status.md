# Backend Payment Expiry Status

## Tujuan

Mengubah transaksi pembayaran mahasiswa yang masih berstatus `pending` tetapi sudah melewati batas waktu bayar agar dibaca sebagai `gagal` di aplikasi, tanpa migration schema tambahan.

## Perubahan

1. `PaymentTransaction` sekarang punya status efektif berbasis waktu:
   - file: `app/Models/PaymentTransaction.php`
   - pending dianggap expired jika umur transaksi lebih dari `24 jam`
   - accessor baru:
     - `pending_expires_at`
     - `effective_transaction_status`
     - `effective_status_label`

2. `CheckoutController::finance()` sekarang memfilter dan menghitung transaksi berdasarkan status efektif:
   - file: `app/Http/Controllers/Mahasiswa/CheckoutController.php`
   - filter `pending` hanya menampilkan pending yang belum kedaluwarsa
   - filter `gagal` mencakup:
     - `deny`
     - `cancel`
     - `expire`
     - `failure`
     - `pending` yang sudah lewat `24 jam`

3. View mahasiswa yang menampilkan status pembayaran sekarang membaca status efektif:
   - `resources/views/pages/mahasiswa/finance.blade.php`
   - `resources/views/pages/mahasiswa/payment.blade.php`
   - `resources/views/pages/mahasiswa/payment-success.blade.php`
   - `resources/views/pages/mahasiswa/transaction-detail.blade.php`

## Catatan

1. Ini belum menulis balik status expired ke database.
2. Status `gagal` saat ini adalah hasil evaluasi runtime dari:
   - `transaction_status = pending`
   - `created_at < now() - 24 jam`
3. Kalau nanti ingin konsisten sampai level database, langkah berikutnya:
   - tambah kolom `expired_at` atau `payment_expires_at`
   - set expiry saat transaksi dibuat
   - buat scheduler untuk mengubah `transaction_status` dari `pending` ke `expire`

## Rekomendasi Lanjutan

1. Simpan `payment_expires_at` explicit di tabel `payment_transactions`
2. Sinkronkan expiry dari payload Midtrans bila tersedia
3. Tambah command scheduler untuk normalisasi status expired di database
