# Backend TODO: Admin Finance Dashboard Report

## Scope
Dokumen ini untuk kebutuhan backend finance report di dashboard admin.
Frontend sudah dibuat dalam mode demo (mock data), backend belum diimplementasi.

## Tujuan
1. Menyediakan data pendapatan terpadu dari:
- Kursus
- Webinar
- Tiket event/seminar
2. Menyediakan metrik agregat untuk dashboard admin.
3. Menyediakan data time-series untuk grafik revenue.
4. Menyediakan ranking produk/top item berdasarkan pendapatan.
5. Menyediakan endpoint export Excel resmi dari server (bukan frontend blob demo).

## Kebutuhan Data
1. Summary KPI
- `total_revenue`
- `revenue_course`
- `revenue_webinar`
- `revenue_ticket`
- `growth_percentage` (vs periode sebelumnya)
- `total_transactions`

2. Revenue Trend
- Time bucket: harian/mingguan/bulanan
- Field:
  - `label`
  - `revenue`

3. Revenue Composition
- Persentase dan nominal per channel:
  - Kursus
  - Webinar
  - Tiket

4. Top Products
- `product_name`
- `category` (`kursus|webinar|tiket`)
- `transaction_count`
- `revenue`

## Endpoint yang Disarankan
1. `GET /admin/finance/summary`
- Query params:
  - `range` (`7d|30d|90d|ytd|custom`)
  - `start_date` (optional for custom)
  - `end_date` (optional for custom)

2. `GET /admin/finance/trend`
- Query params:
  - `range`
  - `group_by` (`day|week|month`)

3. `GET /admin/finance/composition`
- Query params:
  - `range`

4. `GET /admin/finance/top-products`
- Query params:
  - `range`
  - `limit` (default 10)

5. `GET /admin/finance/export/excel`
- Query params:
  - `range`
  - `start_date`
  - `end_date`
- Response:
  - File `.xlsx` dengan sheet:
    - `Summary`
    - `Trend`
    - `Top Products`

## Struktur Response (Contoh)
```json
{
  "success": true,
  "data": {
    "summary": {
      "total_revenue": 112300000,
      "revenue_course": 67400000,
      "revenue_webinar": 27800000,
      "revenue_ticket": 17100000,
      "growth_percentage": 12.5,
      "total_transactions": 622
    }
  }
}
```

## Catatan Implementasi
1. Gunakan source pembayaran yang statusnya `paid/success` saja.
2. Hindari double counting jika ada refund/void:
- Netto = gross - refund.
3. Gunakan timezone yang konsisten (`Asia/Jakarta`) untuk grouping tanggal.
4. Tambahkan index pada kolom:
- `paid_at`
- `payment_status`
- `product_type`
5. Endpoint export sebaiknya menggunakan queued job jika data sangat besar.
6. Untuk skala kecil-menengah, response stream langsung masih acceptable.

## Authorization
1. Endpoint hanya untuk guard `admin`.
2. Tambahkan policy/middleware khusus finance jika diperlukan.

## Acceptance Criteria
1. Semua kartu KPI di dashboard terisi dari API backend.
2. Grafik trend dan komposisi konsisten dengan angka summary.
3. Data top products terurut dari revenue terbesar.
4. Query tetap cepat untuk data besar (target response < 500ms untuk range 30 hari).
5. File Excel dapat di-download dengan struktur kolom yang konsisten.
