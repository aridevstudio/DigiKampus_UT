# DigiKampus UT — Remediation Plan & Patch Summary

**Date:** 2026-02-22
**Scope:** Backend (API, auth, DB, security, validation, error handling) + Frontend (auth/OTP-related flows)
**Excluded:** Infrastructure, DevOps, CI/CD, Docker, monitoring, deployment

---

## A. Scope & Assumptions

### In-Scope
- All API auth controllers (Mahasiswa, Dosen, Admin)
- OTP generation, verification, and reset password flows
- Sanctum token lifecycle management
- Database schema integrity (constraints, indexes)
- Mass assignment protection on models
- Rate limiting on public endpoints
- Error message sanitization (no internal info leakage)
- Password strength enforcement

### Assumptions
- **Database access**: You can run `php artisan migrate` to apply new migrations
- **AP_DEBUG**: Must be set to `false` in production environment (`.env` on server)
- **Credential rotation**: `.env` is already in `.gitignore` (confirmed). If it was ever committed to git history, you must rotate ALL secrets (DB password, SMTP, Google OAuth, APP_KEY)
- **Frontend OTP flow**: Any mobile/SPA client consuming the API must update OTP input from 4→6 digits

---

## B. P0 Backend Fixes (Security Critical)

### B.1 — SQL Injection in DosenMessageController ✅ APPLIED

| Item | Detail |
|---|---|
| **Root Cause** | `DB::raw('CASE WHEN id_sender = ' . $dosenId . ' ...')` — direct string concatenation in raw SQL |
| **File** | `app/Http/Controllers/Api/Dosen/DosenMessageController.php` |
| **Fix** | Replaced with `selectRaw('CASE WHEN id_sender = ? ...', [$dosenId])` — parameterized binding |
| **Risk** | None — query behavior is identical, only the binding method changes |
| **Verify** | Call `GET /api/dosen/messages` as authenticated dosen — should return conversations |
| **DoD** | ☐ Endpoint returns same data as before ☐ No SQL error in logs ☐ sqlmap scan shows no injection |

### B.2 — OTP Strengthened to 6 Digits + Attempt Lockout ✅ APPLIED

| Item | Detail |
|---|---|
| **Root Cause** | 4-digit OTP (10K combos) + `rand()` (not cryptographically secure) + no attempt tracking |
| **Files** | `app/Services/OtpService.php` (NEW), `app/Http/Requests/Api/VerifyOtpRequest.php`, all 3 auth controllers |
| **Fix** | 6-digit OTP via `random_int(100000, 999999)`, max 5 attempts with 15-min lockout, 60s cooldown between requests |
| **Migration** | `2026_02_22_000001_add_attempts_to_password_reset_tokens.php` — adds `attempts` column |
| **Risk** | **Frontend impact**: OTP input fields must accept 6 digits instead of 4 |
| **Verify** | 1) Request OTP → receive 6-digit code 2) Enter wrong OTP 5 times → locked out 3) Wait or request new OTP |
| **DoD** | ☐ OTP is 6 digits ☐ Lockout after 5 wrong attempts ☐ Cooldown prevents spam ☐ Frontend updated for 6 digits |

### B.3 — Rate Limiting on Auth Endpoints ✅ APPLIED

| Item | Detail |
|---|---|
| **Root Cause** | All auth endpoints were unthrottled → brute-force possible |
| **File** | `routes/api.php` |
| **Fix** | Added `throttle:` middleware: login (10/min), forgot-password (3/min), verify-otp (5/5min), reset-password (5/5min) |
| **Risk** | Legitimate users who fail multiple times will be temporarily blocked. Display friendly error. |
| **Verify** | Hit `/api/auth/mahasiswa/login` more than 10× in 1 min → should get HTTP 429 |
| **DoD** | ☐ 429 returned on limit exceeded ☐ Appropriate retry-after header present |

### B.4 — Sanctum Token Expiration ✅ APPLIED

