# Audit Logic Flow Project (Product + UX + System)

Tanggal audit: 12 Maret 2026

## A. Ringkasan tujuan website yang saya tangkap
1. Ini paling masuk akal sebagai platform LMS/edutech multi-role: `Mahasiswa` beli/ikuti kursus-webinar, `Dosen` kelola konten, `Admin` mengelola operasional (user, pengumuman, moderasi, laporan).
2. Tujuan bisnisnya kemungkinan: monetisasi kursus/webinar + engagement belajar + sertifikasi.

## B. Alur user yang saya simpulkan
1. Flow paling mungkin (utama): Mahasiswa login -> lihat course -> checkout + voucher -> payment success -> enrollment aktif -> belajar modul/quiz/tugas -> chat dosen -> selesai -> sertifikat.
2. Flow dosen: login -> buat/edit kursus -> atur modul/quiz/tugas -> pantau progres + chat mahasiswa.
3. Flow admin: login -> kelola dosen/mahasiswa/kursus/pengumuman/chat/report.
4. Flow alternatif (lebih lemah): ini cuma demo frontend (karena beberapa modul masih simulated), bukan sistem produksi penuh.
5. Yang paling masuk akal saat ini: hybrid "setengah produksi, setengah simulasi".

## C. Bagian yang sudah logis
1. Pemisahan role dan route sudah jelas (admin/dosen/mahasiswa).
2. Journey utama kursus dari list -> detail -> checkout -> learning sudah tersambung.
3. Banyak validasi dasar form sudah ada (contoh upload size, type, dsb).
4. UI admin/dosen/mahasiswa sudah diarahkan ke pola yang konsisten (toolbar, list, table/card responsive).

## D. Bagian yang janggal / berpotensi salah
1. Payment flow tidak aman secara logika bisnis: tombol sukses bisa memicu enrollment tanpa bukti pembayaran final.
2. Voucher terlihat dipakai di UI, tapi backend tidak jadi source of truth (rawan beda total bayar vs total tercatat).
3. Admin chat terlihat "realtime/moderasi", tapi endpoint admin chat belum benar-benar ada (UI bisa terasa hidup tapi logikanya kosong/simulasi).
4. Retensi chat 24 jam terlihat lebih banyak di sisi frontend, bukan lifecycle data backend (risiko data tetap ada).
5. Permission gap chat dosen-mahasiswa berpotensi kebocoran: dosen bisa akses chat mahasiswa yang bukan peserta jika hanya andalkan ID target.
6. State enum/status tidak konsisten antar migration dan controller (`aktif/selesai` vs `pending/in_progress/gagal`, dll).
7. Pengumuman kategori/prodi terlihat berkembang cepat, tapi model data belum konsisten penuh (risiko filter salah/insert gagal).

## E. Missing flow / logic yang belum tertangani
1. State machine transaksi: `draft -> pending -> paid -> failed -> refunded -> cancelled`.
2. Idempotency untuk payment callback (hindari double enrollment).
3. Satu voucher per transaksi harus dipaksa di backend, bukan hanya UI.
4. Audit log admin action (hapus chat, moderasi, publish pengumuman).
5. Empty state penting: chat kosong, belum ada course, belum ada pengumuman per prodi, data report nol.
6. Sertifikat otomatis: trigger kelulusan final belum menjadi aturan eksplisit tunggal (course vs webinar vs tugas akhir opsional).
7. Fallback/UX untuk error integrasi (API timeout, fetch fail, race condition realtime).

## F. Risiko jika flow ini dipakai apa adanya
1. Revenue leakage: user bisa "lulus checkout" tanpa pembayaran valid.
2. Data integrity rusak: status enrollment/payment tidak sinkron.
3. Risiko privasi: akses chat lintas peserta.
4. Risiko legal/compliance: retensi chat tidak sesuai klaim "hapus 24 jam".
5. Kepercayaan user turun: fitur terlihat ada, tapi hasil backend tidak konsisten.
6. Operasional admin berat: banyak keputusan manual tanpa guardrail state.

## G. Rekomendasi perbaikan paling prioritas
1. Kunci dulu domain pembayaran + enrollment (backend-first): status transaksi tunggal, webhook/callback, idempotent, baru UI mengikuti.
2. Rapikan kamus state global (enum + constant): user/course/enrollment/payment/news harus satu bahasa yang sama di DB, controller, view.
3. Benahi chat ACL dan retention di backend: validasi partisipan, admin moderation endpoint nyata, TTL/hapus terjadwal sesuai policy.
4. Terapkan kontrak voucher server-side: satu voucher aktif per order, validasi scope/expiry/usage, total final dihitung backend.
5. Pengumuman target prodi: tambah field target + fallback "semua prodi" + filter konsumsi di mahasiswa.
6. Buat test skenario lintas role paling kritis (happy path + edge case): checkout gagal, double submit, unauthorized chat, publish pengumuman salah target.

## H. Pertanyaan penting yang harus dipikirkan sekarang
1. "Paid" ditentukan oleh apa sebagai sumber kebenaran final: frontend event, gateway callback, atau admin approve?
2. Definisi "course selesai" itu apa: semua modul selesai, quiz lulus, tugas akhir (opsional) lulus, atau cukup progress %?
3. Jika dosen mematikan tugas akhir, bagaimana aturan sertifikat berubah secara deterministik?
4. Admin boleh baca semua chat atau hanya chat yang di-flag/aktif? Perlu consent/policy?
5. Retensi chat 24 jam itu hard-delete, soft-delete, atau hide-only? Bagaimana auditnya?
6. Bagaimana rollback kalau payment sukses tapi enrollment gagal (atau sebaliknya)?
7. Apakah finance report untuk keputusan bisnis atau hanya dashboard visual? Jika bisnis, datanya harus immutable dan rekonsiliasi-ready.

## Skor kualitas logic flow
Skor saat ini: **4.5/10** (arah produk sudah jelas, tapi fondasi state + transaksi + permission masih belum aman untuk dipakai penuh di produksi).
