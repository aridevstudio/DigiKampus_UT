# DigiKampus UT — Backend ↔ Frontend Consistency Audit

> **Date**: 2026-02-22  
> **Scope**: All 192 registered routes vs 72 Blade views + 65 JS fetch/form calls  
> **Out of scope**: Infrastructure, DevOps, CI/CD

---

## A) Skills Summary

Skills applied for this audit:
- **api-design-principles** — REST contract & naming conventions
- **backend-architect** — Route/controller architecture review
- **backend-dev-guidelines** — Laravel best practices
- **frontend-dev-guidelines** — Blade/JS integration patterns
- **api-security-best-practices** — Auth flow consistency
- **code-review-excellence** — Cross-layer consistency checks

---

## B) Backend Route Inventory (192 routes)

### B1. API Routes (`auth:sanctum` / public)

| # | Method | URI | Controller | Auth | Notes |
|---|--------|-----|-----------|------|-------|
| 1 | POST | `api/auth/mahasiswa/login` | MahasiswaAuthController@login | public+throttle | |
| 2 | POST | `api/auth/mahasiswa/forgot-password` | MahasiswaAuthController@forgotPassword | public+throttle | |
| 3 | POST | `api/auth/mahasiswa/verify-otp` | MahasiswaAuthController@verifyOtp | public+throttle | |
| 4 | POST | `api/auth/mahasiswa/reset-password` | MahasiswaAuthController@resetPassword | public+throttle | |
| 5 | GET | `api/mahasiswa/profile` | MahasiswaAuthController@profile | sanctum | |
| 6 | PUT | `api/mahasiswa/profile` | MahasiswaAuthController@updateProfile | sanctum | |
| 7 | PUT | `api/mahasiswa/change-password` | MahasiswaAuthController@changePassword | sanctum | |
| 8 | POST | `api/mahasiswa/logout` | MahasiswaAuthController@logout | sanctum | |
| 9 | GET | `api/mahasiswa/dashboard` | MahasiswaDashboardController@index | sanctum | |
| 10 | GET | `api/mahasiswa/dashboard/progress` | MahasiswaDashboardController@progress | sanctum | |
| 11 | GET | `api/mahasiswa/dashboard/courses` | MahasiswaDashboardController@enrolledCourses | sanctum | |
| 12 | GET | `api/mahasiswa/dashboard/news` | MahasiswaDashboardController@news | sanctum | Called from news.blade.php |
| 13 | GET | `api/mahasiswa/dashboard/agenda` | MahasiswaDashboardController@agenda | sanctum | |
| 14 | GET | `api/mahasiswa/courses` | CourseController@index | sanctum | |
| 15 | GET | `api/mahasiswa/courses/webinar` | CourseController@webinar | sanctum | |
| 16 | GET | `api/mahasiswa/courses/tiket` | CourseController@tiket | sanctum | |
| 17 | GET | `api/mahasiswa/courses/kursus` | CourseController@kursus | sanctum | |
| 18 | GET | `api/mahasiswa/courses/{id}` | CourseController@show | sanctum | |
| 19 | POST | `api/mahasiswa/courses/{id}/rate` | CourseController@rate | sanctum | |
| 20 | GET | `api/mahasiswa/my-courses` | MyCourseController@index | sanctum | |
| 21 | GET | `api/mahasiswa/my-courses/weekly-target` | MyCourseController@weeklyTarget | sanctum | |
| 22 | GET | `api/mahasiswa/my-courses/recommendations` | MyCourseController@recommendations | sanctum | |
| 23 | GET | `api/mahasiswa/my-courses/{id}` | MyCourseController@show | sanctum | |
| 24 | POST | `api/mahasiswa/my-courses/{courseId}/materials/{materialId}/complete` | MyCourseController@completeMaterial | sanctum | |
| 25 | GET | `api/mahasiswa/cart` | CartController@index | sanctum | |
| 26 | POST | `api/mahasiswa/cart/add` | CartController@add | sanctum | |
| 27 | DELETE | `api/mahasiswa/cart/{id}` | CartController@remove | sanctum | |
| 28 | DELETE | `api/mahasiswa/cart` | CartController@clear | sanctum | |
| 29 | GET | `api/mahasiswa/favorites` | FavoriteController@index | sanctum | |
| 30 | POST | `api/mahasiswa/favorites/add` | FavoriteController@add | sanctum | |
| 31 | DELETE | `api/mahasiswa/favorites/{id}` | FavoriteController@remove | sanctum | |
| 32 | POST | `api/mahasiswa/favorites/toggle/{courseId}` | FavoriteController@toggle | sanctum | |
| 33 | GET | `api/mahasiswa/notifications` | NotificationController@index | sanctum | |
| 34 | GET | `api/mahasiswa/notifications/unread-count` | NotificationController@unreadCount | sanctum | |
| 35 | POST | `api/mahasiswa/notifications/{id}/read` | NotificationController@markAsRead | sanctum | |
| 36 | POST | `api/mahasiswa/notifications/read-all` | NotificationController@markAllAsRead | sanctum | |
| 37 | GET | `api/mahasiswa/status` | StatusController@getStatus | sanctum | |
| 38 | POST | `api/mahasiswa/status/online` | StatusController@setOnline | sanctum | |
| 39 | POST | `api/mahasiswa/status/offline` | StatusController@setOffline | sanctum | |
| 40 | POST | `api/mahasiswa/status/heartbeat` | StatusController@heartbeat | sanctum | |
| 41 | POST | `api/auth/dosen/register` | DosenAuthController@register | public+throttle | |
| 42 | POST | `api/auth/dosen/login` | DosenAuthController@login | public+throttle | |
| 43 | POST | `api/auth/dosen/forgot-password` | DosenAuthController@forgotPassword | public+throttle | |
| 44 | POST | `api/auth/dosen/verify-otp` | DosenAuthController@verifyOtp | public+throttle | |
| 45 | POST | `api/auth/dosen/reset-password` | DosenAuthController@resetPassword | public+throttle | |
| 46 | GET | `api/auth/dosen/google` | DosenAuthController@redirectToGoogle | public | |
| 47 | GET | `api/auth/dosen/google/callback` | DosenAuthController@handleGoogleCallback | public | |
| 48 | GET | `api/dosen/profile` | DosenAuthController@profile | sanctum | |
| 49 | POST | `api/dosen/logout` | DosenAuthController@logout | sanctum | |
| 50 | GET | `api/dosen/dashboard` | DosenDashboardController@index | sanctum | |
| 51 | GET | `api/dosen/courses` | DosenCourseController@index | sanctum | Called from kelola-*.blade.php |
| 52 | GET | `api/dosen/courses/{id}` | DosenCourseController@show | sanctum | |
| 53 | POST | `api/dosen/courses` | DosenCourseController@store | sanctum | |
| 54 | PUT | `api/dosen/courses/{id}` | DosenCourseController@update | sanctum | |
| 55 | DELETE | `api/dosen/courses/{id}` | DosenCourseController@destroy | sanctum | |
| 56 | POST | `api/dosen/courses/{courseId}/modules` | DosenCourseController@addModule | sanctum | Called from kelola-*.blade.php |
| 57 | PUT | `api/dosen/courses/{courseId}/modules/{moduleId}` | DosenCourseController@updateModule | sanctum | |
| 58 | DELETE | `api/dosen/courses/{courseId}/modules/{moduleId}` | DosenCourseController@deleteModule | sanctum | |
| 59 | GET | `api/dosen/students/progress` | DosenStudentProgressController@index | sanctum | |
| 60 | GET | `api/dosen/students/progress/{enrollmentId}` | DosenStudentProgressController@show | sanctum | |
| 61 | GET | `api/dosen/messages` | DosenMessageController@index | sanctum | Called from pesan.blade.php |
| 62 | GET | `api/dosen/messages/unread-count` | DosenMessageController@unreadCount | sanctum | |
| 63 | GET | `api/dosen/messages/{studentId}` | DosenMessageController@show | sanctum | Called from pesan.blade.php |
| 64 | POST | `api/dosen/messages` | DosenMessageController@send | sanctum | Called from pesan.blade.php |
| 65 | POST | `api/dosen/messages/broadcast` | DosenMessageController@broadcast | sanctum | |
| 66 | POST | `api/auth/admin/login` | AdminAuthController@login | public+throttle | |
| 67 | POST | `api/auth/admin/forgot-password` | AdminAuthController@forgotPassword | public+throttle | |
| 68 | POST | `api/auth/admin/verify-otp` | AdminAuthController@verifyOtp | public+throttle | |
| 69 | POST | `api/auth/admin/reset-password` | AdminAuthController@resetPassword | public+throttle | |
| 70 | GET | `api/admin/profile` | AdminAuthController@profile | sanctum | |
| 71 | POST | `api/admin/logout` | AdminAuthController@logout | sanctum | |

