# Laravel Security Hardening Report

**Tanggal laporan:** 5 Agustus 2026  
**Branch:** `Develop`  
**Status commit:** Belum di-commit  
**Jenis pekerjaan:** Security review dan defensive hardening  
**Ruang lingkup utama:** Authorization, role isolation, private file storage, file download authorization, stored XSS sink review, API response exposure, security headers, dependency audit, dan regression testing.

> Dokumen ini merekam keadaan working tree pada saat audit. Laporan ini tidak menyatakan bahwa aplikasi sudah production-ready atau bahwa server production sudah aman sepenuhnya. Beberapa validasi masih terblokir oleh konfigurasi test database dan dependency vulnerabilities.

---

## 1. Executive Summary

Aplikasi Laravel telah menerima sejumlah patch hardening yang berfokus pada pemisahan authentication dan authorization, perlindungan resource berdasarkan ownership, serta pemindahan upload yang sensitif dari public storage ke private storage.

Perubahan paling penting:

1. Menambahkan middleware role untuk membatasi API mahasiswa, dosen, dan admin.
2. Menambahkan Policy/Gate untuk course, assignment submission, dan attendance proof.
3. Mengubah submission tugas, bukti attendance, materi sesi bootcamp, dan attachment bacaan baru agar disimpan pada `storage/app/private` melalui disk `local`.
4. Menambahkan controller download yang melakukan authorization sebelum memanggil `Storage::download()` atau mengakses path file.
5. Membatasi fallback ke file lama pada public storage hanya untuk prefix legacy tertentu.
6. Menghapus akses langsung `asset('storage/...')` dari beberapa file private pada view dan menggantinya dengan route download terotorisasi.
7. Mengganti beberapa rendering `x-html` menjadi `x-text` untuk mengurangi risiko stored XSS pada konten yang tidak membutuhkan HTML.
8. Menghapus metadata OAuth/provider dari beberapa API resource.
9. Menambahkan security headers dasar dan Content Security Policy dalam mode report-only.
10. Menetapkan expiration Sanctum token default 24 jam pada konfigurasi yang sedang diuji.
11. Menambahkan regression test untuk role boundary, policy registration, file ownership, path traversal, dan private disk.
12. Tidak mengubah password flow default NIM.

### Penilaian risiko saat ini

**Risk level: HIGH — belum layak dinyatakan final untuk production.**

Alasan utama:

- `composer audit` menemukan **40 security advisories pada 13 package**.
- Terdapat advisory **critical/high** pada `dedoc/scramble`, `phpoffice/phpspreadsheet`, Laravel/Symfony, Guzzle, dan phpseclib.
- File private lama yang masih berada di `storage/app/public` masih berpotensi diakses langsung melalui `/storage/...` sampai migrasi selesai.
- Full feature test belum dapat berjalan karena migration lain masih menjalankan SQL MySQL `ALTER TABLE ... MODIFY` pada SQLite.
- Pint masih menemukan 8 style issues pada file yang diperiksa.
- CSP masih `Report-Only` dan masih mengizinkan `unsafe-inline`; ini belum merupakan mitigasi XSS yang kuat.
- Tidak dilakukan verifikasi langsung pada server production, konfigurasi Nginx/Apache, PHP-FPM, permission filesystem, cron, atau timestamp file.

---

## 2. Status Working Tree

Working tree berada pada branch:

```text
Develop...origin/Develop
```

Belum ada commit yang dibuat oleh pekerjaan ini.

### Perubahan tracked yang terlihat

Pada saat pemeriksaan, terdapat **38 file tracked yang berubah**, dengan statistik sekitar:

```text
509 insertions
78 deletions
```

File tracked yang berubah antara lain:

```text
.gitignore
app/Http/Controllers/Api/Auth/MahasiswaAuthController.php
app/Http/Controllers/Api/Dosen/DosenMessageController.php
app/Http/Controllers/Auth/Admin/BootcampSesiController.php
app/Http/Controllers/Auth/AdminContentApiController.php
app/Http/Controllers/Auth/AdminController.php
app/Http/Controllers/Auth/DosenContentApiController.php
app/Http/Controllers/Auth/DosenController.php
app/Http/Controllers/Mahasiswa/CourseController.php
app/Http/Controllers/Mahasiswa/ProfileController.php
app/Http/Middleware/EnsureAuthenticatedAdmin.php
app/Http/Middleware/EnsureAuthenticatedDosen.php
app/Http/Middleware/EnsureAuthenticatedMahasiswa.php
app/Http/Resources/AdminResource.php
app/Http/Resources/DosenResource.php
app/Models/BootcampSession.php
app/Providers/AppServiceProvider.php
bootstrap/app.php
config/filesystems.php
database/migrations/2026_02_22_200001_expand_courses_tipe_enum.php
database/migrations/2026_02_22_200002_separate_kategori_from_tipe.php
database/migrations/2026_02_25_000003_make_id_mahasiswa_nullable_on_agendas_table.php
database/migrations/2026_02_28_011710_add_pending_to_users_status_enum.php
database/migrations/2026_03_19_200001_expand_enrollment_status_enum.php
resources/views/Auth/admin/bootcamp-attendance.blade.php
resources/views/Auth/admin/kelola-bacaan.blade.php
resources/views/Auth/dosen/detail-kursus.blade.php
resources/views/Auth/dosen/kelola-bacaan.blade.php
resources/views/pages/mahasiswa/apps.blade.php
resources/views/pages/mahasiswa/bootcamp-detail.blade.php
resources/views/pages/mahasiswa/course-learn.blade.php
resources/views/pages/mahasiswa/news.blade.php
routes/admin.php
routes/api.php
routes/dosen.php
routes/mahasiswa.php
tests/Feature/Api/SecurityFixesTest.php
```

### File baru yang belum tracked

Pada saat inspeksi awal, file baru aplikasi yang belum tracked adalah:

```text
app/Http/Middleware/EnsureUserRole.php
app/Http/Middleware/SecurityHeaders.php
app/Policies/AssignmentSubmissionPolicy.php
app/Policies/BootcampLiveClassAttendancePolicy.php
app/Policies/CoursePolicy.php
app/Services/PrivateFileService.php
tests/Unit/PrivateFileServiceTest.php
```

File laporan ini dibuat setelah inspeksi status awal, sehingga sekarang juga muncul sebagai file baru:

```text
docs/security_hardening_report.md
```

Angka 38 file tracked pada laporan ini adalah snapshot sebelum file laporan dibuat dan mencakup entry dirty `.agent/skills`. Saat menyiapkan commit, file laporan harus dihitung/dipilih secara terpisah.

### Catatan ownership dan commit

Perubahan dalam working tree cukup luas dan mencakup hardening authorization, storage, security headers, resource exposure, view, serta beberapa migration. Sebelum commit, perubahan sebaiknya dipisahkan atau setidaknya direview per kelompok karena tidak seluruh perubahan memiliki risiko dan tujuan yang sama.

File `.agent/skills` juga berstatus modified/dirty dan tidak seharusnya digabungkan ke commit aplikasi tanpa verifikasi terpisah.

Perubahan dalam working tree cukup luas dan mencakup hardening authorization, storage, security headers, resource exposure, view, serta beberapa migration. Sebelum commit, perubahan sebaiknya dipisahkan atau setidaknya direview per kelompok karena tidak seluruh perubahan memiliki risiko dan tujuan yang sama.

File `.agent/skills` juga berstatus modified/dirty dan tidak seharusnya digabungkan ke commit aplikasi tanpa verifikasi terpisah.

---

## 3. Perubahan yang Sudah Dikerjakan

