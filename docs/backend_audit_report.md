# Backend Connection Audit Report

Generated: 2026-01-31

## Summary

Complete list of all pages, buttons, forms, and features that are NOT connected to backend or have missing backend.

---

## 🔴 DOSEN DASHBOARD

### Halaman dengan Form/Action yang Belum Terhubung Backend

#### 1. `/dosen/konten/video` - Kelola Video

- **File:** `kelola-video.blade.php`
- **Status:** ❌ Form action="#" - tidak ada controller
- **Yang belum berfungsi:**
  - Upload video file
  - Simpan draft video
  - Publikasikan video
  - Upload thumbnail
  - Ambil dari frame video

#### 2. `/dosen/konten/quiz` - Kelola Quiz

- **File:** `kelola-quiz.blade.php`
- **Status:** ❌ Frontend-only (demo toast)
- **Yang belum berfungsi:**
  - Tambah/edit/hapus soal (hanya localStorage)
  - Simpan draft kuis
  - Publikasikan kuis

#### 3. `/dosen/konten/bacaan` - Kelola Bacaan

- **File:** `kelola-bacaan.blade.php`
- **Status:** ❌ Frontend-only (demo toast)
- **Yang belum berfungsi:**
  - Simpan konten bacaan
  - Upload lampiran
  - Simpan draft
  - Publikasikan bacaan

#### 4. `/dosen/konten/tugas` - Kelola Tugas

- **File:** `kelola-tugas.blade.php`
- **Status:** ❌ Frontend-only (demo toast)
- **Yang belum berfungsi:**
  - Simpan tugas
  - Simpan rubrik penilaian
  - Simpan draft
  - Publikasikan tugas

#### 5. `/dosen/pesan` - Pesan/Chat

- **File:** `pesan.blade.php`
- **Status:** ❌ Static UI - data hardcoded
- **Yang belum berfungsi:**
  - Daftar percakapan (hardcoded)
  - Riwayat chat (hardcoded)
  - Kirim pesan baru
  - Search pesan
  - Attachment file
  - Emoji picker

---

## 🟡 MAHASISWA DASHBOARD

### Halaman Coming Soon (Belum Ada)

| Route                       | Page           | Status         |
| --------------------------- | -------------- | -------------- |
| `/mahasiswa/forum`          | Forum Diskusi  | ⏳ Coming Soon |
| `/mahasiswa/chat`           | Chat           | ⏳ Coming Soon |
| `/mahasiswa/apps`           | Apps           | ⏳ Coming Soon |
| `/mahasiswa/learning-goals` | Learning Goals | ⏳ Coming Soon |
| `/mahasiswa/news`           | News           | ⏳ Coming Soon |

### Halaman dengan Fitur Partial/Belum Lengkap

#### 1. Dashboard (`dashboard.blade.php`)

- **Yang belum berfungsi:**
  - Tombol "Lanjutkan Belajar" di header (tidak ada action)
  - Tombol "Tandai Semua Telah Dibaca" di notifikasi (frontend only)
  - Filter notifikasi (frontend only, tidak persist ke DB)
  - Tombol "Lihat Semua" berita (hardcoded data)

#### 2. Notification (`notification.blade.php`)

- **Status:** ❌ Data notifikasi hardcoded
- **Yang belum berfungsi:**
  - Fetch notifikasi dari database
  - Mark as read
  - Delete notification
  - Notification preferences

#### 3. Calendar (`calendar.blade.php`)

- **Status:** ⚠️ Partial - Agenda diambil dari DB tapi belum bisa add/edit
- **Yang belum berfungsi:**
  - Tambah agenda baru
  - Edit agenda
  - Delete agenda

#### 4. Finance (`finance.blade.php`)

- **Status:** ✅ Terhubung backend (CheckoutController)

#### 5. Course Pages

- **Status:** ✅ Sebagian besar terhubung backend
- **Yang belum berfungsi:**
  - Ragu-flag di quiz tidak persist ke DB (session only)

