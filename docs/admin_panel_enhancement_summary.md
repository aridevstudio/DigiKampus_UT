# DigiKampus UT — Admin Panel Enhancement Summary

## Overview

10 admin panel tasks implemented end-to-end: backend logic, Blade views, routes, migrations, services, and PHPUnit tests.

---

## Cross-Check Matrix

| # | Task | Controller | Routes | Blade Views | Migration | Model | Service | Tests |
|---|------|-----------|--------|-------------|-----------|-------|---------|-------|
| 1 | YouTube Playlist Sync | `syncYoutubePlaylist()`, `getYoutubeVideos()` | `admin.kursus.syncPlaylist`, `admin.kursus.youtubeVideos` | `kursus.blade.php` | `create_youtube_playlist_videos_table` | `YoutubePlaylistVideo` | `YoutubePlaylistService` | `AdminYoutubeTest` (7) |
| 2 | Admin Course Menu | `showKursus()` (existing) | `admin.kursus` (existing) | Dashboard quick actions → `admin.kursus` | — | — | — | `AdminDashboardTest` (5) |
| 3 | Student Validation | `storeMahasiswa()`, `storeDosen()`, `updateMahasiswa()`, `updateDosen()` | existing | — | — | — | — | `AdminValidationTest` (5) |
| 4 | Excel Import with Preview | `previewImport()`, `confirmImport()` | `admin.import.preview`, `admin.import.confirm` | `mahasiswa.blade.php`, `dosen.blade.php` | `create_import_logs_table` | `ImportLog` | `ExcelImportService` | `AdminImportTest` (14) |
| 5 | Excel Template Download | `downloadTemplate()` | `admin.import.template` | Template button in mahasiswa/dosen | — | — | `ExcelImportService::generateTemplate()` | `AdminImportTest` (3) |
| 6 | Photo Upload Limits | All store/update validators | — | profile, mahasiswa, dosen accept attrs | — | — | — | `AdminValidationTest` (3) |
| 7 | Persistent Notifications | `getNotifications()`, `getNotificationCount()`, `markNotificationRead()`, `markAllNotificationsRead()` | `admin.notifications.*` (4 routes) | `admin.blade.php` layout | `create_admin_notifications_table` | `AdminNotification` | — | `AdminNotificationTest` (6) |
| 8 | Fix Settings Link | — | — | `header.blade.php` | — | — | — | — |
| 9 | Edit Profile (photo) | `updateProfile()` | existing | `profile.blade.php` | — | — | — | — |
| 10 | Dashboard Counting Fix | `showDashboard()` | existing | `dashboard.blade.php` | — | — | — | `AdminDashboardTest` (3) |

---

## Files Created

| File | Purpose |
|------|---------|
| `app/Models/AdminNotification.php` | Persistent admin notification model with `notifyAllAdmins()` |
| `app/Models/YoutubePlaylistVideo.php` | YouTube video records per course |
| `app/Models/ImportLog.php` | Audit log for Excel imports |
| `app/Services/YoutubePlaylistService.php` | Playlist ID extraction, RSS/API video fetch, DB sync |
| `app/Services/ExcelImportService.php` | Excel parse/preview, import execution, template generation |
| `database/migrations/2026_02_22_100001_create_admin_notifications_table.php` | Notifications table |
| `database/migrations/2026_02_22_100002_create_youtube_playlist_videos_table.php` | YouTube videos table |
| `database/migrations/2026_02_22_100003_create_import_logs_table.php` | Import logs table |
| `tests/Unit/YoutubePlaylistServiceTest.php` | 8 tests — playlist URL parser |
| `tests/Feature/Admin/AdminDashboardTest.php` | 5 tests — dashboard auth, counting |
| `tests/Feature/Admin/AdminNotificationTest.php` | 6 tests — CRUD, isolation |
| `tests/Feature/Admin/AdminValidationTest.php` | 5 tests — photo, uniqueness |
| `tests/Feature/Admin/AdminYoutubeTest.php` | 7 tests — sync, video retrieval |
| `tests/Feature/Admin/AdminImportTest.php` | 14 tests — template, preview, import |

## Files Modified

| File | Changes |
|------|---------|
| `app/Http/Controllers/Auth/AdminController.php` | Added 8 methods, updated 5 validators, fixed dashboard counting |
| `routes/admin.php` | Added 8 new routes |
| `resources/views/Auth/admin/dashboard.blade.php` | "Dosen Aktif" / "Mahasiswa Aktif" labels, quick action links |
| `resources/views/components/layouts/admin.blade.php` | Notification count badge, dropdown CRUD, icon mapping, polling |
| `resources/views/Auth/admin/kursus.blade.php` | YouTube sync button, video list, sync JS |
| `resources/views/Auth/admin/mahasiswa.blade.php` | 3-step Excel import modal, webp accept |
| `resources/views/Auth/admin/dosen.blade.php` | 3-step Excel import modal, webp accept |
| `resources/views/Auth/admin/profile.blade.php` | 2MB limit, webp support |
| `resources/views/components/dashboard/header.blade.php` | Settings link → profile route |
| `composer.json` | Added `phpoffice/phpspreadsheet` |
| `phpunit.xml` | SQLite in-memory for tests |

