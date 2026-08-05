# Private Storage Deployment Rules

## Tujuan

File berikut wajib diakses melalui controller authorization dan tidak boleh diberikan langsung oleh web server:

```text
/storage/assignments/*
/storage/attendance/*
/storage/bootcamp-sesi-materi/*
/storage/bacaan-lampiran/*
```

Asset public berikut tidak boleh ikut diblokir:

```text
/storage/profiles/*
/storage/course-thumbnails/*
/storage/bootcamp-thumbnails/*
/storage/news-thumbnails/*
/storage/apps/*
```

## Apache

Tambahkan rule berikut pada konfigurasi virtual host atau `.htaccess` di `public/` setelah memastikan lokasi tersebut memang berada di document root aplikasi:

```apache
RewriteEngine On

# Block legacy private prefixes from the public storage symlink.
RewriteRule ^storage/(assignments|attendance|bootcamp-sesi-materi|bacaan-lampiran)(/|$) - [R=404,L,NC]
```

Jika server tidak meneruskan 404 melalui rewrite, gunakan:

```apache
RedirectMatch 404 ^/storage/(assignments|attendance|bootcamp-sesi-materi|bacaan-lampiran)(/|$)
```

Jangan menambahkan rule wildcard seperti `/storage/*` karena avatar dan thumbnail memang public.

## Nginx

Letakkan rule sebelum `location /` pada server block yang melayani aplikasi:

```nginx
location ~ ^/storage/(assignments|attendance|bootcamp-sesi-materi|bacaan-lampiran)(/|$) {
    return 404;
}
```

Biarkan symbolic link public untuk asset public tetap bekerja melalui location umum yang existing.

## Verifikasi aman

Jalankan setelah deploy ke staging atau production yang diizinkan:

```text
GET /storage/assignments/<known-file>
GET /storage/attendance/<known-file>
GET /storage/bootcamp-sesi-materi/<known-file>
GET /storage/bacaan-lampiran/<known-file>
```

Expected:

```text
404 Not Found
```

Kemudian verifikasi public asset:

```text
GET /storage/profiles/<known-avatar>
GET /storage/course-thumbnails/<known-thumbnail>
GET /storage/news-thumbnails/<known-thumbnail>
```

Expected:

```text
200 OK
```

## Catatan migrasi

Rule deny harus dipasang setelah file legacy sudah disalin dan diverifikasi pada private disk, atau sebagai protective control selama fase migrasi jika business owner menyetujui bahwa direct legacy URLs boleh segera berhenti bekerja.

Rule ini tidak menghapus file dan tidak mengubah database. Deployment owner harus melakukan reload konfigurasi web server setelah review.
