# Chat 24 Jam + Admin Moderation (Backend TODO)

## Scope
Dokumen ini berisi pekerjaan backend yang **belum dikerjakan** (sesuai permintaan fokus frontend dulu).

## Tujuan
1. Pesan chat mahasiswa-dosen otomatis tidak tampil setelah 24 jam (server-side source of truth).
2. Admin bisa memantau seluruh percakapan.
3. Admin bisa menghapus pesan tertentu (soft delete/hard delete sesuai kebijakan).
4. Admin bisa ikut mengirim pesan di thread mahasiswa-dosen (bukan hanya monitor).
5. Update chat realtime (push event), bukan polling saja.

## Perubahan Data Model
1. Tabel `messages`
- Tambah kolom `expires_at` (datetime, indexed) -> default `created_at + 24 jam`.
- Tambah kolom `deleted_by_admin_at` (nullable datetime).
- Tambah kolom `deleted_by_admin_id` (nullable FK user/admin).
- Tambah kolom `delete_reason` (nullable string/text, opsional).
- Tambah kolom `conversation_id` (bigint/indexed, wajib jika admin ikut thread).
- Tambah kolom `sender_role` (enum: `mahasiswa|dosen|admin`) agar rendering role konsisten.

2. Tabel `chat_conversations` (disarankan)
- `id`, `mahasiswa_id`, `dosen_id`, `created_at`, `updated_at`.
- Unique index: (`mahasiswa_id`, `dosen_id`).
- Menjadi sumber thread utama untuk admin monitor + join chat.

3. Optional: tabel audit `message_moderation_logs`
- `id`, `message_id`, `admin_id`, `action` (`delete`, `restore`, `force_delete`), `reason`, `created_at`.

## Endpoint Backend yang Dibutuhkan
1. Admin monitor conversations
- `GET /admin/messages/conversations`
- Return daftar conversation terbaru (mahasiswa <-> dosen), unread count, last message.

2. Admin monitor message thread
- `GET /admin/messages/thread?conversation_id=...`
- Return message list yang belum expired dan belum deleted.

3. Admin join chat (baru)
- `POST /admin/messages/send`
- Payload:
  - `conversation_id` (required)
  - `content` (required, max 2000)
  - `target` (`mahasiswa|dosen|both`, default `both`)
- Behavior:
  - `target=both`: kirim 2 message di conversation yang sama (visible ke dua pihak).
  - `target=mahasiswa|dosen`: kirim ke pihak terpilih saja.

4. Admin delete message
- `DELETE /admin/messages/{id}` (soft delete by admin)
- Simpan audit log + alasan.

5. Optional restore
- `POST /admin/messages/{id}/restore`

6. Update endpoint existing dosen/mahasiswa
- `GET /mahasiswa/messages/chat/{dosenId}` dan `GET /dosen/messages/chat/{studentId}` harus baca dari `conversation_id` yang sama.
- Response wajib bisa mengembalikan `sender_role=admin` agar bubble admin muncul di UI.

## Filter Logic Wajib di Server
Setiap query chat (mahasiswa/dosen/admin) harus memfilter:
- `deleted_by_admin_at IS NULL`
- `expires_at > NOW()`

Dengan ini walau frontend dimanipulasi, pesan expired tetap tidak bisa diambil.

## Realtime (Disarankan)
Gunakan Laravel Broadcasting (Pusher / Soketi / Reverb):
1. Event `MessageSent`
- Trigger saat message baru dibuat.
- Channel private per conversation.

2. Event `MessageDeletedByAdmin`
- Trigger saat admin hapus pesan.
- Frontend remove message langsung.

3. Event `AdminMessageSent` (baru)
- Trigger saat admin kirim pesan ke thread.
- Channel conversation yang sama, diterima mahasiswa+dosen.

4. Event `ConversationExpired`
- Opsional; atau cukup frontend refresh berkala + server filter.

## Scheduler / Job
1. Command harian/jam-an untuk housekeeping:
- Menandai expired (jika butuh materialized state).
- Purge hard delete setelah X hari (opsional kebijakan retensi legal).

## Authorization Rules
1. Mahasiswa
- Hanya bisa akses percakapan yang melibatkan dirinya.

2. Dosen
- Hanya bisa akses percakapan dengan mahasiswa terkait.

3. Admin
- Bisa read all thread.
- Bisa delete message.
- Bisa kirim pesan ke thread (target mahasiswa/dosen/both).

## Acceptance Criteria
1. Pesan >24 jam tidak dikirim di response API.
2. Admin dapat membuka thread mahasiswa-dosen mana pun.
3. Admin bisa kirim pesan ke thread dan langsung terlihat di sisi dosen/mahasiswa.
4. Admin delete message -> pesan hilang realtime di sisi dosen dan mahasiswa.
5. Semua endpoint melewati policy/guard yang benar.

## Catatan Frontend Saat Ini
Frontend sudah:
- Menyembunyikan pesan >24 jam di UI (client-side).
- Polling realtime lebih cepat.

Tetap perlu backend di atas agar rule 24 jam dan moderation benar-benar enforce di server.

## Catatan Implementasi
1. Hindari solusi prefix/tag di `content` untuk menandai context thread.
2. Pakai `conversation_id` sebagai source of truth supaya:
- Query cepat.
- Admin join thread clean.
- Realtime channel stabil per conversation.