### B2. Web Routes — Admin (session: `EnsureAuthenticatedAdmin`)

| # | Method | URI | Name | Controller |
|---|--------|-----|------|-----------|
| 1 | GET | `admin/login` | admin.login | AdminController@showLoginForm |
| 2 | POST | `admin/login` | admin.login.post | AdminController@login |
| 3 | GET | `admin/forgot-password` | admin.forgot-password | AdminController@showForgotPasswordForm |
| 4 | POST | `admin/forgot-password` | admin.forgot-password.post | AdminController@sendForgotPasswordOtp |
| 5 | GET | `admin/verify-otp` | admin.verify-otp | AdminController@showVerifyOtpForm |
| 6 | POST | `admin/verify-otp` | admin.verify-otp.post | AdminController@verifyOtp |
| 7 | GET | `admin/reset-password` | admin.reset-password | AdminController@showResetPasswordForm |
| 8 | POST | `admin/reset-password` | admin.reset-password.post | AdminController@resetPassword |
| 9 | GET | `admin/dashboard` | admin.dashboard | AdminController@showDashboard |
| 10 | GET | `admin/dosen` | admin.dosen | AdminController@showDosen |
| 11 | POST | `admin/dosen` | admin.dosen.store | AdminController@storeDosen |
| 12 | GET | `admin/dosen/{id}` | admin.dosen.get | AdminController@getDosen |
| 13 | PUT | `admin/dosen/{id}` | admin.dosen.update | AdminController@updateDosen |
| 14 | DELETE | `admin/dosen/{id}` | admin.dosen.delete | AdminController@deleteDosen |
| 15 | POST | `admin/dosen/import` | admin.dosen.import | AdminController@importDosen |
| 16 | GET | `admin/mahasiswa` | admin.mahasiswa | AdminController@showMahasiswa |
| 17 | POST | `admin/mahasiswa` | admin.mahasiswa.store | AdminController@storeMahasiswa |
| 18 | GET | `admin/mahasiswa/{id}` | admin.mahasiswa.get | AdminController@getMahasiswa |
| 19 | PUT | `admin/mahasiswa/{id}` | admin.mahasiswa.update | AdminController@updateMahasiswa |
| 20 | DELETE | `admin/mahasiswa/{id}` | admin.mahasiswa.delete | AdminController@deleteMahasiswa |
| 21 | POST | `admin/mahasiswa/import` | admin.mahasiswa.import | AdminController@importMahasiswa |
| 22 | GET | `admin/kursus` | admin.kursus | AdminController@showKursus |
| 23 | POST | `admin/kursus` | admin.kursus.store | AdminController@storeKursus |
| 24 | GET | `admin/kursus/{id}` | admin.kursus.get | AdminController@getKursus |
| 25 | PUT | `admin/kursus/{id}` | admin.kursus.update | AdminController@updateKursus |
| 26 | DELETE | `admin/kursus/{id}` | admin.kursus.delete | AdminController@deleteKursus |
| 27 | GET | `admin/profile` | admin.profile | AdminController@showProfile |
| 28 | PUT | `admin/profile` | admin.profile.update | AdminController@updateProfile |
| 29 | GET | `admin/notifications` | admin.notifications | AdminController@getNotifications |
| 30 | POST | `admin/logout` | admin.logout | AdminController@logout |

