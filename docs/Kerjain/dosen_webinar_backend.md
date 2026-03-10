# Backend: Dosen Webinar Form Support

## Overview
Frontend sekarang mengirim field webinar (tanggal, jam, kuota) saat kategori = `webinar` dipilih di `buat-kursus` dan `edit-kursus`. Backend perlu migrasi dan update controller.

## 1. Migration — Tambah Kolom Webinar di `courses`

```php
Schema::table('courses', function (Blueprint $table) {
    $table->date('tanggal_webinar')->nullable()->after('kategori');
    $table->time('jam_mulai_webinar')->nullable()->after('tanggal_webinar');
    $table->time('jam_selesai_webinar')->nullable()->after('jam_mulai_webinar');
    $table->unsignedInteger('kuota_peserta')->nullable()->after('jam_selesai_webinar');
});
```

## 2. Course Model — Update `$fillable` dan `$casts`

Tambahkan ke `$fillable`:
```php
'tanggal_webinar', 'jam_mulai_webinar', 'jam_selesai_webinar', 'kuota_peserta'
```

Tambahkan ke `$casts`:
```php
'tanggal_webinar' => 'date',
```

## 3. DosenController — `storeCourse()` Validation

Tambahkan validasi conditional:
```php
'tanggal_webinar'     => 'required_if:kategori,webinar|nullable|date|after_or_equal:today',
'jam_mulai_webinar'   => 'required_if:kategori,webinar|nullable|date_format:H:i',
'jam_selesai_webinar' => 'required_if:kategori,webinar|nullable|date_format:H:i|after:jam_mulai_webinar',
'kuota_peserta'       => 'nullable|integer|min:1',
```

Tambahkan field ke `Course::create()`:
```php
'tanggal_webinar'     => $request->tanggal_webinar,
'jam_mulai_webinar'   => $request->jam_mulai_webinar,
'jam_selesai_webinar' => $request->jam_selesai_webinar,
'kuota_peserta'       => $request->kuota_peserta,
```

## 4. DosenController — `updateCourse()` Validation

Sama seperti `storeCourse()`, tambahkan validasi dan update field yang sama.

## 5. Field Mapping (Frontend → Backend)

| Form Field Name      | DB Column              | Type     | Kondisi           |
|----------------------|------------------------|----------|--------------------|
| `tanggal_webinar`    | `tanggal_webinar`      | date     | required if webinar|
| `jam_mulai_webinar`  | `jam_mulai_webinar`    | time     | required if webinar|
| `jam_selesai_webinar`| `jam_selesai_webinar`  | time     | required if webinar|
| `kuota_peserta`      | `kuota_peserta`        | integer  | optional           |
| `youtube_playlist`   | `youtube_playlist`     | string   | dipakai sebagai link meeting utk webinar |

## 6. Catatan

- `youtube_playlist` sudah ada di DB — dipakai ganda: sebagai YouTube playlist (kursus) atau link meeting Zoom/Google Meet (webinar).
- Admin webinar modal (`kursus.blade.php`) juga punya field yang sama tapi belum ada kolom DB-nya. Migrasi ini akan mendukung keduanya sekaligus.