---

## 🟢 ADMIN DASHBOARD

### Semua CRUD Sudah Terhubung ✅

- Kelola Dosen: CRUD lengkap
- Kelola Mahasiswa: CRUD lengkap
- Kelola Kursus: CRUD lengkap

### Yang Belum Ada

- Kelola Notifikasi/Pengumuman
- Kelola Berita (News)
- Laporan/Analytics export
- System Settings

---

## 📝 KOMPONEN NAVBAR/SIDEBAR

### Dosen Sidebar

| Item              | Route                      | Status     |
| ----------------- | -------------------------- | ---------- |
| Dashboard         | `/dosen/dashboard`         | ✅         |
| Kursus Saya       | `/dosen/kursus`            | ✅         |
| Buat Kursus Baru  | `/dosen/kursus/buat`       | ✅         |
| Progres Mahasiswa | `/dosen/progres-mahasiswa` | ✅         |
| Pesan             | `/dosen/pesan`             | ⚠️ UI only |

### Dosen Header

- Tombol Notifikasi: ❌ Tidak berfungsi (no route)
- Dark Mode Toggle: ✅ Berfungsi (localStorage)
- Dropdown Profile: ✅ Logout berfungsi

### Mahasiswa Sidebar

| Item           | Route                       | Status         |
| -------------- | --------------------------- | -------------- |
| Home           | `/mahasiswa/dashboard`      | ✅             |
| Get Courses    | `/mahasiswa/get-courses`    | ✅             |
| Courses        | `/mahasiswa/courses`        | ✅             |
| Favorites      | `/mahasiswa/favorites`      | ✅             |
| Forum          | `/mahasiswa/forum`          | ⏳ Coming Soon |
| Chat           | `/mahasiswa/chat`           | ⏳ Coming Soon |
| Apps           | `/mahasiswa/apps`           | ⏳ Coming Soon |
| Calendar       | `/mahasiswa/calendar`       | ⚠️ Partial     |
| Finance        | `/mahasiswa/finance`        | ✅             |
| Learning Goals | `/mahasiswa/learning-goals` | ⏳ Coming Soon |
| News           | `/mahasiswa/news`           | ⏳ Coming Soon |

### Mahasiswa Header

- Tombol Notifikasi: ⚠️ Mengarah ke notifikasi (data hardcoded)
- Dark Mode Toggle: ✅ Berfungsi
- Profile Dropdown: ✅ Semua berfungsi

### Admin Sidebar

| Item             | Route              | Status |
| ---------------- | ------------------ | ------ |
| Dashboard        | `/admin/dashboard` | ✅     |
| Kelola Dosen     | `/admin/dosen`     | ✅     |
| Kelola Mahasiswa | `/admin/mahasiswa` | ✅     |
| Kelola Kursus    | `/admin/kursus`    | ✅     |

---

## 📊 RINGKASAN

| Role      | Total Fitur | Terhubung | Belum | Persentase |
| --------- | ----------- | --------- | ----- | ---------- |
| Admin     | 12          | 12        | 0     | 100% ✅    |
| Mahasiswa | 25          | 15        | 10    | 60%        |
| Dosen     | 20          | 10        | 10    | 50%        |

---

## 🛠️ PRIORITAS PENGEMBANGAN

### High Priority (Core Features)

1. Backend untuk kelola-video (Dosen)
2. Backend untuk kelola-quiz (Dosen)
3. Backend untuk kelola-tugas (Dosen)
4. Backend untuk kelola-bacaan (Dosen)
5. Real-time chat/pesan (Dosen-Mahasiswa)

### Medium Priority

6. Notifikasi system (database-driven)
7. News/Berita management
8. Calendar CRUD (add/edit/delete agenda)

### Low Priority (Can Stay Coming Soon)

9. Forum diskusi
10. Apps hub
11. Learning Goals tracker