### B3. Web Routes — Dosen (session: `EnsureAuthenticatedDosen`)

| # | Method | URI | Name | Controller |
|---|--------|-----|------|-----------|
| 1 | GET | `dosen/login` | dosen.login | DosenController@showLoginForm |
| 2 | POST | `dosen/login` | dosen.login.post | DosenController@login |
| 3 | GET | `dosen/auth/google` | dosen.google.redirect | DosenController@redirectToGoogle |
| 4 | GET | `dosen/auth/google/callback` | dosen.google.callback | DosenController@handleGoogleCallback |
| 5 | GET | `dosen/forgot-password` | dosen.forgot-password | DosenController@showForgotPasswordForm |
| 6 | POST | `dosen/forgot-password` | dosen.forgot-password.post | DosenController@sendForgotPasswordOtp |
| 7 | GET | `dosen/verify-otp` | dosen.verify-otp | DosenController@showVerifyOtpForm |
| 8 | POST | `dosen/verify-otp` | dosen.verify-otp.post | DosenController@verifyOtp |
| 9 | GET | `dosen/reset-password` | dosen.reset-password | DosenController@showResetPasswordForm |
| 10 | POST | `dosen/reset-password` | dosen.reset-password.post | DosenController@resetPassword |
| 11 | GET | `dosen/dashboard` | dosen.dashboard | DosenController@showDashboard |
| 12 | GET | `dosen/kursus` | dosen.kursus | DosenController@showKursusSaya |
| 13 | GET | `dosen/kursus/buat` | dosen.kursus.buat | DosenController@showBuatKursus |
| 14 | POST | `dosen/kursus/buat` | dosen.kursus.store | DosenController@storeCourse |
| 15 | GET | `dosen/kursus/{id}` | dosen.kursus.detail | DosenController@getKursusDetail |
| 16 | GET | `dosen/kursus/{id}/edit` | dosen.kursus.edit | DosenController@showEditKursus |
| 17 | PUT | `dosen/kursus/{id}` | dosen.kursus.update | DosenController@updateCourse |
| 18 | GET | `dosen/kursus/{id}/modul` | dosen.kursus.modul | DosenController@showKelolaModul |
| 19 | GET | `dosen/kursus/{id}/preview` | dosen.kursus.preview | DosenController@previewKursus |
| 20 | GET | `dosen/kursus/{id}/progres` | dosen.kursus.progres | DosenController@showProgresKursus |
| 21 | POST | `dosen/kursus/{id}/publish` | dosen.kursus.publish | DosenController@publishCourse |
| 22 | POST | `dosen/kursus/{id}/module` | dosen.module.store | DosenController@storeModule |
| 23 | PUT | `dosen/kursus/{id}/module/reorder` | dosen.module.reorder | DosenController@reorderModules |
| 24 | PUT | `dosen/kursus/{id}/module/{moduleId}` | dosen.module.update | DosenController@updateModule |
| 25 | DELETE | `dosen/kursus/{id}/module/{moduleId}` | dosen.module.delete | DosenController@deleteModule |
| 26 | GET | `dosen/kursus/{id}/material/{materialId}` | dosen.material.detail | DosenController@getMaterialDetail |
| 27 | POST | `dosen/kursus/{id}/material` | dosen.material.store | DosenController@storeMaterial |
| 28 | PUT | `dosen/kursus/{id}/material/reorder` | dosen.material.reorder | DosenController@reorderMaterials |
| 29 | PUT | `dosen/kursus/{id}/material/{materialId}` | dosen.material.update | DosenController@updateMaterial |
| 30 | DELETE | `dosen/kursus/{id}/material/{materialId}` | dosen.material.delete | DosenController@deleteMaterial |
| 31 | GET | `dosen/konten/video` | dosen.kelola-video | Route::view (static) |
| 32 | GET | `dosen/konten/quiz` | dosen.kelola-quiz | Route::view (static) |
| 33 | GET | `dosen/konten/bacaan` | dosen.kelola-bacaan | Route::view (static) |
| 34 | GET | `dosen/konten/tugas` | dosen.kelola-tugas | Route::view (static) |
| 35 | GET | `dosen/progres-mahasiswa` | dosen.progres | DosenController@showProgresMahasiswa |
| 36 | GET | `dosen/pesan` | dosen.pesan | Route::view (static) |
| 37 | POST | `dosen/logout` | dosen.logout | DosenController@logout |
| 38 | GET | `dosen/fitur-belum-tersedia` | dosen.coming-soon | Route::view (static) |

### B4. Web Routes — Mahasiswa (session: `EnsureAuthenticatedMahasiswa`)

