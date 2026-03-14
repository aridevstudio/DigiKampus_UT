# Backend Bootcamp dan Tiket

## Tujuan
Memisahkan domain `bootcamp/tiket` supaya:

1. Admin mengelola operasional produk, batch, kuota, publish, penjualan, dan refund
2. Dosen hanya mengelola delivery: sesi, materi, cohort, review tugas, dan mentoring

## Pembagian Hak Akses

### Admin
1. Buat/edit/publish bootcamp
2. Buka/stop penjualan tiket
3. Atur batch, seat quota, waitlist, pricing, voucher, promo
4. Assign dosen/mentor ke bootcamp
5. Lihat peserta, payment, attendance, refund, reschedule
6. Tutup batch dan trigger sertifikat / arsip

### Dosen
1. Lihat bootcamp yang diampu
2. Kelola sesi, materi, tugas, feedback, nilai
3. Pantau cohort dan attendance
4. Broadcast informasi belajar ke peserta batch

### Tidak boleh di dosen
1. Mengubah harga
2. Mengubah kuota / capacity
3. Publish atau unpublish tiket penjualan
4. Menyetujui refund

## Entitas yang Dibutuhkan

1. `bootcamps`
   - tipe produk `bootcamp` / `ticketed_event`
   - title, slug, summary, price, status
2. `bootcamp_batches`
   - batch_name
   - registration_open_at
   - registration_close_at
   - event_start_at
   - event_end_at
   - seat_quota
   - waitlist_quota
   - mode `online`, `offline`, `hybrid`
3. `bootcamp_mentors`
   - bootcamp_id / batch_id
   - user_id
   - role `lead_mentor`, `mentor`, `facilitator`, `speaker`
4. `bootcamp_participants`
   - user_id
   - batch_id
   - payment_status
   - attendance_status
   - completion_status
5. `bootcamp_sessions`
   - batch_id
   - mentor_id
   - title
   - scheduled_at
   - duration
   - delivery_type
6. `bootcamp_attendance`
7. `bootcamp_refunds`
8. `bootcamp_certificates`

## Endpoint Minimal

### Admin
1. `GET /admin/bootcamp-tiket`
2. `GET /admin/bootcamp-tiket/{id}`
3. `POST /admin/bootcamp-tiket`
4. `PUT /admin/bootcamp-tiket/{id}`
5. `POST /admin/bootcamp-tiket/{id}/publish`
6. `POST /admin/bootcamp-tiket/{id}/close-registration`
7. `POST /admin/bootcamp-tiket/{id}/assign-mentor`
8. `POST /admin/bootcamp-tiket/{id}/refund`

### Dosen
1. `GET /dosen/bootcamp`
2. `GET /dosen/bootcamp/{id}`
3. `POST /dosen/bootcamp/{id}/sessions`
4. `PUT /dosen/bootcamp/{id}/sessions/{sessionId}`
5. `POST /dosen/bootcamp/{id}/attendance`
6. `POST /dosen/bootcamp/{id}/broadcast`
7. `POST /dosen/bootcamp/{id}/grades`

## State yang Harus Jelas

### Produk
1. `draft`
2. `internal_review`
3. `published`
4. `registration_closed`
5. `in_progress`
6. `completed`
7. `archived`

### Payment
1. `pending`
2. `paid`
3. `failed`
4. `refunded`

### Attendance
1. `not_marked`
2. `present`
3. `late`
4. `absent`

## Risiko Kalau Dicampur

1. Dosen bisa tanpa sengaja mengubah operasional penjualan
2. Admin sulit audit batch yang sudah live
3. Refund dan reschedule bercampur dengan aktivitas belajar
4. UX kacau karena course biasa, webinar, dan bootcamp punya lifecycle berbeda

## Arah UI yang Sudah Dipasang

1. Sidebar admin: `Bootcamp & Tiket`
2. Sidebar dosen: `Bootcamp Saya`
3. Admin page fokus ops, cohort, mentor, revenue funnel
4. Dosen page fokus sesi, cohort, mentoring, submission queue
