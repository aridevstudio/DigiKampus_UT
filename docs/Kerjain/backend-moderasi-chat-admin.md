# Backend Moderasi Chat Admin

## Tujuan
- Mendukung aksi moderasi di halaman `admin/chat` agar tidak hanya bekerja di UI lokal.

## Aksi yang Sudah Ada di Frontend
- Hapus 1 pesan
- Hapus semua chat `Mahasiswa` dalam 1 percakapan
- Hapus semua chat `Dosen` dalam 1 percakapan
- Hapus seluruh percakapan

## Endpoint yang Dibutuhkan

### 1. Hapus satu pesan
- `DELETE /admin/messages/{messageId}`

Response:
```json
{
  "success": true,
  "message": "Pesan berhasil dihapus."
}
```

### 2. Hapus semua pesan berdasarkan role dalam satu percakapan
- `POST /admin/messages/conversation/{conversationId}/purge-role`

Payload:
```json
{
  "role": "student"
}
```

Role yang valid:
- `student`
- `lecturer`

Response:
```json
{
  "success": true,
  "message": "Semua pesan berdasarkan role berhasil dihapus."
}
```

### 3. Hapus seluruh percakapan
- `DELETE /admin/messages/conversations/{conversationId}`

Response:
```json
{
  "success": true,
  "message": "Percakapan berhasil dihapus."
}
```

## Aturan Logic
- Hanya `admin` yang boleh melakukan moderasi.
- Hapus pesan sebaiknya `soft delete` jika masih butuh audit.
- Jika policy retensi 24 jam dipakai, moderasi manual admin harus tetap tercatat di audit log.
- Setelah delete, `last_message`, `last_message_at`, dan `message_count` di conversation harus dihitung ulang.
- Jika seluruh pesan habis, conversation bisa:
  - tetap ada dengan state kosong, atau
  - otomatis ikut dihapus.

Rekomendasi:
- Jika semua pesan hilang, biarkan conversation tetap ada dulu agar histori relasi tidak langsung hilang.

## Audit Log yang Disarankan
- `admin_id`
- `conversation_id`
- `message_id` nullable
- `action`
- `target_role` nullable
- `reason` nullable
- `created_at`

Contoh `action`:
- `delete_message`
- `purge_student_messages`
- `purge_lecturer_messages`
- `delete_conversation`

## Risiko Jika Tidak Dibuat Backend
- UI terlihat berhasil, tapi data asli tidak berubah.
- Reload halaman akan mengembalikan chat lama.
- Admin mengira moderasi sudah final padahal belum tersimpan.