| # | Method | URI | Name | Controller |
|---|--------|-----|------|-----------|
| 1 | GET | `mahasiswa/login` | mahasiswa.login | MahasiswaController@showLoginForm |
| 2 | POST | `mahasiswa/login` | mahasiswa.post | MahasiswaController@showLoginFormPost |
| 3-8 | — | `mahasiswa/forgot-password`, `verify-otp`, `reset-password` (GET+POST) | mahasiswa.* | MahasiswaController |
| 9 | GET | `mahasiswa/dashboard` | mahasiswa.dashboard | DashboardController@index |
| 10 | GET | `mahasiswa/calendar` | mahasiswa.calendar | DashboardController@calendar |
| 11 | GET | `mahasiswa/notification` | mahasiswa.notification | DashboardController@notification |
| 12 | POST | `mahasiswa/notification/{id}/read` | mahasiswa.notification.read | DashboardController@markNotificationRead |
| 13 | POST | `mahasiswa/notification/read-all` | mahasiswa.notification.read-all | DashboardController@markAllNotificationsRead |
| 14 | GET | `mahasiswa/profile` | mahasiswa.profile | ProfileController@index |
| 15 | GET | `mahasiswa/profile/edit` | mahasiswa.profile.edit | ProfileController@edit |
| 16 | PUT | `mahasiswa/profile/update` | mahasiswa.profile.update | ProfileController@update |
| 17 | GET | `mahasiswa/courses` | mahasiswa.courses | CourseController@myCourses |
| 18 | GET | `mahasiswa/get-courses` | mahasiswa.get-courses | CourseController@index |
| 19 | GET | `mahasiswa/course/{id}` | mahasiswa.course-detail | CourseController@show |
| 20 | GET | `mahasiswa/course/{id}/learn` | mahasiswa.course-learn | CourseController@learn |
| 21 | POST | `mahasiswa/course/material/{id}/complete` | mahasiswa.material.complete | CourseController@completeMaterial |
| 22-26 | — | `mahasiswa/course/{courseId}/quiz/{quizId}/*` | mahasiswa.course-quiz, quiz-answer, quiz-flag, quiz-reset, quiz-result | CourseController |
| 27-30 | — | `mahasiswa/course/{courseId}/assignment/{assignmentId}/*` | mahasiswa.assignment-*, submit-assignment | CourseController |
| 31 | GET | `mahasiswa/course/{courseId}/module/{moduleId}/feedback` | mahasiswa.module-feedback | CourseController@moduleFeedback |
| 32 | GET | `mahasiswa/favorites` | mahasiswa.favorites | CourseController@favorites |
| 33 | POST | `mahasiswa/favorite/add` | mahasiswa.favorite.add | CourseController@addToFavorite |
| 34 | DELETE | `mahasiswa/favorite/{id}` | mahasiswa.favorite.remove | CourseController@removeFromFavorite |
| 35 | GET | `mahasiswa/checkout` | mahasiswa.checkout | CheckoutController@index |
| 36 | POST | `mahasiswa/cart/add` | mahasiswa.cart.add | CheckoutController@addToCart |
| 37 | DELETE | `mahasiswa/cart/{id}` | mahasiswa.cart.remove | CheckoutController@removeFromCart |
| 38 | GET | `mahasiswa/payment` | mahasiswa.payment | CheckoutController@payment |
| 39 | GET | `mahasiswa/payment-success` | mahasiswa.payment-success | CheckoutController@success |
| 40 | GET | `mahasiswa/finance` | mahasiswa.finance | CheckoutController@finance |
| 41 | GET | `mahasiswa/finance/transaction/{id}` | mahasiswa.transaction-detail | CheckoutController@transactionDetail |
| 42 | GET | `mahasiswa/forum` | mahasiswa.forum | Route::view (coming-soon) |
| 43 | GET | `mahasiswa/chat` | mahasiswa.chat | Route::view (coming-soon) |
| 44 | GET | `mahasiswa/apps` | mahasiswa.apps | Route::view (coming-soon) |
| 45 | GET | `mahasiswa/learning-goals` | mahasiswa.learning-goals | Route::view (coming-soon) |
| 46 | GET | `mahasiswa/news` | mahasiswa.news | Route::view (news) |
| 47 | POST | `mahasiswa/logout` | mahasiswa.logout | MahasiswaController@logout |

### B5. Dead Routes

| File | Issue |
|------|-------|
| `routes/auth.php` | File exists but is **NOT loaded** from `bootstrap/app.php` or `web.php`. References non-existent Breeze controllers (RegisteredUserController, AuthenticatedSessionController, etc.). **Dead code.** |

---

## C) Frontend API Call Inventory

### C1. Blade → API fetch() calls (Sanctum Bearer token)

These pages call `/api/*` endpoints using `Bearer + localStorage.getItem('token')`:

| # | File | Endpoint Called | Method | Auth Header |
|---|------|----------------|--------|-------------|
| 1 | `Auth/dosen/pesan.blade.php` | `/api/dosen/messages?search=...` | GET | Bearer localStorage |
| 2 | `Auth/dosen/pesan.blade.php` | `/api/dosen/messages/{studentId}` | GET | Bearer localStorage |
| 3 | `Auth/dosen/pesan.blade.php` | `/api/dosen/messages` | POST | Bearer localStorage |
| 4 | `Auth/dosen/kelola-video.blade.php` | `/api/dosen/courses?sort=terbaru&per_page=100` | GET | Bearer localStorage |
| 5 | `Auth/dosen/kelola-video.blade.php` | `/api/dosen/courses/{id}/modules` | POST | Bearer localStorage |
| 6 | `Auth/dosen/kelola-tugas.blade.php` | `/api/dosen/courses?sort=terbaru&per_page=100` | GET | Bearer localStorage |
| 7 | `Auth/dosen/kelola-tugas.blade.php` | `/api/dosen/courses/{id}/modules` | POST | Bearer localStorage |
| 8 | `Auth/dosen/kelola-quiz.blade.php` | `/api/dosen/courses?sort=terbaru&per_page=100` | GET | Bearer localStorage |
| 9 | `Auth/dosen/kelola-quiz.blade.php` | `/api/dosen/courses/{id}/modules` | POST | Bearer localStorage |
| 10 | `Auth/dosen/kelola-bacaan.blade.php` | `/api/dosen/courses?sort=terbaru&per_page=100` | GET | Bearer localStorage |
| 11 | `Auth/dosen/kelola-bacaan.blade.php` | `/api/dosen/courses/{id}/modules` | POST | Bearer localStorage |
| 12 | `pages/mahasiswa/news.blade.php` | `/api/mahasiswa/dashboard/news?limit=50` | GET | Bearer localStorage |