---

## New Routes

| Method | URI | Name | Controller Method |
|--------|-----|------|------------------|
| GET | `/admin/notifications` | `admin.notifications` | `getNotifications` |
| GET | `/admin/notifications/count` | `admin.notifications.count` | `getNotificationCount` |
| POST | `/admin/notifications/{id}/read` | `admin.notifications.read` | `markNotificationRead` |
| POST | `/admin/notifications/read-all` | `admin.notifications.readAll` | `markAllNotificationsRead` |
| POST | `/admin/kursus/{id}/sync-playlist` | `admin.kursus.syncPlaylist` | `syncYoutubePlaylist` |
| GET | `/admin/kursus/{id}/youtube-videos` | `admin.kursus.youtubeVideos` | `getYoutubeVideos` |
| POST | `/admin/import/{type}/preview` | `admin.import.preview` | `previewImport` |
| POST | `/admin/import/{type}/confirm` | `admin.import.confirm` | `confirmImport` |
| GET | `/admin/import/{type}/template` | `admin.import.template` | `downloadTemplate` |

---

## Validation Rules Updated

### Photo Uploads (all store/update)
- **Before:** `max:5120`, `mimes:jpg,jpeg,png`
- **After:** `max:2048`, `mimes:jpg,jpeg,png,webp` + Indonesian error messages

### NIM/NIP Uniqueness
- `storeMahasiswa`: `unique:profiles,nim`
- `storeDosen`: `unique:profiles,nim` (NIP stored in `nim` column)

### Excel Import
- `mimes:xlsx,xls,csv`, `max:5120`
- Row-level validation: email format, required fields, phone format

---

## Manual Test Plan

### Task 1: YouTube Playlist Sync
1. Go to Admin → Kursus → Edit any course
2. Paste a YouTube playlist URL
3. Click "Sync Playlist" button
4. Verify video list appears with thumbnails
5. Click sync again → verify update counts

### Task 2: Course Menu
1. Go to Admin Dashboard
2. Click "Kelola Kursus" quick action
3. Verify it navigates to `/admin/kursus`

### Task 3: Validation
1. Try creating a mahasiswa with a duplicate NIM → error shown
2. Try creating a dosen with a duplicate email → error shown

### Task 4: Excel Import
1. Go to Admin → Mahasiswa → click "Import Excel"
2. Upload an .xlsx file → preview step shows valid/invalid rows
3. Select duplicate strategy → Confirm → result shown
4. Repeat for Dosen

### Task 5: Excel Template
1. Click "Template" button on Mahasiswa or Dosen page
2. Verify .xlsx downloads with correct headers and sample data

### Task 6: Photo Upload
1. Try uploading a 3MB photo → error "Foto maksimal 2MB"
2. Upload a .webp photo → accepted
3. Test on mahasiswa, dosen, and profile pages

### Task 7: Notifications
1. Perform an action that generates a notification (import, sync)
2. Check notification bell → count badge shows
3. Click notification → marked as read
4. Click "Tandai Semua" → all marked read

### Task 8: Settings Link
1. Click user avatar dropdown in header
2. Click "Settings" → navigates to profile page (not `#`)

### Task 9: Edit Profile
1. Go to Admin → Profile
2. Verify help text says "JPG, PNG, WebP. Maks 2MB."
3. Upload a webp photo → accepted

### Task 10: Dashboard Counts
1. Go to Admin Dashboard
2. Verify card labels: "Dosen Aktif", "Mahasiswa Aktif"
3. Deactivate a dosen → count decreases

---

## Test Results

```
Tests:    45 passed, 17 failed (pre-existing Breeze template tests)
New tests: 45/45 PASSED (92 assertions)
Duration: ~3s
```

**Note:** The 17 pre-existing failures are from Laravel Breeze scaffolding tests (`Tests\Feature\Auth\*`, `Tests\Feature\ProfileTest`) that test default `/login`, `/register` routes not used in this project. These failures existed before our changes.

---

## Definition of Done

- [x] YouTube playlist sync — backend service with RSS/API fallback, DB sync, Blade UI
- [x] Admin course menu — dashboard quick action links corrected
- [x] Student validation — NIM/NIP uniqueness, email uniqueness
- [x] Excel import with preview — 3-step modal (upload → preview → confirm), skip/update/stop strategies  
- [x] Excel template download — styled .xlsx with headers, sample data, notes
- [x] Photo upload limits — 2MB max, webp support, Indonesian error messages
- [x] Persistent notifications — DB-backed with CRUD, badge count, polling, mark-read
- [x] Settings dead link fixed — routes to profile page per guard
- [x] Edit profile updated — 2MB limit, webp accept
- [x] Dashboard counting — only counts `status = 'aktif'` users
- [x] 3 database migrations created and run
- [x] 3 Eloquent models created
- [x] 2 service classes created
- [x] 8 new admin routes added
- [x] 6 Blade views updated
- [x] 45 PHPUnit tests written and passing
- [x] phpunit.xml configured for SQLite in-memory testing