## 3.1 Role middleware untuk API

### File

```text
app/Http/Middleware/EnsureUserRole.php
bootstrap/app.php
routes/api.php
```

### Implementasi

Middleware baru membaca role dari user yang sudah diautentikasi:

```php
$user = $request->user();

if (!$user || !in_array((string) $user->role, $roles, true)) {
    return response()->json([
        'success' => false,
        'message' => 'Forbidden.',
    ], 403);
}
```

Alias didaftarkan sebagai:

```php
'role' => \App\Http\Middleware\EnsureUserRole::class,
```

### Route API yang diperketat

Sebelumnya area protected menggunakan pola umum:

```php
Route::middleware('auth:sanctum')->group(function () {
    // endpoint mahasiswa/dosen
});
```

Kemudian scope role ditambahkan:

```php
Route::middleware('auth:sanctum')->group(function () {
    Route::middleware('role:mahasiswa')
        ->prefix('mahasiswa')
        ->group(function () {
            // endpoint mahasiswa
        });

    Route::middleware('role:dosen')
        ->prefix('dosen')
        ->group(function () {
            // endpoint dosen
        });
});

Route::middleware(['auth:sanctum', 'role:admin'])
    ->prefix('admin')
    ->group(function () {
        // endpoint admin
    });
```

### Dampak keamanan

Dengan patch ini, token Sanctum yang valid saja tidak cukup untuk memasuki area role lain. Contoh yang diharapkan:

| Actor | Endpoint | Hasil yang diharapkan |
|---|---|---:|
| Mahasiswa | `GET /api/dosen/profile` | `403 Forbidden` |
| Mahasiswa | `POST /api/dosen/courses` | `403 Forbidden` |
| Dosen | `GET /api/admin/profile` | `403 Forbidden` |
| Admin | `GET /api/dosen/profile` | `403 Forbidden` |

### Batasan validasi

Test feature yang seharusnya memverifikasi route boundary belum dapat mencapai assertions karena proses migrasi SQLite berhenti lebih dahulu pada migration lain yang masih menggunakan sintaks MySQL. Jadi keberadaan patch dapat diinspeksi, tetapi efektivitas end-to-end belum terverifikasi melalui feature test penuh.

---

## 3.2 Role check pada web guards

### File

```text
app/Http/Middleware/EnsureAuthenticatedAdmin.php
app/Http/Middleware/EnsureAuthenticatedDosen.php
app/Http/Middleware/EnsureAuthenticatedMahasiswa.php
```

### Perubahan

Sebelumnya middleware hanya memeriksa apakah guard sudah login:

```php
if (!Auth::guard('admin')->check()) {
    // redirect login
}
```

Sekarang user diambil dan role-nya diverifikasi:

```php
$user = Auth::guard('admin')->user();

if (!$user || $user->role !== 'admin') {
    // redirect atau JSON unauthorized response
}
```

Pola yang sama diterapkan untuk guard dosen dan mahasiswa.

### Tujuan

Defense-in-depth pada web layer untuk mengurangi risiko:

- User dari guard yang salah masuk ke area role lain.
- Session valid tetapi role database tidak sesuai dengan area aplikasi.
- Bypass akibat hanya mengandalkan session existence.

---

## 3.3 Policy dan Gate untuk resource ownership

### File

```text
app/Policies/CoursePolicy.php
app/Policies/AssignmentSubmissionPolicy.php
app/Policies/BootcampLiveClassAttendancePolicy.php
app/Providers/AppServiceProvider.php
```

Policy didaftarkan secara eksplisit:

```php
Gate::policy(Course::class, CoursePolicy::class);
Gate::policy(AssignmentSubmission::class, AssignmentSubmissionPolicy::class);
Gate::policy(
    BootcampLiveClassAttendance::class,
    BootcampLiveClassAttendancePolicy::class
);
```

### CoursePolicy

Policy membedakan katalog, akses content, dan pengelolaan course.

Perilaku yang diterapkan:

- Admin dapat mengakses course.
- Dosen hanya dapat mengakses course yang `id_dosen`-nya sama dengan user login.
- Mahasiswa hanya dapat melihat course berstatus tertentu.
- Mahasiswa untuk content harus memiliki enrollment aktif/berjalan/selesai.
- Dosen/admin dapat mengelola sesuai ownership/role.

Contoh aturan content:

```php
return $user->role === 'mahasiswa'
    && $course->enrollments()
        ->where('id_mahasiswa', $user->id)
        ->whereIn('status', ['aktif', 'in_progress', 'selesai'])
        ->exists();
```

### AssignmentSubmissionPolicy

Aturan akses:

- Admin dapat melihat submission.
- Mahasiswa hanya dapat melihat submission miliknya sendiri.
- Dosen hanya dapat melihat submission dari course yang dimilikinya.

```php
if ($user->role === 'mahasiswa') {
    return (int) $submission->id_mahasiswa === (int) $user->id;
}

return $user->role === 'dosen'
    && $submission->course()->where('id_dosen', $user->id)->exists();
```

### BootcampLiveClassAttendancePolicy

Aturan akses:

- Admin dapat melihat attendance proof.
- Mahasiswa hanya dapat melihat bukti miliknya.
- Dosen hanya dapat melihat bukti attendance pada course miliknya.

### Catatan

Policy sudah didaftarkan, tetapi seluruh kombinasi actor/resource/action tetap perlu diuji dengan database yang compatible agar tidak hanya mengandalkan unit inspection.

---

## 3.4 Authorization pada endpoint pesan dosen

### File

```text
app/Http/Controllers/Api/Dosen/DosenMessageController.php
```

Ditambahkan pemeriksaan `canAccessStudent()` untuk endpoint membaca dan mengirim pesan.

Dosen hanya dapat berinteraksi dengan mahasiswa jika:

1. Mahasiswa ter-enroll pada salah satu course milik dosen tersebut; atau
2. Sudah ada thread pesan langsung antara dosen dan mahasiswa.

Pemeriksaan dilakukan sebelum query pesan atau pembuatan pesan.

Contoh hasil yang diharapkan:

```http
GET /api/dosen/messages/{student-yang-tidak-terkait}
```

```http
403 Forbidden
```

### Risiko yang masih perlu diperhatikan

Implementasi ini memakai query `Course::where('id_dosen', ...)->pluck(...)`, lalu mengecek enrollment/thread. Pada skala besar, query dan index database perlu ditinjau untuk performa. Selain itu, seluruh endpoint pesan lain seperti broadcast, unread count, atau list harus tetap diaudit agar konsistensi authorization terjaga.

---

## 3.5 Private storage untuk file sensitif

### File utama

```text
config/filesystems.php
app/Services/PrivateFileService.php
```

Disk `local` sekarang diarahkan ke:

```php
'root' => storage_path('app/private'),
'serve' => false,
```

Sebelumnya `serve` bernilai `true`; sekarang direct file serving pada disk local dinonaktifkan.

### Service baru

`PrivateFileService` menyediakan:

- Resolusi disk private.
- Fallback sementara untuk file legacy tertentu.
- Download melalui disk yang benar.
- Resolusi path server untuk preview internal.
- Penolakan path traversal.
- Penolakan absolute path.
- Penolakan Windows drive path.
- Penolakan null byte.
- Normalisasi nama file download.

Contoh pemakaian:

```php
return app(PrivateFileService::class)->download(
    $submission->file_path,
    $submission->original_file_name ?: basename($submission->file_path),
);
```

### Path validation

Path berikut ditolak:

```text
../.env
/etc/passwd
\\server\share\file
C:\Windows\win.ini
file\0.pdf
```