### C2. Blade → Web fetch() calls (session/CSRF)

| # | File | URL Called | Method | Auth |
|---|------|-----------|--------|------|
| 1 | `components/layouts/admin.blade.php` | `/admin/notifications` | GET | session cookie |
| 2 | `Auth/admin/dosen.blade.php` | `/admin/dosen/{id}` | GET | session cookie |
| 3 | `Auth/admin/mahasiswa.blade.php` | `/admin/mahasiswa/{id}` | GET | session cookie |
| 4 | `Auth/admin/kursus.blade.php` | `/admin/kursus/{id}` | GET | session cookie |
| 5 | `Auth/dosen/kelola-modul.blade.php` | `/dosen/kursus/{id}/modul/{materialId}` | GET | session cookie |
| 6 | `Auth/dosen/kelola-modul.blade.php` | `route('dosen.modul.reorder')` | PUT | CSRF |
| 7 | `Auth/dosen/kelola-modul.blade.php` | `route('dosen.modul.store')` | POST | CSRF |
| 8 | `Auth/dosen/edit-kursus.blade.php` | `route('dosen.module.reorder')` | PUT | CSRF |
| 9 | `Auth/dosen/edit-kursus.blade.php` | `route('dosen.material.reorder')` | PUT | CSRF |
| 10 | `Auth/dosen/edit-kursus.blade.php` | `/dosen/kursus/{id}/material/{materialId}` | GET | session cookie |

### C3. Blade form submissions (standard HTTP)

| # | File | Action Route | Method | Notes |
|---|------|-------------|--------|-------|
| 1 | `components/auth/login-form.blade.php` | mahasiswa.post | POST | |
| 2 | `components/auth/forgot-password-form.blade.php` | mahasiswa.forgot-password.post | POST | |
| 3 | `components/auth/verify-otp-form.blade.php` | mahasiswa.verify-otp.post | POST | |
| 4 | `components/auth/reset-password-form.blade.php` | mahasiswa.reset-password.post | POST | |
| 5 | `Auth/dosen/login.blade.php` | dosen.login.post | POST | |
| 6 | `Auth/dosen/forgot-password.blade.php` | dosen.forgot-password.post | POST | |
| 7 | `Auth/dosen/verify-otp.blade.php` | dosen.verify-otp.post | POST | |
| 8 | `Auth/dosen/reset-password.blade.php` | dosen.reset-password.post | POST | |
| 9 | `Auth/admin/login.blade.php` | admin.login.post | POST | |
| 10 | `Auth/admin/forgot-password.blade.php` | admin.forgot-password.post | POST | |
| 11 | `Auth/admin/verify-otp.blade.php` | admin.verify-otp.post | POST | |
| 12 | `Auth/admin/reset-password.blade.php` | admin.reset-password.post | POST | |
| 13 | `Auth/admin/dosen.blade.php` | admin.dosen.store / PUT / DELETE / import | POST | |
| 14 | `Auth/admin/mahasiswa.blade.php` | admin.mahasiswa.store / PUT / DELETE / import | POST | |
| 15 | `Auth/admin/kursus.blade.php` | admin.kursus.store / PUT / DELETE | POST | |
| 16 | `Auth/admin/profile.blade.php` | admin.profile.update | PUT | |
| 17 | `Auth/dosen/buat-kursus.blade.php` | dosen.kursus.store | POST | |
| 18 | `Auth/dosen/edit-kursus.blade.php` | dosen.kursus.update / module.store / material.store / module.update / material.update + DELETE | PUT/POST/DELETE | |
| 19 | `Auth/dosen/kelola-modul.blade.php` | dosen.modul.store ⚠️ / editForm / deleteForm + publish | POST/PUT/DELETE | **WRONG NAME** |
| 20 | `pages/mahasiswa/edit-profile.blade.php` | mahasiswa.profile.update | PUT | |
| 21 | `pages/mahasiswa/favorites.blade.php` | mahasiswa.favorite.remove | DELETE | |
| 22 | `pages/mahasiswa/course-learn.blade.php` | mahasiswa.favorite.add / mahasiswa.material.complete | POST | |
| 23 | `pages/mahasiswa/course-detail.blade.php` | mahasiswa.cart.add / mahasiswa.favorite.add | POST | |
| 24 | `pages/mahasiswa/checkout.blade.php` | mahasiswa.cart.remove | DELETE | |
| 25 | `pages/mahasiswa/notification.blade.php` | mahasiswa.notification.read-all / notification.read | POST | |
| 26 | `pages/mahasiswa/course-quiz.blade.php` | mahasiswa.quiz-answer / quiz-flag | POST (fetch) | |
| 27 | `pages/mahasiswa/assignment-submission.blade.php` | mahasiswa.submit-assignment | POST (fetch) | |
| 28 | `components/layouts/dosen.blade.php` | dosen.logout | POST | |
| 29 | `components/layouts/admin.blade.php` | admin.logout | POST | |
| 30 | `components/dashboard/header.blade.php` | $routes['logout'] | POST | Dynamic |

---

## D) Cross-Check Matrix

### D1. Frontend → Backend Match Status

