# DigiKampus UT — Audit Koneksi Frontend ↔ Backend ↔ Database

**Tanggal:** 2026-02-14 | **Versi:** 4.0 (Final Deep Audit)

---

## Status Overview

| Role          | Status        | Routes | Coming Soon | Issues |
| :------------ | :------------ | :----: | :---------: | :----: |
| **Admin**     | 🟢 100% Ready |   17   |      0      |   0    |
| **Dosen**     | 🟢 100% Ready |   26   |      0      |   0    |
| **Mahasiswa** | � ~93% Ready  |   28   |      4      |   0    |

---

## 🟢 ADMIN — 100% Connected

Semua halaman (Dashboard, Kelola Dosen, Kelola Mahasiswa, Kelola Kursus) terhubung ke `AdminController` dengan full CRUD.
**Model:** `User`, `Course`, `Enrollment`, `Profile`, `Jurusan`.

## 🟢 DOSEN — 100% Connected

Semua halaman terhubung:

- **Dashboard** → `DosenController::showDashboard()` → DB: courses, enrollments
- **Kursus Saya** → `DosenController::showKursusSaya()` → DB: courses, enrollments
- **Kelola Modul/Video/Quiz/Tugas/Bacaan** → Server-side + API → DB: course_modules, course_materials
- **Pesan/Chat** → API: `/api/dosen/messages` → DB: messages, users
- **Progres Kursus/Mahasiswa** → Server-side → DB: enrollments, users
- **Header:** Messages → `dosen.pesan` ✅ | Profile/Settings → "Segera Hadir" ✅

## � MAHASISWA — ~93% Connected

### ✅ Halaman yang Terhubung (28 routes)

| Halaman                              | Controller                                  |  Status  |
| :----------------------------------- | :------------------------------------------ | :------: |
| Dashboard                            | `DashboardController::index`                |    ✅    |
| Get Courses                          | `CourseController::index` (filter enrolled) |    ✅    |
| Kursus Saya                          | `CourseController::myCourses`               |    ✅    |
| Course Detail                        | `CourseController::show`                    |    ✅    |
| Course Learn                         | `CourseController::learn`                   |    ✅    |
| Quiz (take/answer/flag/reset/result) | `CourseController`                          |    ✅    |
| Assignment (detail/submit/status)    | `CourseController`                          |    ✅    |
| Module Feedback                      | `CourseController::moduleFeedback`          |    ✅    |
| Favorites                            | `CourseController::favorites`               |    ✅    |
| Calendar                             | `DashboardController::calendar`             |    ✅    |
| **Notification**                     | `DashboardController::notification`         | ✅ Fixed |
| Profile (view/edit/update)           | `ProfileController`                         |    ✅    |
| Checkout                             | `CheckoutController::index`                 | ✅ Fixed |
| Payment                              | `CheckoutController::payment`               |    ✅    |
| Payment Success                      | `CheckoutController::success`               |    ✅    |
| Finance                              | `CheckoutController::finance`               |    ✅    |
| Transaction Detail                   | `CheckoutController::transactionDetail`     |    ✅    |
| News                                 | `Route::view` + API fetch                   |    ✅    |

### ✅ Fix yang Sudah Diterapkan

1. **Notification Page** — Sebelumnya pakai data dummy hardcoded, sekarang sudah terhubung ke `Notification` model via `DashboardController`. Mendukung filter, search, mark-as-read (single + all).

2. **Checkout Page (Empty Cart)** — Sebelumnya keranjang kosong masih menampilkan metode pembayaran, biaya layanan Rp 5.000, dan tombol "Bayar Sekarang". Sekarang semua disembunyikan saat keranjang kosong, dan biaya layanan = Rp 0.

3. **Get Courses (Filter Enrolled)** — Kursus yang sudah di-enroll/dibeli otomatis tidak muncul di katalog (`whereNotIn` di `CourseController::index`).

### � Coming-Soon (Tidak Ada Backend)

| Sidebar Item   | Route                      | Backend |
| :------------- | :------------------------- | :-----: |
| Forum          | `mahasiswa.forum`          |   ❌    |
| Chat           | `mahasiswa.chat`           |   ❌    |
| Apps           | `mahasiswa.apps`           |   ❌    |
| Learning Goals | `mahasiswa.learning-goals` |   ❌    |

Keempat fitur ini tidak memiliki controller, model, maupun migrasi database. Tetap ditampilkan sebagai "Coming Soon".