| Item | Detail |
|---|---|
| **Root Cause** | `config/sanctum.php` had `'expiration' => null` — tokens never expire |
| **File** | `config/sanctum.php` |
| **Fix** | Changed to `'expiration' => env('SANCTUM_TOKEN_EXPIRATION', 1440)` (24 hours) |
| **Risk** | Users must re-login after 24h. Adjust via env var if needed. |
| **Verify** | Create token, advance time 25h, call protected endpoint → should get 401 |
| **DoD** | ☐ Token expires after 24h ☐ Config is env-overridable ☐ Frontend handles 401 with re-login flow |

### B.5 — APP_DEBUG ⚠️ MANUAL ACTION REQUIRED

| Item | Detail |
|---|---|
| **Root Cause** | `.env` has `APP_DEBUG=true` |
| **Fix** | Set `APP_DEBUG=false` in production `.env` |
| **Risk** | None — only affects error display format |
| **Verify** | Trigger a 500 error in production → should NOT show stack trace |
| **DoD** | ☐ Production `.env` has `APP_DEBUG=false` ☐ Error responses show generic message |

### B.6 — Remove `$e->getMessage()` from API Responses ✅ APPLIED

| Item | Detail |
|---|---|
| **Root Cause** | Exception messages exposed to API consumers in catch blocks |
| **Files** | All 3 auth controllers (`forgotPassword`, `handleGoogleCallback`) |
| **Fix** | Replaced with `Log::error()` for internal logging, generic message to client |
| **Risk** | Developers lose debug info in response — must check logs instead |
| **Verify** | Force a mail error → response should NOT contain exception message |
| **DoD** | ☐ No `'error' => $e->getMessage()` in any API response ☐ Errors logged to `storage/logs` |

### B.7 — Mass Assignment Protection on Course Model ✅ APPLIED

| Item | Detail |
|---|---|
| **Root Cause** | `rating` and `jumlah_ulasan` were in `$fillable` — could be set via crafted request |
| **File** | `app/Models/Course.php` |
| **Fix** | Removed both fields from `$fillable`. Only `recalculateRating()` can update them. |
| **Risk** | If any admin controller manually sets these via `Course::create()`, it will silently ignore them. |
| **Verify** | Send POST/PUT to course endpoint with `rating: 5.0` → rating should remain unchanged |
| **DoD** | ☐ `$fillable` does not contain `rating` or `jumlah_ulasan` ☐ `recalculateRating()` still works |

### B.8 — Fix Default `password123` ✅ APPLIED

| Item | Detail |
|---|---|
| **Root Cause** | `AdminController` used hardcoded `password123` for new users and CSV imports |
| **File** | `app/Http/Controllers/Auth/AdminController.php` (4 locations) |
| **Fix** | Replaced with `Str::random(12)` — random password generated per user |
| **Risk** | Admin must note the password shown in flash message. CSV import users need a "reset password" flow. |
| **Verify** | Create new dosen/mahasiswa via admin → flash message shows random password, NOT `password123` |
| **DoD** | ☐ Zero occurrences of `Hash::make('password123')` in codebase ☐ CSV import uses random passwords |

### B.9 — Token Cleanup on Login ✅ APPLIED

| Item | Detail |
|---|---|
| **Root Cause** | Each login created a new token without revoking previous ones → `personal_access_tokens` grows unbounded |
| **Files** | All 3 API auth login methods |
| **Fix** | Added `$user->tokens()->delete()` before creating new token |
| **Risk** | Multi-device sessions will be killed on new login. If multi-device support is needed, use a different strategy. |
| **Verify** | Login twice → `personal_access_tokens` table should have exactly 1 row for that user |
| **DoD** | ☐ Old tokens revoked on login ☐ User can auth with latest token only |

---

## C. P0 Frontend Fixes (Auth/OTP Related)

### C.1 — OTP Input: 4 → 6 Digits