Service melempar `NotFoundHttpException` sehingga path sensitif tidak dibocorkan sebagai file system error.

---

## 3.6 Legacy fallback public storage

Migrasi penuh tidak dilakukan secara otomatis untuk menghindari kehilangan file atau breaking change.

Service memprioritaskan:

```text
storage/app/private
```

Jika file tidak ditemukan pada local disk, fallback hanya diizinkan untuk prefix legacy yang diketahui:

```text
assignments/
attendance/
bootcamp-sesi-materi/
bacaan-lampiran/
```

Fallback tidak diizinkan untuk path umum seperti:

```text
profiles/
apps/
unknown/
```

### Tujuan

Pendekatan ini menjaga backward compatibility untuk file lama sekaligus mencegah semua path private-looking dapat mencari file di public storage secara bebas.

### Risiko yang masih tersisa

Selama file lama masih berada di:

```text
storage/app/public/assignments/
storage/app/public/attendance/
storage/app/public/bootcamp-sesi-materi/
storage/app/public/bacaan-lampiran/
```

file tersebut masih berpotensi diakses melalui symbolic link public:

```text
/public/storage/<path>
```

Controller download sudah memiliki authorization, tetapi direct URL lama tidak melewati controller. Karena itu perlindungan file legacy belum final.

---

## 3.7 Assignment submission menjadi private

### File

```text
app/Http/Controllers/Mahasiswa/CourseController.php
app/Http/Controllers/Auth/DosenController.php
routes/mahasiswa.php
routes/dosen.php
resources/views/pages/mahasiswa/bootcamp-detail.blade.php
```

### Sebelum

Submission menggunakan nama file yang mengandung identifier dan disimpan di public disk:

```php
$fileName = "assignment_{$courseId}_{$assignmentId}_{$userId}_" . time() . '.' . $file->getClientOriginalExtension();
$filePath = $file->storeAs('assignments', $fileName, 'public');
```

Link view juga memakai direct asset URL:

```php
asset('storage/' . $submission->file_path)
```

### Sesudah

File baru disimpan di private disk dengan nama acak Laravel:

```php
$filePath = $file->store('assignments', 'local');
```

Link view diarahkan ke route terotorisasi:

```php
route('mahasiswa.assignment-download', [
    'courseId' => $course->id_course,
    'assignmentId' => $a['material_id'],
])
```

### Authorization mahasiswa

Mahasiswa harus:

1. Memiliki enrollment pada course.
2. Memiliki status enrollment yang diizinkan.
3. Mengakses material assignment yang benar.
4. Memiliki submission sendiri.
5. Lolos `AssignmentSubmissionPolicy`.

Mahasiswa lain tidak mendapatkan file submission tersebut.

### Authorization dosen

Dosen harus terkait dengan course submission melalui ownership course. Dosen tidak boleh mengunduh submission dari course dosen lain.

---

## 3.8 Attendance proof menjadi private

### File

```text
app/Http/Controllers/Mahasiswa/CourseController.php
app/Http/Controllers/Auth/AdminController.php
routes/mahasiswa.php
routes/admin.php
resources/views/pages/mahasiswa/course-learn.blade.php
resources/views/Auth/admin/bootcamp-attendance.blade.php
```

### Sebelum

Bukti attendance disimpan di public disk dengan nama berbasis course/user/session/time.

### Sesudah

File baru disimpan menggunakan nama acak pada local disk:

```php
$filePath = $file->store('attendance', 'local');
```

Mahasiswa mengakses melalui route:

```text
/bootcamp/{courseId}/attendance/{sessionKey}/download
```

Controller memastikan:

- User adalah mahasiswa login.
- User memiliki enrollment pada course.
- Enrollment memiliki status yang diizinkan.
- Attendance adalah milik user tersebut.
- Attendance sudah berstatus verified.
- File benar-benar tersedia melalui `PrivateFileService`.

Admin menggunakan route:

```text
/bootcamp-live-class-attendance/{id}/download
```

dan authorization policy attendance.

---

## 3.9 Bootcamp session material menjadi private

### File

```text
app/Http/Controllers/Auth/Admin/BootcampSesiController.php
app/Http/Controllers/Mahasiswa/CourseController.php
app/Models/BootcampSession.php
routes/mahasiswa.php
resources/views/pages/mahasiswa/bootcamp-detail.blade.php
resources/views/pages/mahasiswa/course-learn.blade.php
```

### Upload baru

Materi sesi baru disimpan ke local disk:

```php
return $request->file($field)->store('bootcamp-sesi-materi', 'local');
```

### Download mahasiswa

Mahasiswa harus memiliki enrollment yang sesuai dan course content authorization sebelum download.

### Perubahan model

`BootcampSession::materiUrl()` tidak lagi membuat public `Storage::url()` dari `materi_file`. Method tersebut hanya mengembalikan `materi_url` yang memang merupakan URL eksternal/raw URL.

### Catatan backward compatibility

Materi lama yang masih dicatat sebagai `materi_file` dan masih tersimpan di public storage tetap membutuhkan migrasi terencana.

---

## 3.10 Attachment bacaan menjadi private

### File

```text
app/Http/Controllers/Auth/AdminContentApiController.php
app/Http/Controllers/Auth/DosenContentApiController.php
app/Http/Controllers/Auth/DosenController.php
routes/admin.php
routes/dosen.php
routes/mahasiswa.php
resources/views/Auth/admin/kelola-bacaan.blade.php
resources/views/Auth/dosen/kelola-bacaan.blade.php
resources/views/pages/mahasiswa/bootcamp-detail.blade.php
```

### Upload baru

Attachment bacaan sekarang menggunakan:

```php
$request->file('lampiran_file')->store('bacaan-lampiran', 'local');
```

### URL response

Beberapa response API yang sebelumnya mengembalikan:

```php
asset('storage/' . $module->lampiran_path)
```

sekarang mengembalikan route controller download.

### Catatan penting

Audit menemukan bahwa beberapa response API masih mengembalikan field internal `lampiran_path`, khususnya pada jalur `DosenContentApiController`. Field path internal sebaiknya tidak dikirim ke client jika client hanya membutuhkan URL download yang terotorisasi.

Tindak lanjut yang direkomendasikan:

```php
// Hindari
'lampiran_path' => $material->lampiran_path,

// Gunakan
'lampiran_url' => $material->lampiran_path
    ? route('dosen.material-attachment.download', [...])
    : null,
```

Perubahan ini belum diterapkan dalam laporan ini karena permintaan saat ini adalah mendokumentasikan pekerjaan yang sudah dilakukan, bukan memperluas patch tanpa persetujuan.

---

## 3.11 Public upload hardening yang sudah ada/terlihat

Public storage tetap dipertahankan untuk asset yang memang intended public, antara lain:

- Avatar/profile photo.
- Logo atau app icon.
- Course thumbnail.
- Bootcamp thumbnail.
- News thumbnail.
- Certificate template image.

Beberapa handler memakai `store()` sehingga nama file dibuat acak oleh Laravel, misalnya:

```php
$path = $photo->store('profile_photos', 'public');
```

dan:

```php
$thumbnailPath = $request->file('thumbnail')->store('course-thumbnails', 'public');
```

### Validasi yang teridentifikasi

Beberapa handler sudah menggunakan kombinasi:

```text
image
mimes:jpg,jpeg,png,webp
max:5120
```

Materi bootcamp menggunakan allowlist seperti:

```text
mimes:pdf,zip,ppt,pptx,doc,docx
max:51200
```

Attendance proof menggunakan:

```text
mimes:jpg,jpeg,png,webp,pdf
max:5120
```

### Batasan audit

