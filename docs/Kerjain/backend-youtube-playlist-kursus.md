# Backend YouTube Playlist Kursus

## Tujuan

Membuat field `youtube_playlist` pada kursus benar-benar terhubung ke backend, sehingga:

1. URL playlist tersimpan di tabel `courses`
2. Semua video playlist disinkron ke tabel `youtube_playlist_videos`
3. Semua video playlist otomatis dibuat menjadi `course_materials`
4. Mahasiswa bisa memutar seluruh video lewat flow belajar kursus yang sudah ada

## Flow yang Dipakai Sekarang

1. Admin simpan atau update kursus dengan `kategori = kursus`
2. Backend validasi URL playlist YouTube
3. Service ambil isi playlist
4. Video hasil fetch disimpan ke `youtube_playlist_videos`
5. Service membuat / memperbarui modul otomatis `Playlist YouTube Otomatis`
6. Setiap video playlist diubah jadi materi `tipe = video` pada modul tersebut
7. Video lama yang sudah tidak ada di playlist ikut dibersihkan

## Catatan Penting

1. Backend sekarang memprioritaskan `YouTube Data API` lewat `YOUTUBE_API_KEY`
2. Ini jadi source utama untuk playlist panjang karena lebih stabil dan hasilnya lebih lengkap
3. Jika API gagal, backend masih punya fallback ke mode web publik / RSS
4. Jika kursus diubah ke kategori non-`kursus` atau URL playlist dikosongkan, data sync playlist otomatis dibersihkan

## File yang Terlibat

1. `app/Services/YoutubePlaylistService.php`
2. `app/Http/Controllers/Auth/AdminController.php`
3. `app/Models/Course.php`
4. `resources/views/Auth/admin/kursus.blade.php`
5. `config/services.php`

## Environment

Tambahkan `YOUTUBE_API_KEY` di `.env`:

```env
YOUTUBE_API_KEY=your_youtube_data_api_key
```

## Endpoint Terkait

1. `POST /admin/kursus/{id}/sync-playlist`
2. `GET /admin/kursus/{id}/youtube-videos`

## Dampak ke Frontend

1. Modal edit kursus admin sekarang punya preview video playlist
2. Admin bisa trigger `Sinkronkan Playlist` manual
3. Setelah sync berhasil, daftar video tampil dan masing-masing punya tombol `Putar`