| Item | Detail |
|---|---|
| **Root Cause** | Backend now generates 6-digit OTP; frontend still expects 4 |
| **Where** | Any mobile app / SPA that calls `/api/auth/*/verify-otp` |
| **Fix** | Update OTP input UI: max length 6, validation regex `\d{6}`, placeholder "000000" |
| **DoD** | ☐ Input accepts 6 digits ☐ Submit button disabled if < 6 digits ☐ Validation error shows correct message |

### C.2 — Handle HTTP 429 (Rate Limit)

| Item | Detail |
|---|---|
| **Root Cause** | New rate limiting returns 429; frontend may not handle it |
| **Where** | Login screens, forgot-password screens, OTP verification screens |
| **Fix** | Catch 429 response, show "Terlalu banyak percobaan. Coba lagi nanti." with countdown timer from `Retry-After` header |
| **DoD** | ☐ 429 displays friendly message ☐ Countdown timer shown ☐ No raw error displayed |

### C.3 — Handle Token Expiry (401)

| Item | Detail |
|---|---|
| **Root Cause** | Tokens now expire after 24h; frontend must handle 401 gracefully |
| **Where** | Any authenticated API call |
| **Fix** | Intercept 401 globally (Axios interceptor / fetch wrapper), redirect to login with "Sesi Anda telah berakhir" message |
| **DoD** | ☐ 401 triggers logout + redirect ☐ Current page state preserved for re-login redirect |

### C.4 — Generic Error Messages on Forgot Password

| Item | Detail |
|---|---|
| **Root Cause** | Backend now returns success even for non-existent emails (anti-enumeration) |
| **Where** | Forgot password confirmation screen |
| **Fix** | Always show "Jika email terdaftar, kode OTP telah dikirim" — no "Email tidak ditemukan" message |
| **DoD** | ☐ Same success UI regardless of email existence ☐ No error state for "email not found" |

---

## D. P1 Backend Fixes (Maintainability)

### D.1 — Centralized OtpService ✅ APPLIED

| Item | Detail |
|---|---|
| **Root Cause** | Identical OTP logic duplicated 3× across auth controllers (~150 LOC each copy) |
| **File** | `app/Services/OtpService.php` (NEW — 220 LOC) |
| **Fix** | Single service handles all OTP operations: `generateAndSend()`, `verify()`, `resetPassword()` |
| **Impact** | Future OTP changes only need updating in one place |
| **DoD** | ☐ All 3 auth controllers delegate to OtpService ☐ No direct `password_reset_tokens` queries in controllers |

### D.2 — Database Unique Constraints & Indexes ✅ APPLIED

| Item | Detail |
|---|---|
| **Root Cause** | Missing DB-level data integrity constraints |
| **File** | `database/migrations/2026_02_22_000002_add_unique_constraints_and_indexes.php` |
| **Fix** | Added UNIQUE on: enrollments, carts, favorites, course_ratings, material_progress. Added INDEX on: profiles.nim, courses.id_dosen, courses.status, messages(sender, receiver) |
| **Risk** | Migration will FAIL if duplicate data already exists. Clean duplicates first. |
| **Verify** | Run `php artisan migrate`. If duplicate data exists, fix it first with a data cleanup script. |
| **DoD** | ☐ Migration runs successfully ☐ Duplicate inserts throw IntegrityConstraintViolation ☐ Login via NIM is measurably faster |

### D.3 — Password Complexity Rules ✅ APPLIED

| Item | Detail |
|---|---|
| **Files** | `ResetPasswordRequest.php`, `ChangePasswordRequest.php` |
| **Fix** | Added regex: `(?=.*[a-z])(?=.*[A-Z])(?=.*\d)` — must contain lowercase, uppercase, and digit |
| **DoD** | ☐ Password "password" is rejected ☐ Password "Password1" is accepted |

### D.4 — User Enumeration Prevention ✅ APPLIED

| Item | Detail |
|---|---|
| **Files** | `ForgotPasswordRequest.php`, all forgotPassword() methods |
| **Fix** | Removed `exists:users,email` from validation. Controller always returns success message. |
| **DoD** | ☐ Non-existent email returns 200 OK ☐ Existing email returns 200 OK with identical message |