Belum semua public upload handler diverifikasi dengan standar yang sama. Masih perlu audit lanjutan untuk memastikan setiap public upload:

- Memakai MIME detection berbasis isi file.
- Memiliki extension allowlist.
- Memiliki batas ukuran.
- Tidak memakai nama asli user sebagai path.
- Tidak menerima `.php`, `.phtml`, `.php5`, `.phar`, `.exe`, atau `.sh`.
- Tidak dapat dieksekusi sebagai script oleh web server.
- Untuk gambar, idealnya dire-encode agar mengurangi risiko polyglot/malicious metadata.

---

## 3.12 Penyimpanan profile photo dan public asset

Perubahan yang diterapkan:

- Beberapa nama file profile photo yang sebelumnya dibentuk manual kini memakai `store()`.
- File profile photo tetap public karena memang dipakai sebagai avatar UI.
- Cleanup tetap mencoba menghapus file dari public disk.

Contoh kategori public yang sesuai:

| Kategori | Disk yang sesuai | Alasan |
|---|---|---|
| Avatar | `public` | Ditampilkan lintas halaman dan tidak diperlakukan sebagai dokumen rahasia |
| Course thumbnail | `public` | Asset katalog |
| Bootcamp thumbnail | `public` | Asset promosi/katalog |
| News thumbnail | `public` | Konten publik |
| App icon | `public` | Ditampilkan pada katalog aplikasi |
| Certificate template image | `public` atau asset terkontrol | Tergantung apakah template memang public |
| Assignment | `local` | Mengandung pekerjaan/data mahasiswa |
| Attendance proof | `local` | Bukti administratif milik user |
| Academic document | `local` | Data sensitif/ownership-based |
| Bacaan attachment | `local` | Akses seharusnya melalui course authorization |
| Session material | `local` | Akses seharusnya melalui enrollment/course authorization |

---

## 3.13 Stored XSS dan raw HTML rendering

### Perubahan yang diterapkan

Target rendering konten bacaan dan berita diubah dari `x-html` menjadi `x-text`.

Sebelum:

```html
<div x-html="selectedNews?.konten"></div>
```

Sesudah:

```html
<div x-text="selectedNews?.konten"></div>
```

Perubahan juga diterapkan pada preview konten bacaan admin dan dosen.

### Dampak

Konten database tidak lagi diparse sebagai HTML pada sink tersebut. Payload seperti:

```html
<script>alert(1)</script>
<img src=x onerror=alert(1)>
```

akan ditampilkan sebagai teks, bukan dieksekusi sebagai markup.

### Hasil pencarian sink

Pencarian source menunjukkan tidak ada lagi `x-html` pada target view yang diubah. Namun masih terdapat banyak penggunaan JavaScript `innerHTML` untuk UI dinamis.

`innerHTML` tidak otomatis berarti vulnerability, tetapi menjadi risiko jika nilai yang dimasukkan berasal dari user/database/API tanpa escaping. Area yang perlu audit lanjutan antara lain:

- Import preview admin.
- Notification list.
- Finance report.
- Playlist list.
- Participant list.
- Comment rendering.
- Dynamic table generation.

Temuan ini harus dibedakan antara:

- Static trusted markup.
- Data API yang diinterpolasi ke template string.
- Data user yang langsung dimasukkan ke `innerHTML`.

### Raw Blade HTML

Ditemukan penggunaan raw Blade HTML pada beberapa area. Salah satu yang teridentifikasi menggunakan nilai yang telah di-escape:

```php
{!! nl2br(e($currentMaterial['content'])) !!}
```

Ini berbeda dari raw output tanpa escaping. Namun semua penggunaan `{!! !!}` tetap perlu direview per konteks agar tidak menganggap semua raw HTML aman.

### Rekomendasi rich text

Jika rich text memang diperlukan, jangan mengembalikan `x-html` secara langsung. Gunakan sanitizer allowlist yang eksplisit:

```text
Allowed tags:
p, b, i, ul, li

Blocked:
script
iframe
object
embed
style
onclick
onerror
javascript:
data:
```

---

## 3.14 Security headers

### File

```text
app/Http/Middleware/SecurityHeaders.php
bootstrap/app.php
```

Middleware menambahkan:

```http
X-Content-Type-Options: nosniff
X-Frame-Options: SAMEORIGIN
Referrer-Policy: strict-origin-when-cross-origin
Permissions-Policy: camera=(), microphone=(), geolocation=()
Strict-Transport-Security: max-age=31536000; includeSubDomains
```

CSP ditambahkan dalam mode report-only:

```http
Content-Security-Policy-Report-Only: default-src 'self'; ...
```

### Tujuan report-only

Mode report-only dipilih untuk menghindari breaking change pada halaman yang masih bergantung pada inline script/style dan dependency eksternal. Ini memungkinkan inventory violation sebelum enforcement.

### Risiko yang tersisa

CSP saat ini masih mengandung:

```text
'unsafe-inline'
```

pada `style-src` dan `script-src`, sehingga mitigasi XSS belum maksimal. CSP juga belum mengatur `report-to`/`report-uri` ke endpoint monitoring yang tervalidasi.

Rekomendasi:

1. Inventarisasi violation.
2. Migrasikan inline script ke file terpisah atau nonce/hash.
3. Hapus `unsafe-inline` secara bertahap.
4. Ubah Report-Only menjadi enforcement setelah regression test UI.
5. Jangan mengizinkan domain wildcard tanpa alasan.

---

## 3.15 HTTPS dan trusted proxy

### File

```text
app/Providers/AppServiceProvider.php
bootstrap/app.php
```

URL scheme dipaksa HTTPS pada environment non-local.

`APP_URL` juga digunakan sebagai root URL, dengan upgrade `http://` menjadi `https://` pada non-local.

Trusted proxy dikonfigurasi dengan:

```php
'env('TRUSTED_PROXIES', '*')
```

### Risiko

Source code menggunakan `env('TRUSTED_PROXIES', '*')`. Tanda `*` adalah fallback/default ketika variable environment tidak tersedia; nilai `.env` production aktual belum diverifikasi. Jika production benar-benar menggunakan `*`, konfigurasi tersebut praktis menerima forwarded headers dari semua proxy. Jika aplikasi dapat menerima traffic langsung dari sumber yang tidak dipercaya, header `X-Forwarded-*` dapat dipalsukan dan memengaruhi deteksi scheme/host.

Rekomendasi production:

```env
TRUSTED_PROXIES=<IP atau CIDR proxy resmi>
```

Jangan menggunakan `*` kecuali deployment architecture benar-benar memastikan hanya proxy terpercaya yang dapat mencapai aplikasi.

---

## 3.16 API response exposure

### File

```text
app/Http/Resources/AdminResource.php
app/Http/Resources/DosenResource.php
```

Field berikut dihapus dari response resource:

```text
google_id
provider
```

Tujuannya mengurangi kebocoran metadata OAuth/provider internal.

### Field sensitif lain yang masih harus diaudit

Pastikan response API tidak mengembalikan:

```text
password
password_reset_token
remember_token
Sanctum token plaintext
API key
secret
internal storage path
private file URL
provider metadata yang tidak diperlukan
```

Audit lanjutan masih diperlukan pada semua Resource, controller response, debug endpoint, dan exception response.

---

## 3.17 Sanctum token expiration

Konfigurasi Sanctum menunjukkan:

```php
'expiration' => env('SANCTUM_TOKEN_EXPIRATION', 1440),
```

Artinya default token expiration adalah 1440 menit atau 24 jam, kecuali environment mengganti nilai tersebut.

### Catatan

Hal berikut masih harus diverifikasi di production:

- Nilai environment aktual.
- Apakah token lama sudah dicabut setelah perubahan konfigurasi.
- Apakah logout melakukan revocation.
- Apakah login baru mencabut token lama untuk semua role.
- Apakah token personal dan SPA session memiliki kebijakan berbeda.
- Apakah token disimpan aman di client.

---

## 3.18 Login token revocation

Regression test ditambahkan untuk memastikan login dosen dan admin kedua tidak menambah token aktif tanpa batas.

Ekspektasi test:

```text
Login pertama  -> 1 token
Login kedua    -> tetap 1 token
```

Validasi feature belum dapat mencapai test ini karena migrasi SQLite berhenti lebih awal.

---

## 3.19 Password flow default NIM

**Tidak ada perubahan kode pada mekanisme password default NIM.**

Tidak dilakukan:

- Penggantian password default.
- Migrasi password existing.
- Pemaksaan reset password baru.
- Perubahan import/bulk-user password flow.
- Penghapusan akun lama.

### Risiko yang tetap ada

Jika password default sama dengan nomor induk, user yang mengetahui nomor induk dapat mencoba mengambil alih akun, terutama jika:

- User belum pernah mengganti password.
- Tidak ada `requires_password_reset` enforcement.
- Tidak ada MFA.
- Tidak ada detection login abnormal.
- Nomor induk mudah diperoleh dari dokumen atau endpoint publik.

### Rekomendasi tanpa breaking change

1. Pertahankan flow existing untuk kompatibilitas sementara.
2. Tandai akun yang masih memakai default password.
3. Minta reset password pada login berikutnya secara bertahap.
4. Terapkan rate limiting dan lockout.
5. Tambahkan notifikasi login baru.
6. Tambahkan MFA untuk admin/dosen.
7. Buat migration plan dengan stakeholder sebelum mengubah default password.

---

## 4. Klasifikasi Storage

## 4.1 Public files

File berikut secara konsep cocok tetap berada di:

```text
storage/app/public
```

| File/kategori | Kondisi | Rekomendasi |
|---|---|---|
| Avatar user | Public by design | Gunakan random filename, image MIME validation, size limit |
| Logo/app icon | Public by design | Tetap public, allowlist image dan non-executable |
| Course thumbnail | Public by design | Tetap public, random filename dan image validation |
| Bootcamp thumbnail | Public by design | Tetap public, random filename dan image validation |
| News thumbnail | Public by design | Tetap public, image validation dan output URL terkontrol |
| Certificate template image | Tergantung kebutuhan | Pastikan tidak berisi data pribadi dan validasi image |

## 4.2 Private files

| File/kategori | Sebelum | Sesudah untuk upload baru | Risiko legacy |
|---|---|---|---|
| Assignment submission | Public | Local/private | File lama masih bisa direct URL |
| Attendance proof | Public | Local/private | File lama masih bisa direct URL |
| Bootcamp session material | Public | Local/private | File lama perlu migrasi |
| Bacaan attachment | Public | Local/private | File lama perlu migrasi |
| Dokumen akademik | Perlu audit tiap handler | Direkomendasikan local/private | Belum seluruh handler diverifikasi |
| File ujian | Perlu audit tiap handler | Direkomendasikan local/private | Belum ditemukan/ditutup seluruhnya |

---

## 5. Upload Security Review

### Perubahan yang sudah dilakukan

- Submission baru memakai `store(..., 'local')`.
- Attendance proof baru memakai `store(..., 'local')`.
- Bootcamp material baru memakai `store(..., 'local')`.
- Bacaan attachment baru memakai `store(..., 'local')`.
- Profile photo API memakai `store(..., 'public')` sehingga nama file acak Laravel.
- Sejumlah public asset juga memakai `store()`.

### Validasi yang teridentifikasi

- Image validation untuk thumbnail/photo pada beberapa handler.
- MIME extension allowlist.
- Max size.
- File upload pada materi bootcamp dibatasi ke dokumen umum.
- Attendance proof dibatasi ke image/PDF.

### Gap yang masih harus ditutup

Audit handler upload lengkap harus memastikan semua field berikut tervalidasi konsisten:

```text
thumbnail
foto
photo
lampiran_file
materi_file
proof_file
file
```

Checklist per handler:

```text
[ ] required/nullable sesuai business rule
[ ] file/image rule
[ ] MIME allowlist
[ ] extension allowlist
[ ] max file size
[ ] random server-side filename
[ ] original filename hanya metadata
[ ] storage disk sesuai klasifikasi
[ ] tidak ada executable extension
[ ] tidak memakai user input sebagai path
[ ] cleanup file lama tidak menghapus path di luar root
[ ] web server tidak mengeksekusi upload
```

---

## 6. Endpoint Download yang Ditambahkan/Diubah

### Mahasiswa

```text
GET /bootcamp/{courseId}/attendance/{sessionKey}/download
GET /bootcamp/{courseId}/session/{sessionId}/material/download
GET /course/{courseId}/material/{materialId}/attachment
GET /course/{courseId}/assignment/{assignmentId}/download
```

### Dosen

```text
GET /{courseId}/material/{materialId}/attachment
GET /tugas/submission/{submissionId}/download
```

### Admin

```text
GET /kursus/{courseId}/material/{materialId}/attachment
GET /bootcamp-live-class-attendance/{id}/download
```

### Prinsip akses

Semua endpoint private file seharusnya mengikuti alur:

```text
Request
  -> Authentication
  -> Role boundary
  -> Resource lookup scoped
  -> Policy/Gate authorization
  -> PrivateFileService path validation
  -> Storage download/path response
```

---

## 7. Temuan Keamanan yang Masih Terbuka

## Finding H-01 — Legacy private files masih dapat diakses melalui public symlink

**Severity:** High  
**Status:** Open / mitigated only for new upload flow  
**Lokasi:** `storage/app/public/assignments`, `attendance`, `bootcamp-sesi-materi`, `bacaan-lampiran`  

### Bukti

Public disk masih memiliki symbolic link:

```text
public/storage -> storage/app/public
```

File lama pada prefix private masih berada di bawah public root.

### Dampak

Attacker yang mengetahui atau menebak path dapat mengunduh file tanpa melewati authorization controller.

### Exploit scenario aman

Tanpa mengubah data, lakukan verifikasi pada staging:

```text
GET /storage/assignments/<legacy-file>
```

Jika response `200`, file legacy masih public.

### Rekomendasi

1. Inventarisasi path database.
2. Salin file legacy ke `storage/app/private`.
3. Validasi checksum/jumlah file.
4. Update metadata path bila diperlukan.
5. Verifikasi download route.
6. Hapus public copy hanya setelah backup dan approval.
7. Tambahkan deny rule untuk private legacy prefixes jika migration bertahap diperlukan.

---

## Finding H-02 — Dependency vulnerabilities belum diperbaiki

**Severity:** Critical/High  
**Status:** Open  
**Bukti:** `composer audit --format=plain` menemukan 40 advisory pada 13 package.

### Package penting yang terdampak

- `dedoc/scramble` `v0.13.14`
- `guzzlehttp/guzzle`
- `guzzlehttp/psr7`
- `laravel/framework` `v12.52.0`
- `league/commonmark`
- `phpoffice/phpspreadsheet` `5.4.0`
- `phpseclib/phpseclib`
- `symfony/http-foundation`
- `symfony/http-kernel`
- `symfony/mailer`
- `symfony/mime`
- `symfony/polyfill-intl-idn`
- `symfony/routing`

### Advisories paling berisiko

