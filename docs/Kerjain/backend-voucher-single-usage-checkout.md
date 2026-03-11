# Backend TODO: Voucher Checkout (Single Voucher Only)

## Tujuan
Menjamin transaksi checkout mahasiswa hanya dapat memakai **1 voucher aktif** per order, dengan perhitungan total yang konsisten dari backend.

## Rule Bisnis
1. Satu transaksi = maksimal satu voucher.
2. Jika user memasukkan voucher baru, voucher lama harus otomatis terganti (tidak ditumpuk).
3. Voucher hanya valid jika:
   - kode aktif,
   - belum kadaluarsa,
   - belum melebihi kuota penggunaan,
   - memenuhi minimum subtotal.
4. Diskon voucher tidak boleh membuat subtotal jadi negatif.
5. Semua hitung total final dilakukan backend (frontend hanya menampilkan).

## Data Model yang Dibutuhkan
### `vouchers`
- `id`
- `code` (unique)
- `type` (`percent` | `fixed`)
- `value`
- `min_subtotal`
- `max_discount` (nullable, untuk tipe percent)
- `quota_total` (nullable)
- `quota_per_user` (nullable)
- `starts_at`, `ends_at`
- `is_active`
- `created_by`
- timestamps

### `voucher_usages`
- `id`
- `voucher_id`
- `user_id` (mahasiswa)
- `order_id` / `transaction_id`
- `discount_amount`
- `used_at`
- timestamps

## Endpoint yang Disarankan
1. `POST /mahasiswa/checkout/voucher/apply`
   - input: `voucher_code`
   - output: voucher terpasang + nominal diskon + total terbaru
2. `DELETE /mahasiswa/checkout/voucher`
   - output: voucher dilepas + total terbaru
3. `GET /mahasiswa/checkout/summary`
   - output: subtotal, service_fee, voucher, discount, grand_total

## Alur Hitung Aman (Server-Side)
1. Ambil cart items user.
2. Hitung `subtotal`.
3. Validasi voucher terpasang (jika ada).
4. Hitung `discount_amount`:
   - percent: `subtotal * value%` (clamp ke `max_discount` jika ada)
   - fixed: min(`value`, `subtotal`)
5. Hitung `grand_total = subtotal - discount_amount + service_fee`.
6. Simpan voucher aktif pada draft order/session checkout.

## Integritas Saat Pembayaran
1. Sebelum create payment, backend wajib re-validate voucher.
2. Simpan snapshot:
   - `subtotal_snapshot`
   - `discount_snapshot`
   - `service_fee_snapshot`
   - `grand_total_snapshot`
3. Setelah pembayaran sukses, catat `voucher_usages`.
4. Jika voucher invalid saat payment (expired/quota habis), batalkan apply dan tampilkan total baru.

## Acceptance Criteria
1. User tidak bisa apply lebih dari 1 voucher sekaligus.
2. Nominal total di checkout, payment, dan success selalu konsisten.
3. Tidak bisa manipulasi total dari sisi frontend.
4. Voucher expired atau kuota habis ditolak dengan pesan yang jelas.
