# Backend: Auto-generate Kode Course (KRS/WEB)

## Konteks
Frontend kini menampilkan kode kursus & webinar sebagai field readonly yang otomatis terisi. Backend perlu menyediakan variabel `$nextKursusCode` dan `$nextWebinarCode` ke view, serta auto-generate kode saat store jika tidak diisi.

## Yang Perlu Dilakukan

### 1. Di Controller `kelolaKursus()` (method yang render halaman)
Tambahkan logic generate next code:

```php
// Hitung kode kursus berikutnya
$lastKursus = Course::where('kategori', 'kursus')
    ->where('kode_course', 'like', 'KRS%')
    ->orderByRaw("CAST(SUBSTRING(kode_course, 4) AS UNSIGNED) DESC")
    ->first();
$nextKursusNum = $lastKursus ? (int) substr($lastKursus->kode_course, 3) + 1 : 1;
$nextKursusCode = 'KRS' . str_pad($nextKursusNum, 2, '0', STR_PAD_LEFT);

// Hitung kode webinar berikutnya
$lastWebinar = Course::where('kategori', 'webinar')
    ->where('kode_course', 'like', 'WEB%')
    ->orderByRaw("CAST(SUBSTRING(kode_course, 4) AS UNSIGNED) DESC")
    ->first();
$nextWebinarNum = $lastWebinar ? (int) substr($lastWebinar->kode_course, 3) + 1 : 1;
$nextWebinarCode = 'WEB' . str_pad($nextWebinarNum, 2, '0', STR_PAD_LEFT);
```

Pass ke view:
```php
compact('courses', 'dosenList', 'jurusanList', 'nextKursusCode', 'nextWebinarCode')
```

### 2. Di Controller `storeKursus()` (method store)
Jika `kode_course` kosong atau tidak diisi, auto-generate:

```php
if (empty($request->kode_course)) {
    $prefix = $request->kategori === 'webinar' ? 'WEB' : 'KRS';
    $last = Course::where('kode_course', 'like', $prefix . '%')
        ->orderByRaw("CAST(SUBSTRING(kode_course, " . (strlen($prefix) + 1) . ") AS UNSIGNED) DESC")
        ->first();
    $nextNum = $last ? (int) substr($last->kode_course, strlen($prefix)) + 1 : 1;
    $kode = $prefix . str_pad($nextNum, 2, '0', STR_PAD_LEFT);
} else {
    $kode = $request->kode_course;
}
```

### 3. Di Controller `storeKursus()` — handle field webinar baru
Field baru dari modal webinar yang perlu di-handle:
- `status` — 'draft' atau 'aktif' (dari hidden input + toggle)
- `akses_publik` — boolean
- `sertifikat` — boolean
- `tanggal_webinar`, `jam_mulai_webinar`, `jam_selesai_webinar`
- `youtube_playlist` — dipakai untuk link meeting webinar

### 4. Validation Update
Hapus `required` pada `kode_course` validation (karena sekarang auto-generate):
```php
// Dari:
'kode_course' => 'required|string|unique:courses,kode_course',
// Menjadi:
'kode_course' => 'nullable|string|unique:courses,kode_course',
```