- Scramble remote code execution via evaluation of user-controlled validation rules.
- PhpSpreadsheet SSRF/RCE ketika filename dikontrol user.
- PhpSpreadsheet SSRF bypass melalui redirect.
- PhpSpreadsheet memory exhaustion/CPU denial of service pada beberapa reader.
- Laravel temporary signed URL path confusion.
- Laravel email rule CRLF injection.
- Symfony mailer/mime email and SMTP injection.
- Symfony HTTP kernel HEAD method filter bypass pada versi affected.
- Symfony HTTP foundation SSRF private-network check bypass.
- Guzzle host/cookie/proxy/redirect handling vulnerabilities.
- phpseclib SSRF/DoS/cryptographic issues.

### Tindakan wajib

1. Backup `composer.lock` dan buat branch remediation.
2. Jalankan `composer update --with-all-dependencies` secara terkontrol.
3. Prioritaskan package dengan critical/high advisory.
4. Test import spreadsheet dengan file valid, malformed, oversized, dan hostile.
5. Test semua API documentation route Scramble.
6. Jalankan `composer audit` kembali.
7. Deploy hanya setelah regression test dan staging verification.

**Dependency update belum dilakukan oleh pekerjaan ini.**

---

## Finding M-01 — CSP masih report-only dan unsafe-inline masih aktif

**Severity:** Medium  
**Status:** Partially mitigated  

CSP sudah ditambahkan, tetapi belum enforcement penuh dan masih mengizinkan inline script/style.

### Dampak

CSP saat ini membantu observasi tetapi belum cukup kuat untuk mencegah seluruh dampak XSS.

### Rekomendasi

- Gunakan nonce/hash.
- Kurangi inline JavaScript.
- Tambahkan report collector.
- Hapus `unsafe-inline`.
- Uji halaman admin/dosen/mahasiswa sebelum enforce.

---

## Finding M-02 — Private path masih muncul pada sebagian response API

**Severity:** Medium  
**Status:** Partially mitigated  

Sebagian response sudah mengganti `asset('storage/...')` menjadi route download, tetapi beberapa response masih mengirim field seperti:

```text
lampiran_path
materi_file
file_path
proof_file
```

### Dampak

- Internal storage layout terekspos.
- Client dapat mencoba membangun direct URL.
- Memperbesar risiko jika public symlink/legacy file masih tersedia.

### Rekomendasi

Return hanya:

```text
has_attachment
original_filename
attachment_url
```

Jangan mengirim internal path untuk private object.

---

## Finding M-03 — Test feature belum executable penuh pada environment saat ini

**Severity:** Medium  
**Status:** Open test infrastructure issue  

`SecurityFixesTest` gagal sebelum assertion berjalan karena migration `news` menjalankan:

```sql
ALTER TABLE news MODIFY kategori ENUM(...)
```

SQLite tidak mendukung sintaks tersebut.

### Dampak

- Role matrix belum memiliki bukti end-to-end.
- Download ownership belum memiliki bukti integration test.
- Policy registration test tidak dapat dijalankan dalam feature suite.

### Rekomendasi

Gunakan salah satu pendekatan:

1. Jalankan feature test dengan MySQL-compatible test database.
2. Tambahkan guard `DB::getDriverName()` pada migration yang memang MySQL-specific.
3. Gunakan migration schema builder yang portable.
4. Pisahkan test unit yang tidak memerlukan `RefreshDatabase`.
5. Jangan melaporkan feature security test sebagai passing sebelum benar-benar mencapai assertions.

---

## Finding L-01 — Pint menemukan 8 style issues

**Severity:** Low / maintainability  
**Status:** Open  

Pint gagal pada 8 file/kelompok style, termasuk:

```text
AdminContentApiController.php
AdminController.php
BootcampSesiController.php
DosenContentApiController.php
DosenController.php
CourseController.php
EnsureUserRole.php
SecurityHeaders.php
```

Style issue tidak selalu vulnerability, tetapi formatter failure membuat quality gate tidak hijau dan menyulitkan review patch.

---

## Finding L-02 — Tidak ditemukan signature sederhana pada source directories yang diperiksa; ini bukan hasil forensic-clean

**Severity:** Informational / incomplete assurance  
**Status:** Scope-limited  

Pencarian pada `app`, `routes`, `config`, `bootstrap`, `database`, dan `resources` tidak menemukan signature sederhana berikut:

```text
eval(
base64_decode(
shell_exec(
exec(
system(
passthru(
proc_open(
```

### Batasan

Hasil ini bukan bukti tidak ada malware atau hasil forensic-clean karena:

- Scan signature ini hanya mencakup `app`, `routes`, `config`, `bootstrap`, `database`, dan `resources`.
- `vendor/`, `public/`, `storage/`, `bootstrap/cache/`, dan seluruh filesystem server tidak termasuk scope command tersebut.
- Obfuscation dapat menggunakan variasi lain.
- Backdoor dapat berada di server tetapi tidak di working tree.
- Timestamp file dan cron belum diaudit.
- Database payload/backdoor belum diperiksa.
- Web server/PHP-FPM belum diperiksa.
- Production artifacts, logs, user accounts, dan scheduled jobs belum diperiksa.

### Tindakan lanjutan

- Bandingkan repository dengan known-good commit.
- Scan seluruh tracked PHP termasuk vendor dengan tool yang tepat.
- Periksa `storage`, `public`, `bootstrap/cache`, upload directory.
- Periksa cron/systemd/task scheduler.
- Periksa user server dan SSH keys.
- Periksa access log, auth log, PHP-FPM log, queue log.
- Rotate secrets jika ada indikasi compromise.

---

## 8. File dan Konfigurasi yang Belum Diverifikasi Penuh

Audit source telah mencakup konfigurasi aplikasi tertentu, tetapi berikut belum dapat dianggap selesai tanpa akses runtime/server:

```text
.env exposure pada web server aktual
APP_DEBUG value pada production
APP_KEY rotation/compromise history
Nginx/Apache virtual host
PHP-FPM pool configuration
Directory listing
File permissions dan ownership
Public backup files (.sql/.zip/.tar)
.git exposure dari HTTP
storage symlink pada server aktual
Cron jobs
Systemd timers
OS users dan SSH keys
TLS certificate/configuration
WAF/reverse proxy rules
Production CORS origin
Production Sanctum stateful domains
```

### Validasi manual yang disarankan

```text
GET /.env
GET /.git/HEAD
GET /composer.json
GET /composer.lock
GET /database/*.sql
GET /storage/<legacy-private-file>
GET /bootstrap/cache/*.php
```

Semua request di atas harus diuji secara legal pada staging/production milik sendiri dengan hati-hati, tanpa brute force atau destructive payload.

---

## 9. Migration Plan untuk Private Files

Tidak ada file yang dihapus atau dipindahkan massal oleh pekerjaan ini.

## Phase 1 — Inventory

1. Ambil semua path dari kolom:
   - `assignment_submissions.file_path`
   - `bootcamp_live_class_attendances.proof_file`
   - `bootcamp_sessions.materi_file`
   - `course_materials.lampiran_path`
2. Normalisasi slash dan prefix.
3. Tandai disk asal berdasarkan prefix dan keberadaan file.
4. Catat ukuran, checksum, MIME, owner, dan timestamp.
5. Buat laporan file missing/duplicate.

## Phase 2 — Copy private files

1. Copy public legacy file ke `storage/app/private`.
2. Pertahankan relative path jika kompatibel.
3. Gunakan copy, bukan move, pada fase pertama.
4. Verifikasi checksum source/destination.
5. Catat hasil migrasi per record.

## Phase 3 — Update database metadata