| Frontend Call | Backend Route | Match? | Issue |
|--------------|--------------|--------|-------|
| `fetch('/api/dosen/messages?search=...')` with Bearer token | `GET api/dosen/messages` (sanctum) | ⚠️ PARTIAL | **Auth mismatch** — Token never stored in localStorage |
| `fetch('/api/dosen/messages/{id}')` with Bearer token | `GET api/dosen/messages/{studentId}` (sanctum) | ⚠️ PARTIAL | **Auth mismatch** |
| `fetch('/api/dosen/messages', POST)` with Bearer token | `POST api/dosen/messages` (sanctum) | ⚠️ PARTIAL | **Auth mismatch** |
| `fetch('/api/dosen/courses?...')` with Bearer token | `GET api/dosen/courses` (sanctum) | ⚠️ PARTIAL | **Auth mismatch** — Used ×4 in kelola-* views |
| `fetch('/api/dosen/courses/{id}/modules', POST)` with Bearer token | `POST api/dosen/courses/{courseId}/modules` (sanctum) | ⚠️ PARTIAL | **Auth mismatch** — Used ×4 in kelola-* views |
| `fetch('/api/mahasiswa/dashboard/news?...')` with Bearer token | `GET api/mahasiswa/dashboard/news` (sanctum) | ⚠️ PARTIAL | **Auth mismatch** |
| `route('dosen.modul.store')` | `dosen.module.store` | ❌ BROKEN | **Route not defined** — Blade will throw 500 error |
| `route('dosen.modul.reorder')` | `dosen.module.reorder` | ❌ BROKEN | **Route not defined** — Blade will throw 500 error |
| `fetch('/dosen/kursus/{id}/modul/{materialId}')` | `GET dosen/kursus/{id}/material/{materialId}` | ❌ BROKEN | **URL segment wrong** — `/modul/` should be `/material/` |
| editForm.action = `/dosen/kursus/{id}/modul/{id}` | `PUT dosen/kursus/{id}/material/{materialId}` | ❌ BROKEN | **URL segment wrong** — `/modul/` should be `/material/` |
| deleteForm.action = `/dosen/kursus/{id}/modul/{id}` | `DELETE dosen/kursus/{id}/material/{materialId}` | ❌ BROKEN | **URL segment wrong** — `/modul/` should be `/material/` |
| `fetch('/admin/notifications')` | `GET admin/notifications` | ✅ OK | Session auth, matches |
| `fetch('/admin/dosen/{id}')` | `GET admin/dosen/{id}` | ✅ OK | Session auth, matches |
| `fetch('/admin/mahasiswa/{id}')` | `GET admin/mahasiswa/{id}` | ✅ OK | Session auth, matches |
| `fetch('/admin/kursus/{id}')` | `GET admin/kursus/{id}` | ✅ OK | Session auth, matches |
| `fetch('/dosen/kursus/{id}/material/{id}')` in edit-kursus | `GET dosen/kursus/{id}/material/{materialId}` | ✅ OK | Session auth, matches |
| `route('dosen.module.reorder')` in edit-kursus | `PUT dosen/kursus/{id}/module/reorder` | ✅ OK | |
| `route('dosen.material.reorder')` in edit-kursus | `PUT dosen/kursus/{id}/material/reorder` | ✅ OK | |
| All `route('mahasiswa.*')` in Blade forms | Mahasiswa web routes | ✅ OK | All route names match |
| All `route('admin.*')` in Blade forms | Admin web routes | ✅ OK | All route names match |
| All `route('dosen.*')` in Blade forms (except kelola-modul) | Dosen web routes | ✅ OK | |

### D2. Backend → Frontend Coverage (Unused API endpoints)

| API Endpoint | Called from Frontend? | Notes |
|-------------|----------------------|-------|
| `GET api/mahasiswa/dashboard` | ❌ NOT CALLED | Web dashboard uses own controller |
| `GET api/mahasiswa/dashboard/progress` | ❌ NOT CALLED | Web dashboard fetches server-side |
| `GET api/mahasiswa/dashboard/courses` | ❌ NOT CALLED | Web dashboard fetches server-side |
| `GET api/mahasiswa/dashboard/agenda` | ❌ NOT CALLED | Web dashboard fetches server-side |
| `GET api/mahasiswa/courses` | ❌ NOT CALLED | Web has own CourseController |
| `GET api/mahasiswa/courses/webinar` | ❌ NOT CALLED | Web has own controller |
| `GET api/mahasiswa/courses/tiket` | ❌ NOT CALLED | Web has own controller |
| `GET api/mahasiswa/courses/kursus` | ❌ NOT CALLED | Web has own controller |
| `GET api/mahasiswa/courses/{id}` | ❌ NOT CALLED | Web has own controller |
| `POST api/mahasiswa/courses/{id}/rate` | ❌ NOT CALLED | No rating UI in Blade |
| `GET api/mahasiswa/my-courses` | ❌ NOT CALLED | Web has own controller |
| `GET api/mahasiswa/my-courses/weekly-target` | ❌ NOT CALLED | |
| `GET api/mahasiswa/my-courses/recommendations` | ❌ NOT CALLED | |
| `GET api/mahasiswa/my-courses/{id}` | ❌ NOT CALLED | |
| `POST api/mahasiswa/my-courses/.../complete` | ❌ NOT CALLED | Web uses own form |
| `GET api/mahasiswa/cart` | ❌ NOT CALLED | Web has own controller |
| `POST api/mahasiswa/cart/add` | ❌ NOT CALLED | Web has own form |
| `DELETE api/mahasiswa/cart/{id}` | ❌ NOT CALLED | Web has own form |
| `DELETE api/mahasiswa/cart` (clear all) | ❌ NOT CALLED | No UI exists |
| `GET api/mahasiswa/favorites` | ❌ NOT CALLED | Web has own controller |
| `POST api/mahasiswa/favorites/add` | ❌ NOT CALLED | Web has own form |
| `DELETE api/mahasiswa/favorites/{id}` | ❌ NOT CALLED | Web has own form |
| `POST api/mahasiswa/favorites/toggle/{courseId}` | ❌ NOT CALLED | No toggle UI |
| `GET api/mahasiswa/notifications` | ❌ NOT CALLED | Web has own controller |
| `GET api/mahasiswa/notifications/unread-count` | ❌ NOT CALLED | |
| `POST api/mahasiswa/notifications/{id}/read` | ❌ NOT CALLED | Web has own form |
| `POST api/mahasiswa/notifications/read-all` | ❌ NOT CALLED | Web has own form |
| `GET/POST api/mahasiswa/status/*` (4 endpoints) | ❌ NOT CALLED | Online status not wired |
| `GET api/mahasiswa/profile` | ❌ NOT CALLED | Web has own controller |
| `PUT api/mahasiswa/profile` | ❌ NOT CALLED | Web has own form |
| `PUT api/mahasiswa/change-password` | ❌ NOT CALLED | No UI |
| `POST api/auth/dosen/register` | ❌ NOT CALLED | No register page for web |
| `GET api/dosen/dashboard` | ❌ NOT CALLED | Web has own controller |
| `GET api/dosen/courses/{id}` | ❌ NOT CALLED | Web has own controller |
| `POST api/dosen/courses` | ❌ NOT CALLED | Web has own form |
| `PUT api/dosen/courses/{id}` | ❌ NOT CALLED | Web has own form |
| `DELETE api/dosen/courses/{id}` | ❌ NOT CALLED | No delete UI in web |
| `PUT api/dosen/courses/{courseId}/modules/{moduleId}` | ❌ NOT CALLED | Web has own form |
| `DELETE api/dosen/courses/{courseId}/modules/{moduleId}` | ❌ NOT CALLED | Web has own form |
| `GET api/dosen/students/progress` | ❌ NOT CALLED | Web has own controller |
| `GET api/dosen/students/progress/{enrollmentId}` | ❌ NOT CALLED | |
| `GET api/dosen/messages/unread-count` | ❌ NOT CALLED | Not wired in UI |
| `POST api/dosen/messages/broadcast` | ❌ NOT CALLED | No broadcast UI |
| `GET api/dosen/profile` | ❌ NOT CALLED | No profile page for dosen |
| `GET api/admin/profile` | ❌ NOT CALLED | Web has own page |
| All `api/auth/*/login,forgot,verify,reset` | ❌ NOT CALLED | Web has own forms |

