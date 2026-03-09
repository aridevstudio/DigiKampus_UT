# Backend: Tambahkan Admin ke Daftar Dosen Pengampu

## Konteks
Di halaman **Kelola Kursus** (`kursus.blade.php`), dropdown dosen pengampu sekarang harus bisa menampilkan admin juga sebagai pilihan, bukan hanya user dengan role `dosen`.

## File yang perlu diubah

### `app/Http/Controllers/Auth/AdminController.php` — method `showKursus()`

**Saat ini:**
```php
$dosenList = User::where('role', 'dosen')->where('status', 'aktif')->get();
```

**Perlu diubah menjadi:**
```php
$dosenList = User::where('status', 'aktif')
    ->whereIn('role', ['dosen', 'admin'])
    ->orderBy('name')
    ->get();
```

Ini memungkinkan admin juga bisa dipilih sebagai dosen pengampu kursus di dropdown yang searchable.

## Catatan
- Frontend sudah menggunakan searchable dropdown (Alpine.js) untuk memilih dosen
- Field `id_dosen` tetap sama, tidak ada perubahan schema database
- Pastikan admin yang tampil memiliki `status = 'aktif'`