Jika path tetap sama dan hanya disk yang berubah, database mungkin tidak perlu berubah. Jika path berubah, update metadata dalam transaction dan simpan mapping:

```text
old_disk
old_path
new_disk
new_path
checksum
migrated_at
```

## Phase 4 — Switch read path

1. Gunakan local disk sebagai authoritative.
2. Pertahankan fallback hanya selama periode transisi.
3. Monitor fallback usage.
4. Pastikan semua UI menggunakan route terotorisasi.

## Phase 5 — Remove public legacy copies

Hanya setelah:

- Semua record termigrasi.
- Semua download route tested.
- Tidak ada fallback hit selama periode monitoring.
- Backup tervalidasi.
- Approval stakeholder diperoleh.

Baru kemudian hapus public copy secara terkontrol.

---

## 10. Authorization Test Matrix

| Actor | Resource | Action | Expected |
|---|---|---|---:|
| Anonymous | API mahasiswa | GET profile | 401 |
| Mahasiswa | API dosen | GET profile | 403 |
| Mahasiswa | API dosen | POST course | 403 |
| Mahasiswa | API admin | DELETE/admin action | 403 |
| Dosen | API admin | GET profile | 403 |
| Admin | API dosen | GET profile | 403 atau sesuai policy eksplisit |
| Mahasiswa A | Submission B | Download | 403/404 |
| Mahasiswa A | Submission A | Download | 200 |
| Dosen A | Submission course Dosen B | Preview/download | 403/404 |
| Dosen A | Submission course Dosen A | Preview/download | 200 |
| Admin | Attendance proof | Download | 200 |
| Mahasiswa A | Attendance proof B | Download | 403/404 |
| Mahasiswa enrolled | Course attachment | Download | 200 |
| Mahasiswa non-enrolled | Course attachment | Download | 403/404 |
| Any authenticated user | `../.env` path | Resolve/download | 404 |
| Any authenticated user | `/storage/private-file` direct | HTTP | 404 setelah migration/deny rule |

---

## 11. Testing yang Sudah Dijalankan

## 11.1 PHP syntax check

Command:

```bash
php -l app/Services/PrivateFileService.php
php -l app/Http/Middleware/EnsureUserRole.php
php -l app/Http/Middleware/SecurityHeaders.php
php -l app/Policies/CoursePolicy.php
php -l app/Policies/AssignmentSubmissionPolicy.php
php -l app/Policies/BootcampLiveClassAttendancePolicy.php
php -l app/Http/Controllers/Mahasiswa/CourseController.php
php -l app/Http/Controllers/Auth/DosenController.php
php -l app/Http/Controllers/Auth/AdminController.php
php -l app/Http/Controllers/Auth/Admin/BootcampSesiController.php
php -l app/Http/Controllers/Auth/AdminContentApiController.php
php -l app/Http/Controllers/Auth/DosenContentApiController.php
php -l app/Http/Controllers/Api/Auth/MahasiswaAuthController.php
php -l app/Http/Controllers/Api/Dosen/DosenMessageController.php
php -l bootstrap/app.php
```

Hasil:

```text
Passed — no syntax errors detected.
```

## 11.2 PrivateFileService unit test

Command:

```bash
php artisan test tests/Unit/PrivateFileServiceTest.php
```

Hasil:

```text
Exit code 0
6 tests completed
8 assertions
6 deprecation notices
```

Test selesai dengan exit code 0, tetapi output PHPUnit menandai keenam test sebagai deprecated karena deprecation notice PHP 8.5. Ini bukan failure storage, namun bukan pula hasil yang sepenuhnya bersih.

Terdapat deprecation notice dari PHP 8.5 terkait:

```text
PDO::MYSQL_ATTR_SSL_CA
```

Ini bukan failure pada test storage, tetapi tetap perlu ditangani pada compatibility/dependency maintenance.

Test mencakup:

- Local disk diprioritaskan.
- Legacy public fallback hanya untuk path yang dikenal.
- Unknown public path tidak diterima.
- Path traversal ditolak.
- Local disk tidak direct-served.
- Absolute path ditolak.

## 11.3 Feature security test

Command:

```bash
php artisan test tests/Feature/Api/SecurityFixesTest.php
```

Hasil:

```text
15 failed, 0 assertions
```

Semua test berhenti pada tahap migration sebelum assertion karena migration `news` menjalankan SQL MySQL-specific pada SQLite:

```sql
ALTER TABLE news MODIFY kategori ENUM(...)
```

Jadi hasil ini harus dibaca sebagai **test infrastructure blocker**, bukan sebagai bukti bahwa setiap security assertion gagal secara fungsional.

## 11.4 Pint

Command:

```bash
vendor/bin/pint --test <changed-files>
```

Hasil:

```text
14 files checked
8 style issues found
```

Pint belum hijau.

## 11.5 Dependency audit

Command:

```bash
composer audit --format=plain
```

Hasil:

```text
40 security vulnerability advisories affecting 13 packages
```

Command exit code non-zero karena advisory ditemukan.

## 11.6 Diff whitespace check

Command:

```bash
git diff --check
```

Tidak ditemukan whitespace error fatal pada diff. Git hanya menampilkan warning line ending CRLF/LF pada beberapa Blade file.

---

## 12. Contoh Perubahan Sebelum dan Sesudah

## 12.1 Role API

### Sebelum

```php
Route::middleware('auth:sanctum')
    ->prefix('dosen')
    ->group(function () {
        // endpoint dosen
    });
```

### Sesudah

```php
Route::middleware(['auth:sanctum', 'role:dosen'])
    ->prefix('dosen')
    ->group(function () {
        // endpoint dosen
    });
```

## 12.2 Public private submission

### Sebelum

```php
$fileName = "assignment_{$courseId}_{$assignmentId}_{$userId}_" . time() . '.' . $file->getClientOriginalExtension();
$filePath = $file->storeAs('assignments', $fileName, 'public');
```

### Sesudah

```php
$filePath = $file->store('assignments', 'local');
```

## 12.3 Direct URL private file

### Sebelum

```blade
<a href="{{ asset('storage/' . $submission->file_path) }}">
```

### Sesudah

```blade
<a href="{{ route('mahasiswa.assignment-download', [
    'courseId' => $course->id_course,
    'assignmentId' => $material->id_material,
]) }}">
```

## 12.4 File download

### Sebelum

```php
return response()->download(
    Storage::disk('public')->path($submission->file_path),
    $downloadName
);
```

### Sesudah

```php
Gate::forUser($user)->authorize('view', $submission);

return app(PrivateFileService::class)->download(
    $submission->file_path,
    $downloadName,
);
```

## 12.5 Stored XSS sink

### Sebelum

```html
<div x-html="selectedNews?.konten"></div>
```

### Sesudah

```html
<div x-text="selectedNews?.konten"></div>
```

## 12.6 Internal OAuth metadata

### Sebelum

```php
return [
    'google_id' => $this->google_id,
    'provider' => $this->provider,
];
```

### Sesudah

Field internal tersebut tidak lagi dikirim pada resource dosen/admin yang diperiksa.

---

## 13. Hal yang Sengaja Tidak Dilakukan

Untuk menjaga backward compatibility dan mencegah perubahan berisiko, pekerjaan ini tidak melakukan:

- Tidak mengubah password default NIM.
- Tidak menghapus user.
- Tidak menghapus file lama.
- Tidak memindahkan seluruh public storage.
- Tidak melakukan bulk migration file secara otomatis.
- Tidak menjalankan exploit destructive.
- Tidak melakukan brute force.
- Tidak mengubah data production.
- Tidak melakukan `git commit`.
- Tidak melakukan `git push`.
- Tidak melakukan dependency update otomatis.
- Tidak mengaktifkan CSP enforcement penuh.
- Tidak menyatakan server production sudah aman hanya berdasarkan source review.