**Summary**: 47 API endpoints exist that are NOT called from any Blade frontend. They're designed for a mobile/SPA client that doesn't exist yet. This is acceptable **IF** documented as "mobile API" — but currently there's confusion where some Blade pages mix web sessions with API Bearer tokens.

---

## E) Mismatch List (with patches)

### 🔴 P0 — Critical (pages are broken in production)

#### E1. Route Name Mismatch: `dosen.modul.*` → `dosen.module.*`
- **File**: `resources/views/Auth/dosen/kelola-modul.blade.php`
- **Impact**: Page throws `Route [dosen.modul.store] not defined` 500 error when rendered
- **Root cause**: Route names in `routes/dosen.php` use English `module`, but blade uses Indonesian `modul`
- **Affected**: Line 195 (`dosen.modul.store`), Line 360 (`dosen.modul.reorder`)

#### E2. URL Segment Mismatch: `/modul/` → `/material/`
- **File**: `resources/views/Auth/dosen/kelola-modul.blade.php`
- **Impact**: Edit modal fetch, edit form action, and delete form action all use `/dosen/kursus/{id}/modul/{materialId}` but the actual route is `/dosen/kursus/{id}/material/{materialId}`. Returns 404.
- **Affected**: Line 391 (fetch), Line 394 (editForm.action), Line 411 (deleteForm.action)

### 🟠 P1 — High (silent failures, features don't work)

#### E3. Auth Mismatch: Blade pages use Bearer token from localStorage (never stored)
- **Files**: 6 Blade views, 12 fetch() calls
- **Impact**: All `localStorage.getItem('token')` calls return `null`, so Bearer header sends `"Bearer null"`. Sanctum rejects with 401 Unauthorized. **ALL interactive features on dosen kelola-* pages and mahasiswa news page are silently broken.**
- **Root cause**: Web login uses `Auth::guard('dosen')->login()` (session-based), but these Blade views call API endpoints that require `auth:sanctum` with a Bearer token. The token is NEVER stored in localStorage.
- **Affected files**:
  - `Auth/dosen/pesan.blade.php` (3 fetch calls)
  - `Auth/dosen/kelola-video.blade.php` (2 fetch calls)
  - `Auth/dosen/kelola-tugas.blade.php` (2 fetch calls)
  - `Auth/dosen/kelola-quiz.blade.php` (2 fetch calls)
  - `Auth/dosen/kelola-bacaan.blade.php` (2 fetch calls)
  - `pages/mahasiswa/news.blade.php` (1 fetch call)

### 🟡 P2 — Medium

#### E4. Dead Route File: `routes/auth.php`
- **Impact**: 60-line file with Breeze scaffolding routes that references controllers that don't exist (RegisteredUserController, VerifyEmailController, etc.). Not loaded by bootstrap/app.php.
- **Risk**: Confusion for developers, no runtime impact.

#### E5. Coming-Soon Placeholders (5 pages)
- **Routes**: `mahasiswa.forum`, `mahasiswa.chat`, `mahasiswa.apps`, `mahasiswa.learning-goals`, `dosen.coming-soon`
- **Impact**: Show generic "coming soon" page. No backend functionality.
- **Risk**: No breakage but users see stub pages.

#### E6. Dosen Profile — No web profile page
- **Issue**: `api/dosen/profile` exists but no web route to view/edit dosen profile. No link in sidebar/header to a profile page.

#### E7. API endpoints for mahasiswa/change-password — No frontend UI
- **Issue**: `PUT api/mahasiswa/change-password` exists but no Blade form calls it.

---

## F) Golden Contract

### F1. Auth Architecture (MUST)

| Layer | Auth Mechanism | Token Storage |
|-------|---------------|---------------|
| Web (Blade pages) | Session guard (`mahasiswa`/`dosen`/`admin`) | Server-side session cookie |
| API (mobile/SPA) | Sanctum Bearer token | Client-side (mobile app storage / SPA memory) |
| **Blade → fetch()** | **Session cookie (same-origin, no Bearer needed)** | **No localStorage** |