---

## E. P1 Frontend Fixes

### E.1 — Password Strength Indicator

| Item | Detail |
|---|---|
| **Where** | Reset password and change password forms |
| **Fix** | Add real-time password strength meter showing requirements (min 8, uppercase, lowercase, digit) |
| **DoD** | ☐ Requirements shown ☐ Meter updates as user types ☐ Submit disabled until requirements met |

### E.2 — Remaining Attempt Counter on OTP Screen

| Item | Detail |
|---|---|
| **Where** | OTP verification screen |
| **Fix** | Display remaining attempts from error response message ("Sisa percobaan: X") |
| **DoD** | ☐ Attempts shown after wrong OTP ☐ Lockout message shown at 0 remaining |

---

## F. Files Changed Summary

### New Files Created
| File | Purpose |
|---|---|
| `app/Services/OtpService.php` | Centralized OTP logic (generate, verify, reset) |
| `database/migrations/2026_02_22_000001_add_attempts_to_password_reset_tokens.php` | OTP attempt tracking |
| `database/migrations/2026_02_22_000002_add_unique_constraints_and_indexes.php` | DB integrity constraints |
| `database/factories/ProfileFactory.php` | Test factory for Profile model |
| `tests/Feature/Api/Auth/MahasiswaAuthTest.php` | Auth flow tests |
| `tests/Feature/Api/Auth/OtpSecurityTest.php` | OTP security tests |
| `tests/Feature/Api/SecurityFixesTest.php` | Mass assignment & token tests |

### Modified Files
| File | Changes |
|---|---|
| `app/Http/Controllers/Api/Dosen/DosenMessageController.php` | Fixed SQL injection |
| `app/Http/Controllers/Api/Auth/MahasiswaAuthController.php` | OtpService, generic errors, token cleanup |
| `app/Http/Controllers/Api/Auth/DosenAuthController.php` | OtpService, generic errors, token cleanup |
| `app/Http/Controllers/Api/Auth/AdminAuthController.php` | OtpService, generic errors, token cleanup |
| `app/Http/Controllers/Auth/AdminController.php` | Random passwords instead of `password123` |
| `app/Models/Course.php` | Removed `rating`/`jumlah_ulasan` from `$fillable` |
| `app/Http/Requests/Api/VerifyOtpRequest.php` | 6-digit OTP, removed `exists` email check |
| `app/Http/Requests/Api/ForgotPasswordRequest.php` | Removed `exists:users,email` |
| `app/Http/Requests/Api/ResetPasswordRequest.php` | Removed `exists` email, added password complexity |
| `app/Http/Requests/Api/ChangePasswordRequest.php` | Added password complexity regex |
| `routes/api.php` | Rate limiting on all auth endpoints |
| `config/sanctum.php` | Token expiration set to 24h |
| `database/factories/UserFactory.php` | Added role/status states |

---

## G. Manual Test Plan

### G.1 — Pre-deployment Checklist
1. ☐ Run `php artisan migrate` (applies 2 new migrations)
2. ☐ Set `APP_DEBUG=false` in production `.env`
3. ☐ Set `SANCTUM_TOKEN_EXPIRATION=1440` in production `.env` (optional, defaults to 1440)
4. ☐ Clear config cache: `php artisan config:clear`

### G.2 — SQL Injection Fix
1. Login as dosen via API
2. Call `GET /api/dosen/messages`
3. ✅ Expected: Conversations list returned normally
4. ✅ Verify: No SQL errors in `storage/logs/laravel.log`

### G.3 — OTP Flow
1. Call `POST /api/auth/mahasiswa/forgot-password` with valid email
2. ✅ Expected: OTP email received with **6-digit** code
3. Call `POST /api/auth/mahasiswa/verify-otp` with correct OTP
4. ✅ Expected: Verification token returned
5. Enter wrong OTP 5 times
6. ✅ Expected: Lockout message after 5th attempt
7. Call `POST /api/auth/mahasiswa/forgot-password` with non-existent email
8. ✅ Expected: Same success response as for real email (no "email not found" error)