---

## 14. Production Hardening Checklist

### Authorization

- [x] Role middleware dibuat.
- [x] Alias middleware didaftarkan.
- [x] Route API utama diberi role boundary (implementasi source selesai; integration test belum mencapai assertion).
- [x] Web guards memeriksa role.
- [x] Course policy dibuat.
- [x] Assignment submission policy dibuat.
- [x] Attendance policy dibuat.
- [ ] Role boundary diverifikasi melalui full feature test.
- [ ] Semua endpoint admin/dosen/mahasiswa diaudit satu per satu.
- [ ] Semua resource ownership test berjalan pada MySQL-compatible database.
- [ ] Authorization untuk API response path ditutup seluruhnya.

### Storage

- [x] Disk local diarahkan ke `storage/app/private`.
- [x] Direct serving local disk dinonaktifkan.
- [x] Private file service dibuat.
- [x] Path traversal ditolak.
- [x] Download route memakai authorization (source implementation selesai; ownership integration test belum terverifikasi karena migration blocker).
- [x] Upload baru untuk beberapa private category diarahkan ke local disk.
- [x] Public fallback dibatasi ke known legacy prefixes.
- [ ] Ownership download test selesai dijalankan pada compatible database.
- [ ] Legacy private files selesai dimigrasi.
- [ ] Direct public access ke legacy private file ditutup.
- [ ] Backup dan checksum migration selesai.
- [ ] Production HTTP test `/storage/private-file` menghasilkan 404.

### Upload

- [x] Beberapa upload memakai random Laravel filename.
- [x] Beberapa handler memakai MIME/size allowlist.
- [ ] Semua upload handler diaudit konsisten.
- [ ] Executable extensions ditolak pada semua handler.
- [ ] Image re-encoding diterapkan untuk asset public berisiko.
- [ ] Web server dipastikan tidak mengeksekusi script di upload directory.
- [ ] Filename user hanya disimpan sebagai metadata display.

### XSS

- [x] Target `x-html` pada news diubah menjadi `x-text`.
- [x] Preview bacaan admin/dosen diubah menjadi `x-text`.
- [x] Sebagian output memakai `e()` sebelum raw `nl2br`.
- [ ] Semua `innerHTML` dengan data API diaudit.
- [ ] Rich text sanitizer dipasang jika HTML memang diperlukan.
- [ ] CSP dipindah dari report-only menjadi enforcement.
- [ ] `unsafe-inline` dihapus secara bertahap.

### API

- [x] Role boundary API ditambahkan.
- [x] OAuth/provider metadata dihapus dari resource yang diperiksa.
- [x] Sanctum default expiration diatur 24 jam.
- [ ] Flow revocation token login sudah integration-verified (implementasi/test ada, tetapi feature suite terblokir sebelum assertion).
- [ ] Production `SANCTUM_TOKEN_EXPIRATION` diverifikasi.
- [ ] Logout/token revocation semua role diverifikasi.
- [ ] CORS production origin diverifikasi.
- [ ] Semua API Resource diaudit untuk secret/path leakage.
- [ ] Rate limiting endpoint login dan sensitive endpoint diverifikasi.

### Dependency

- [ ] `composer audit` menjadi clean atau exception terdokumentasi.
- [ ] Scramble di-update ke versi patched.
- [ ] PhpSpreadsheet di-update ke versi patched.
- [ ] Laravel/Symfony/Guzzle/phpseclib di-update.
- [ ] Import spreadsheet diuji dengan malformed/hostile files.
- [ ] Lockfile diperbarui melalui review terkontrol.

### Server

- [ ] `APP_DEBUG=false` diverifikasi di production.
- [ ] `.env` tidak dapat diakses dari web.
- [ ] `.git/` tidak dapat diakses.
- [ ] Directory listing mati.
- [ ] Backup `.sql`, `.zip`, `.tar` tidak public.
- [ ] Nginx/Apache hanya serve `public/`.
- [ ] PHP upload directory tidak executable.
- [ ] File ownership/permission diverifikasi.
- [ ] PHP-FPM hardening diverifikasi.
- [ ] HTTPS/TLS diverifikasi.
- [ ] `TRUSTED_PROXIES` tidak wildcard pada production tanpa alasan.
- [ ] Cron/systemd/SSH users diaudit.

### Monitoring

- [ ] Login failure alert.
- [ ] Suspicious file access alert.
- [ ] Repeated 403/404 download alert.
- [ ] Upload anomaly alert.
- [ ] Composer/dependency monitoring.
- [ ] Integrity monitoring pada `app/`, `public/`, `bootstrap/`, `routes/`.
- [ ] Centralized immutable logs.
- [ ] Incident response runbook.

---

## 15. Prioritized Remediation Roadmap

## P0 — Wajib sebelum production

1. Update dependency critical/high dan ulangi `composer audit`.
2. Migrasikan private legacy files dari public storage.
3. Tutup direct URL public untuk seluruh private legacy prefix.
4. Jalankan authorization/download tests pada MySQL-compatible database.
5. Audit semua private path yang masih muncul pada API response.
6. Verifikasi `APP_DEBUG=false`, `.env` protection, web root, dan upload execution.
7. Audit `TRUSTED_PROXIES` production.
8. Rotate `APP_KEY`, database credentials, OAuth secrets, dan API keys jika ada indikasi compromise sebelumnya.

## P1 — Hardening lanjutan

1. Selesaikan CSP enforcement.
2. Audit seluruh `innerHTML` dan raw Blade HTML.
3. Standardisasi upload validation helper/Form Request.
4. Tambahkan MFA admin/dosen.
5. Tambahkan login throttling dan anomaly detection.
6. Tambahkan storage access audit log.
7. Tambahkan integration test untuk direct `/storage` access.
8. Pisahkan migration MySQL-specific dari test portability issue.
9. Pisahkan diff authorization/storage/migration agar review lebih aman.

## P2 — Monitoring dan resilience

1. File integrity monitoring.
2. Dependency scanning CI.
3. Secret scanning CI.
4. Scheduled `composer audit`.
5. Alerting untuk repeated unauthorized download attempts.
6. Backup encryption dan restore drill.
7. Incident response playbook.
8. Forensic baseline repository dan production artifact.

---

## 16. Kesimpulan Akhir

Hardening utama sudah dibuat pada source code untuk role boundary, ownership authorization, private storage baru, authorized download, path traversal defense, API metadata exposure, dan beberapa stored XSS sink.

Validasi paling kuat yang berhasil:

- PHP syntax seluruh file target: **pass**.
- `PrivateFileServiceTest`: **6 test pass / 8 assertions**.
- Path traversal dan absolute path defense: **teruji pada unit test**.
- Local disk direct serving: **teruji pada unit test**.

Namun pekerjaan belum dapat dinyatakan selesai secara production-ready karena:

- Dependency audit gagal dengan 40 advisory.
- Full feature test terblokir migration SQLite/MySQL incompatibility.
- Pint belum hijau.
- Legacy private files belum dimigrasi dan masih berpotensi public.
- CSP belum enforcement.
- Server-level forensic dan deployment hardening belum diverifikasi.
- Sebagian internal private path masih berpotensi muncul pada response.

**Keputusan keamanan yang disarankan:** jangan commit/deploy seluruh working tree sebagai satu bundle sebelum dependency, legacy storage exposure, test database, dan review diff diselesaikan. Password default NIM tetap tidak berubah sesuai batasan yang diberikan.