**Rule**: Blade pages that make `fetch()` calls to the backend MUST either:
1. Call **web routes** (session-authenticated), OR
2. Call **API routes** but use Sanctum's cookie-based SPA authentication (stateful), OR  
3. Generate a Sanctum token at login and pass it to the Blade template

### F2. Route Naming Convention (MUST)

| Segment | Convention | Example |
|---------|-----------|---------|
| Route names | English, dot-separated | `dosen.module.store` |
| URL segments | English, kebab-case | `/dosen/kursus/{id}/module` |
| Blade `route()` calls | Must match registered `->name(...)` exactly | `route('dosen.module.store')` |

### F3. Blade → Backend Contract

| Pattern | Correct | Wrong |
|---------|---------|-------|
| Form submission | `action="{{ route('name') }}" method="POST"` + `@csrf` | Hardcoded URLs |
| JS fetch to web route | `fetch('/web/path')` with no auth header (session cookie auto-sent) | `Bearer + localStorage` |
| JS fetch to API route | Must have valid Sanctum token | `localStorage.getItem('token')` that was never set |

---

## G) Verification Plan

### G1. Post-Patch Smoke Tests

| # | Test | Steps | Expected |
|---|------|-------|----------|
| 1 | Dosen kelola-modul renders | Login as dosen → Navigate to `/dosen/kursus/{id}/modul` | Page renders without 500 error |
| 2 | Dosen add material in kelola-modul | Click "Tambah" → Fill form → Submit | Material created, page refreshes |
| 3 | Dosen edit material in kelola-modul | Click edit icon → Modal loads with data | Existing data populated in form |
| 4 | Dosen delete material in kelola-modul | Click delete → Confirm | Material deleted |
| 5 | Dosen reorder materials | Drag-and-drop materials | Order persisted (check DB) |
| 6 | Dosen kelola-video page | Navigate → Select course → Fill form → Save | Video material created via web route |
| 7 | Dosen kelola-quiz page | Navigate → Select course → Add questions → Save | Quiz material created via web route |
| 8 | Dosen kelola-bacaan page | Navigate → Select course → Fill form → Save | Reading material created via web route |
| 9 | Dosen kelola-tugas page | Navigate → Select course → Fill form → Save | Assignment created via web route |
| 10 | Dosen pesan page | Navigate → Select conversation → Send message | Messages load and send successfully |
| 11 | Mahasiswa news page | Navigate → Page loads news | News list displays |
| 12 | Admin notifications | Click bell icon in header | Notifications dropdown shows |
| 13 | Admin CRUD modals | Click edit on dosen/mahasiswa/kursus | Modal loads with correct data |

### G2. Regression Tests

| # | Test | Steps |
|---|------|-------|
| 1 | All auth flows | Login/forgot-password/verify-otp/reset-password for all 3 roles |
| 2 | Dosen course management | Create/edit/preview/publish course |
| 3 | Dosen module management in edit-kursus | Add/edit/delete/reorder modules and materials |
| 4 | Mahasiswa course browsing | Browse/detail/enroll/checkout/payment |
| 5 | Mahasiswa favorites/cart | Add/remove favorites and cart items |
| 6 | Mahasiswa notifications | View/mark-read/mark-all-read |

---

## H) Recommended Fix Strategy & Status

### Fix 1: Route names in kelola-modul (P0) — APPLIED ✅
Changed `dosen.modul.store` → `dosen.module.store` and `dosen.modul.reorder` → `dosen.module.reorder` in `kelola-modul.blade.php`.

### Fix 2: URL segments in kelola-modul (P0) — APPLIED ✅
Changed `/dosen/kursus/${courseId}/modul/${id}` → `/dosen/kursus/${courseId}/material/${id}` in 3 places within `kelola-modul.blade.php`.

### Fix 3: Auth mismatch — Sanctum stateful session auth (P1) — APPLIED ✅
**Approach chosen: Option C** — Leverage Sanctum's built-in stateful SPA support for same-origin Blade fetch() calls.

**Changes applied:**
1. **`config/sanctum.php`**: Added `'mahasiswa'`, `'dosen'`, `'admin'` guards to the `guard` array (was `['web']`, now `['web', 'mahasiswa', 'dosen', 'admin']`). This allows Sanctum's `auth:sanctum` middleware to resolve users authenticated via any of these session guards.
2. **6 Blade files** (13 fetch calls total):
   - `resources/views/Auth/dosen/pesan.blade.php` — 3 fetch calls
   - `resources/views/Auth/dosen/kelola-video.blade.php` — 2 fetch calls
   - `resources/views/Auth/dosen/kelola-tugas.blade.php` — 2 fetch calls
   - `resources/views/Auth/dosen/kelola-quiz.blade.php` — 2 fetch calls
   - `resources/views/Auth/dosen/kelola-bacaan.blade.php` — 2 fetch calls
   - `resources/views/pages/mahasiswa/news.blade.php` — 1 fetch call

   In each fetch call:
   - Removed `'Authorization': 'Bearer ' + localStorage.getItem('token')` (was always `Bearer null`)
   - Added `credentials: 'same-origin'` to send session cookies instead

**Why this works:** When a dosen logs in, `Auth::guard('dosen')->login($user)` creates a session. When Blade pages make fetch() calls to `/api/dosen/...` endpoints (protected by `auth:sanctum`), Sanctum's `EnsureFrontendRequestsAreStateful` middleware (auto-applied for same-origin requests) checks all configured guards. The `dosen` guard finds the session user, and `$request->user()` in API controllers resolves correctly.

### Fix 4: Delete `routes/auth.php` & `AuthOld/` (P2) — PENDING
`routes/auth.php` (60 lines of Breeze scaffolding) is never loaded by `bootstrap/app.php`. References 9 non-existent controllers. `app/Http/Controllers/AuthOld/` (9 files) contains dead Breeze controller copies.

**To apply manually:**
```bash
rm routes/auth.php
rm -rf app/Http/Controllers/AuthOld/
```