### G.4 — Rate Limiting
1. Call `POST /api/auth/mahasiswa/login` 11 times within 1 minute
2. ✅ Expected: 429 Too Many Requests on the 11th attempt
3. Call `POST /api/auth/mahasiswa/forgot-password` 4 times within 1 minute
4. ✅ Expected: 429 on the 4th attempt

### G.5 — Token Expiry
1. Login via API to get a token
2. Use token to call a protected endpoint
3. ✅ Expected: Works normally
4. Wait 24h (or change `SANCTUM_TOKEN_EXPIRATION` to `1` for quick test)
5. Call protected endpoint again
6. ✅ Expected: 401 Unauthorized

### G.6 — Mass Assignment
1. Create or update a course via API with `rating: 5.0` in the request body
2. ✅ Expected: The `rating` field remains unchanged (not mass-assignable)

### G.7 — Password Strength
1. Call reset-password with password `weak`
2. ✅ Expected: 422 validation error
3. Call reset-password with password `StrongPass1`
4. ✅ Expected: Success

### G.8 — Default Password
1. Create new dosen via admin panel
2. ✅ Expected: Flash message shows a random 12-character password (NOT `password123`)

---

## H. Automated Tests Added

| Test File | Tests | What They Cover |
|---|---|---|
| `tests/Feature/Api/Auth/MahasiswaAuthTest.php` | 9 tests | Login (success, wrong pw, wrong NIM, wrong role, nonaktif), token revocation, profile access, logout |
| `tests/Feature/Api/Auth/OtpSecurityTest.php` | 9 tests | Anti-enumeration, OTP send, 6-digit verify, wrong OTP tracking, lockout, expiry, password complexity |
| `tests/Feature/Api/SecurityFixesTest.php` | 4 tests | Mass assignment blocked, sanctum expiry config, token cleanup on dosen/admin login |

**Run tests:**
```bash
php artisan test --filter="MahasiswaAuthTest|OtpSecurityTest|SecurityFixesTest"
```

**Note:** Tests use `RefreshDatabase` trait. Ensure a test database is configured in `phpunit.xml` or `.env.testing`.

---

## Post-Fix Risk Score Estimate

| Category | Before | After | Change |
|---|---|---|---|
| SQL Injection | 🔴 CRITICAL | 🟢 FIXED | Parameter binding |
| OTP Brute-force | 🔴 CRITICAL | 🟢 FIXED | 6-digit + lockout + rate limit |
| Token Expiry | 🟡 HIGH | 🟢 FIXED | 24h expiration |
| Error Leakage | 🟡 HIGH | 🟢 FIXED | Generic messages + logging |
| Mass Assignment | 🟡 HIGH | 🟢 FIXED | Removed from $fillable |
| Default Password | 🟡 HIGH | 🟢 FIXED | Random per user |
| User Enumeration | 🟡 HIGH | 🟢 FIXED | Generic forgot-pw response |
| DB Constraints | 🟡 HIGH | 🟢 FIXED | Unique + indexes added |
| Code Duplication | 🟡 HIGH | 🟢 FIXED | OtpService extracted |
| Password Weakness | 🟠 MEDIUM | 🟢 FIXED | Complexity regex |
| Test Coverage | 🔴 0% | 🟡 ~15% | 22 tests on critical paths |

**Estimated Risk Score: 70 → 40 (MEDIUM)**

Remaining items for further work (P2):
- Refactor God Controllers (AdminController 1223 LOC, DosenController 1085 LOC)
- Add Laravel Policies for authorization (Course, Enrollment, etc.)
- Add pagination to all listing endpoints
- Delete dead code (AuthOld/, routes/auth.php)
- Move tinker/scramble to require-dev
- Add file upload MIME validation
- Queue OTP emails instead of synchronous send
