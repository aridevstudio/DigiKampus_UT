# DigiKampus UT — Deep Technical Analysis Report

**Generated:** 2026-02-22  
**Methodology:** Agent-skill-driven exhaustive codebase analysis  
**Application:** DigiKampus UT — Laravel 12 LMS (Learning Management System)

---

## A. Loaded Agent Skills Summary

| # | Skill ID | Purpose in This Audit | Reasoning Pattern |
|---|---|---|---|
| 1 | `laravel-expert` | Core framework quality, convention adherence, architecture patterns | Laravel-idiomatic code review: thin controllers, FormRequest usage, Eloquent best practices |
| 2 | `laravel-security-audit` | OWASP-aligned vulnerability detection specific to Laravel | Attacker mindset: IDOR, mass assignment, XSS, CSRF, file upload abuse, auth bypass |
| 3 | `production-code-audit` | Line-by-line autonomous deep scan for enterprise-grade quality | 4-phase methodology: discover → detect → fix → verify |
| 4 | `architect-review` | Architectural integrity, scalability, maintainability assessment | SOLID principles, clean architecture, separation of concerns, DDD evaluation |
| 5 | `security-auditor` | DevSecOps, OWASP Top 10, compliance, threat modeling | Defense-in-depth, zero-trust, STRIDE threat model |
| 6 | `api-design-principles` | REST API contract validation, pagination, versioning | Resource modeling, HTTP semantics, error handling standardization |
| 7 | `database-architect` | Schema modeling, indexing strategy, migration quality | Normalization analysis, N+1 detection, query pattern optimization |
| 8 | `backend-architect` | Service boundaries, resilience, observability patterns | Microservice readiness, data access layer, caching strategy |
| 9 | `performance-engineer` | Bottleneck identification, caching, query optimization | N+1 detection, eager loading, pagination, index analysis |
| 10 | `php-pro` | PHP 8+ idioms, type safety, memory efficiency | Strict typing, modern OOP, PSR compliance |
| 11 | `code-review-excellence` | Code quality, DRY, cyclomatic complexity, naming | Anti-pattern detection, code duplication, dead code |
| 12 | `testing-patterns` | Test coverage gaps, TDD strategy, factory patterns | Critical path testing, edge cases, integration tests |

---

## B. Full Directory Coverage Table

### Root Files

| File | Classification | Notes |
|---|---|---|
| `artisan` | [Generated] | Laravel CLI entry point |
| `compose.yaml` | [Config] | Docker Compose for Sail |
| `composer.json` | [Config] | PHP dependencies |
| `composer.lock` | [Generated] | Lock file |
| `package.json` | [Config] | JS dependencies |
| `package-lock.json` | [Generated] | Lock file |
| `phpunit.xml` | [Config] | Test configuration |
| `postcss.config.js` | [Config] | PostCSS |
| `tailwind.config.js` | [Config] | Tailwind CSS |
| `vite.config.js` | [Config] | Vite bundler |
| `vercel.json` | [Config] [To Analyze] | Deployment config — security implications |
| `.env` | [Config] [CRITICAL] | Contains hardcoded credentials |
| `.env.example` | [Config] | Template |
| `.editorconfig` | [Config] | Editor settings |
| `.gitignore` | [Config] | Git ignore |
| `.gitattributes` | [Config] | Git attributes |
| `.vemto_settings` | [Config] | Vemto scaffolding tool |

### `app/Http/Controllers/` (Core Logic)

| File | Classification | Lines | Complexity |
|---|---|---|---|
| `Controller.php` | [To Analyze] | Base controller | Low |
| `ProfileController.php` | [To Analyze] | Profile CRUD | Medium |
| `Api/Auth/MahasiswaAuthController.php` | [To Analyze] | ~400 | High |
| `Api/Auth/DosenAuthController.php` | [To Analyze] | ~400 | High |
| `Api/Auth/AdminAuthController.php` | [To Analyze] | ~291 | High |
| `Api/Mahasiswa/CourseController.php` | [To Analyze] | ~221 | Medium |
| `Api/Mahasiswa/MyCourseController.php` | [To Analyze] | ~270 | Medium |
| `Api/Mahasiswa/CartController.php` | [To Analyze] | ~170 | Low |
| `Api/Mahasiswa/FavoriteController.php` | [To Analyze] | ~172 | Low |
| `Api/Mahasiswa/NotificationController.php` | [To Analyze] | ~167 | Low |
| `Api/Mahasiswa/StatusController.php` | [To Analyze] | ~107 | Low |
| `Api/Mahasiswa/MahasiswaDashboardController.php` | [To Analyze] | Medium | Medium |
| `Api/Dosen/DosenCourseController.php` | [To Analyze] | ~613 | High |
| `Api/Dosen/DosenDashboardController.php` | [To Analyze] | Medium | Medium |
| `Api/Dosen/DosenMessageController.php` | [To Analyze] | ~359 | High |
| `Api/Dosen/DosenStudentProgressController.php` | [To Analyze] | Medium | Medium |
| `Auth/AdminController.php` | [To Analyze] | **1223** | **CRITICAL — GOD CLASS** |
| `Auth/DosenController.php` | [To Analyze] | **1085** | **CRITICAL — GOD CLASS** |
| `Auth/MahasiswaController.php` | [To Analyze] | ~300 | Medium |
| `Mahasiswa/CheckoutController.php` | [To Analyze] | Medium | Medium |
| `Mahasiswa/CourseController.php` | [To Analyze] | Medium | Medium |
| `Mahasiswa/DashboardController.php` | [To Analyze] | Medium | Medium |
| `Mahasiswa/ProfileController.php` | [To Analyze] | Medium | Low |
| `AuthOld/*` (9 files) | [Irrelevant] | Dead code from Breeze scaffolding | Should delete |

### `app/Http/Middleware/`

| File | Classification |
|---|---|
| `Authenticate.php` | [To Analyze] |
| `EnsureAuthenticatedAdmin.php` | [To Analyze] |
| `EnsureAuthenticatedDosen.php` | [To Analyze] |
| `EnsureAuthenticatedMahasiswa.php` | [To Analyze] |
| `RedirectIfAuthenticatedAdmin.php` | [To Analyze] |
| `RedirectIfAuthenticatedDosen.php` | [To Analyze] |
| `RedirectIfAuthenticatedMahasiswa.php` | [To Analyze] |

### `app/Http/Requests/`

| File | Classification |
|---|---|
| `ProfileUpdateRequest.php` | [To Analyze] |
| `Api/AdminLoginRequest.php` | [To Analyze] |
| `Api/ChangePasswordRequest.php` | [To Analyze] |
| `Api/DosenLoginRequest.php` | [To Analyze] |
| `Api/DosenRegisterRequest.php` | [To Analyze] |
| `Api/ForgotPasswordRequest.php` | [To Analyze] |
| `Api/MahasiswaLoginRequest.php` | [To Analyze] |
| `Api/ResetPasswordRequest.php` | [To Analyze] |
| `Api/UpdateProfileRequest.php` | [To Analyze] |
| `Api/UploadPhotoRequest.php` | [To Analyze] |
| `Api/VerifyOtpRequest.php` | [To Analyze] |
| `Auth/LoginRequest.php` | [To Analyze] |
| `Auth/MahasiswaRequest.php` | [To Analyze] |
| `Mahasiswa/Logout.php` | [To Analyze] |

### `app/Http/Resources/`

| File | Classification |
|---|---|
| `AdminResource.php` | [To Analyze] |
| `AgendaResource.php` | [To Analyze] |
| `CourseResource.php` | [To Analyze] |
| `DosenResource.php` | [To Analyze] |
| `EnrolledCourseResource.php` | [To Analyze] |
| `MahasiswaResource.php` | [To Analyze] |
| `MaterialResource.php` | [To Analyze] |
| `MyCourseResource.php` | [To Analyze] |
| `NewsResource.php` | [To Analyze] |

### `app/Models/` (16 models)

| File | Classification |
|---|---|
| `User.php` | [To Analyze] |
| `Profile.php` | [To Analyze] |
| `Course.php` | [To Analyze] |
| `CourseMaterial.php` | [To Analyze] |
| `CourseModule.php` | [To Analyze] |
| `CourseRating.php` | [To Analyze] |
| `Enrollment.php` | [To Analyze] |
| `Cart.php` | [To Analyze] |
| `Favorite.php` | [To Analyze] |
| `Jurusan.php` | [To Analyze] |
| `MaterialProgress.php` | [To Analyze] |
| `Message.php` | [To Analyze] |
| `News.php` | [To Analyze] |
| `Notification.php` | [To Analyze] |
| `Agenda.php` | [To Analyze] |
| `Assignment.php` | [To Analyze] |

### `database/migrations/` (22 files)

All classified as [To Analyze] — schema correctness and indexing reviewed below.

### `database/seeders/` (6 files)

All classified as [To Analyze] — data integrity validation.

### `routes/` (7 files)

All classified as [To Analyze] — route security and naming conventions.

### `config/` (12 files)

All classified as [Config] — security-relevant settings reviewed.

### `resources/views/` (~70+ Blade files)

Classified as [To Analyze] for XSS/output escaping review.

### `tests/` (8 files)

Classified as [To Analyze] — coverage gap analysis.

### `vendor/` | [Vendor] — Excluded from analysis.
### `node_modules/` | [Vendor] — Excluded.
### `.vemto/` | [Generated] — Scaffolding templates, not runtime code.
### `.agent/` | [Irrelevant] — Analysis skills repo, not application code.
### `public/build/`, `public/assets/` | [Generated] — Build output.

---

## C. Architecture Deep Dive

**Skill Applied:** `architect-review`, `laravel-expert`, `backend-architect`  
**Reasoning Pattern:** Clean Architecture evaluation, SOLID compliance, separation of concerns

### C.1 Application Type

Multi-role LMS (Learning Management System) with three actor types:
- **Admin** — Manages users (dosen/mahasiswa), courses
- **Dosen** (Lecturer) — Creates/manages courses, modules, materials, monitors progress, messaging
- **Mahasiswa** (Student) — Enrolls in courses, learns materials, takes quizzes, manages favorites/cart

### C.2 Tech Stack

| Layer | Technology |
|---|---|
| Framework | Laravel 12 (PHP 8.2+) |
| Auth (API) | Laravel Sanctum (token-based) |
| Auth (Web) | Laravel Session Guards (custom: admin, dosen, mahasiswa) |
| Database | MySQL 8.4 |
| API Docs | Scramble (OpenAPI) |
| Frontend | Blade + Tailwind CSS + Vite |
| OAuth | Laravel Socialite (Google) |
| Email | SMTP (Gmail) |
| Deployment | Vercel (serverless PHP) |
| Dev Env | Docker (Sail) |

### C.3 Architectural Pattern

**Current:** Monolithic MVC with role-based route separation.

**Route Architecture:**
- `routes/web.php` → includes `mahasiswa.php`, `admin.php`, `dosen.php`
- `routes/api.php` → API endpoints for mobile/SPA consumers
- `routes/auth.php` → Legacy Breeze auth routes (dead code)

**Anti-Patterns Identified:**

| Anti-Pattern | Location | Severity | Skill |
|---|---|---|---|
| **God Controller** | `AdminController.php` (1223 lines) | **Critical** | `architect-review` |
| **God Controller** | `DosenController.php` (1085 lines) | **Critical** | `architect-review` |
| **Massive code duplication** | Auth logic repeated 3x (Mahasiswa/Dosen/Admin) | **High** | `code-review-excellence` |
| **No Service Layer** | Business logic lives in controllers | **High** | `laravel-expert` |
| **No Policy/Gate usage** | Authorization checked manually in controllers | **High** | `laravel-security-audit` |
| **Dual route systems** | Both web and API routes for same features | **Medium** | `architect-review` |
| **Dead code** | `AuthOld/` folder (9 files), `routes/auth.php` | **Low** | `production-code-audit` |

### C.4 Architectural Recommendations

1. **Extract Service Layer:** Create `AuthService`, `CourseService`, `EnrollmentService`, `NotificationService` to decouple business logic from controllers.
2. **Refactor God Controllers:** Split `AdminController` into `AdminAuthController`, `AdminDosenController`, `AdminMahasiswaController`, `AdminCourseController`.
3. **Implement Policies:** Create `CoursePolicy`, `EnrollmentPolicy`, `MaterialPolicy` for authorization.
4. **Deduplicate Auth Flows:** Create `AbstractAuthController` or a shared `PasswordResetService` used by all three roles.
5. **Remove Dead Code:** Delete `AuthOld/` folder and `routes/auth.php`.

---

## D. Module-by-Module Breakdown

**Skill Applied:** `laravel-expert`, `code-review-excellence`, `php-pro`

### D.1 Authentication Module

| Issue | File | Risk | Description |
|---|---|---|---|
| OTP is only 4 digits | All auth controllers | **High** | Only 10,000 combinations. Brute-forceable without rate limiting on the API side. |
| No OTP attempt limiting | `MahasiswaAuthController::verifyOtp()` | **Critical** | An attacker can try all 10,000 OTP values in seconds. No rate limit on verify endpoint. |
| Error message leaks | `forgotPassword()` all roles | **Medium** | "Email tidak terdaftar" reveals whether an email exists in the system (user enumeration). |
| Duplicate logic x3 | `forgotPassword`, `verifyOtp`, `resetPassword` | **High** | Identical OTP logic duplicated across Mahasiswa, Dosen, Admin controllers (~150 LOC each). |
| `$e->getMessage()` in response | `forgotPassword()` error handler | **High** | Exception messages exposed to API consumers. Can leak internal paths/configs. |
| Password only min:8 | `ResetPasswordRequest` | **Medium** | No complexity requirements (uppercase, number, special char). |
| Token never expires in Sanctum | `config/sanctum.php`: `'expiration' => null` | **High** | API tokens live forever. If compromised, permanent access. |
| No old token cleanup | All login endpoints | **Medium** | Tokens accumulate forever in `personal_access_tokens` table. |
| MahasiswaController password check duplicate | `showLoginFormPost()` lines ~50-60 | **Low** | Password checked twice: first for error, second for login. Redundant. |

### D.2 Course Module

| Issue | File | Risk | Description |
|---|---|---|---|
| No pagination on course listing | `Api\Mahasiswa\CourseController::index()` | **High** | Uses `->get()` instead of `->paginate()`. Loads ALL courses into memory. |
| No `$fillable` / `$guarded` validation | `Course::$fillable` is broad | **Medium** | 12 fields in fillable including `rating` and `jumlah_ulasan` — these should be system-calculated, not mass-assignable. |
| LIKE search without index | `Course::scopeSearch()` | **Medium** | `LIKE "%keyword%"` cannot use indexes. Full table scan on every search. |
| No authorization on `rate()` | `CourseController::rate()` | **Medium** | Any authenticated user can rate any course, even if not enrolled. |
| Rating recalculation race condition | `Course::recalculateRating()` | **Medium** | Two concurrent ratings could produce incorrect averages without DB locks. |

### D.3 Enrollment Module

| Issue | File | Risk | Description |
|---|---|---|---|
| Progress recalculation N+1 potential | `Enrollment::recalculateProgress()` | **Medium** | Executes separate count queries inside loop context. |
| No unique constraint | `enrollments` table | **High** | No unique key on `(id_mahasiswa, id_course)`. Student can be enrolled multiple times. |
| `float` for progress | Migration | **Low** | Using `float` for percentage. Should use `decimal(5,2)` for precision. |

### D.4 Cart Module

| Issue | File | Risk | Description |
|---|---|---|---|
| No payment integration | `CartController` | **Informational** | Cart exists but no actual payment processing. Enrollments created by admin. |
| Cart stale items | No cleanup | **Low** | Cart items never expire. Prices could change. |

### D.5 Notification Module

| Issue | File | Risk | Description |
|---|---|---|---|
| No pagination | `NotificationController::index()` | **Medium** | `->get()` loads all notifications. Can grow unbounded. |
| Owner check OK | `markAsRead()` | Good | Properly checks `id_mahasiswa = user->id`. |

### D.6 Messaging Module

| Issue | File | Risk | Description |
|---|---|---|---|
| SQL injection risk | `DosenMessageController::index()` | **Critical** | `DB::raw('CASE WHEN id_sender = ' . $dosenId . ' THEN ...')` — Direct string concatenation in raw SQL. |
| No message content sanitization | `DosenMessageController::send()` | **Medium** | Message content stored as-is without XSS filtering. |
| No pagination on conversations | `DosenMessageController::index()` | **Medium** | Loads all conversations without limit. |

### D.7 Admin Module (God Class)

| Issue | File | Risk | Description |
|---|---|---|---|
| **1223 lines** in single controller | `AdminController.php` | **Critical** | Violates Single Responsibility Principle. Contains auth, CRUD for dosen, mahasiswa, courses, profile, notifications, CSV import. |
| Default password `password123` | `storeDosen()`, `storeMahasiswa()` | **High** | Hardcoded weak default password. No forced password change mechanism. |
| CSV import without validation | `importDosen()`, `importMahasiswa()` | **High** | Bulk import from CSV likely has insufficient row-level validation. |
| No CSRF validation on JSON endpoints | `getDosen()`, `getMahasiswa()` GET | **Low** | These return JSON but are web routes — should be fine for GET. |

### D.8 Dosen Module (God Class)

| Issue | File | Risk | Description |
|---|---|---|---|
| **1085 lines** in single controller | `DosenController.php` | **Critical** | Auth, dashboard, courses, modules, materials, progress, messaging all in one. |
| No file type validation on thumbnails | `storeCourse()` | **Medium** | No explicit MIME validation on thumbnail upload. |
| Unhandled exception silenced | `showDashboard()` Agenda catch | **Low** | `catch (\Exception $e) {}` silently swallows errors. |

---

## E. Security & Risk Report

**Skill Applied:** `laravel-security-audit`, `security-auditor`  
**Reasoning Pattern:** OWASP Top 10 (2021), STRIDE threat model, attacker mindset

### E.1 CRITICAL Vulnerabilities

#### E.1.1 Credentials in `.env` Committed to Repository
- **File:** [.env](.env#L33-L35)
- **Risk:** CRITICAL
- **Details:** Gmail SMTP password (`bedjuhwforcurazb`), database credentials (`DB_HOST=147.139.241.73`, `DB_USERNAME=large`, `DB_PASSWORD=-Kambing12345`), Google OAuth secrets — all in plaintext in `.env` which appears to be committed.
- **OWASP:** A07:2021 — Security Misconfiguration
- **Impact:** Full database access, email account compromise, OAuth impersonation
- **Fix:** Rotate ALL credentials immediately. Ensure `.env` is in `.gitignore`. Use secret manager.

#### E.1.2 SQL Injection in Message Controller
- **File:** [app/Http/Controllers/Api/Dosen/DosenMessageController.php](app/Http/Controllers/Api/Dosen/DosenMessageController.php#L49)
- **Risk:** CRITICAL
- **Code:** `DB::raw('CASE WHEN id_sender = ' . $dosenId . ' THEN id_receiver ELSE id_sender END as student_id')`
- **Exploit:** If `$dosenId` can be manipulated (e.g., through a compromised Sanctum token), attacker can inject SQL.
- **Fix:**
```php
// BEFORE (vulnerable)
DB::raw('CASE WHEN id_sender = ' . $dosenId . ' THEN ...')

// AFTER (safe)
DB::raw('CASE WHEN id_sender = ? THEN id_receiver ELSE id_sender END as student_id', [$dosenId])
// Or better yet, use Eloquent query builder
```

#### E.1.3 OTP Brute-Force (No Rate Limiting)
- **File:** All `verifyOtp()` API endpoints
- **Risk:** CRITICAL
- **Details:** 4-digit OTP (10,000 combinations) + no rate limit on verification endpoint = brute-forceable in seconds.
- **Fix:** Add `throttle:5,5` middleware to OTP verification routes. Consider 6-digit OTP. Lock after 3 failed attempts.

### E.2 HIGH Vulnerabilities

#### E.2.1 Permanent API Tokens
- **File:** [config/sanctum.php](config/sanctum.php#L52)
- **Risk:** HIGH
- **Details:** `'expiration' => null` means tokens never expire.
- **Fix:** Set `'expiration' => 1440` (24 hours) or implement refresh token pattern.

#### E.2.2 Debug Mode & Error Leakage
- **File:** [.env](.env#L4): `APP_DEBUG=true`
- **File:** All `forgotPassword()` methods: `'error' => $e->getMessage()`
- **Risk:** HIGH
- **Fix:** Set `APP_DEBUG=false` in production. Remove `$e->getMessage()` from API responses.

#### E.2.3 User Enumeration
- **File:** All auth endpoints
- **Risk:** HIGH
- **Details:** "Email tidak terdaftar" / "NIM tidak ditemukan" reveals account existence.
- **Fix:** Use generic message: "If this account exists, an OTP has been sent."

#### E.2.4 Default Password `password123`
- **File:** [AdminController.php](app/Http/Controllers/Auth/AdminController.php) — `storeDosen()`, `storeMahasiswa()`
- **Risk:** HIGH
- **Details:** All newly created users get `password123`. No force-change mechanism.
- **Fix:** Generate random password, email it, require change on first login.

#### E.2.5 Mass Assignment on Sensitive Fields
- **File:** [Course.php model](app/Models/Course.php#L16-L31)
- **Risk:** HIGH
- **Details:** `rating` and `jumlah_ulasan` are in `$fillable`. A crafted request could set arbitrary ratings.
- **Fix:** Remove `rating` and `jumlah_ulasan` from `$fillable`. These should only be set by `recalculateRating()`.

#### E.2.6 No Authorization Policies (IDOR Risk)
- **Risk:** HIGH
- **Details:** No `Policy` or `Gate` definitions exist. Authorization is manually checked in some controllers but missing in others.
- **Examples:**
  - Any authenticated user can rate any course (no enrollment check)
  - No ownership validation in some API endpoints
- **Fix:** Create Laravel Policies for Course, Enrollment, Notification, Message.

### E.3 MEDIUM Vulnerabilities

| Issue | Risk | Details |
|---|---|---|
| No CORS configuration | Medium | No `cors.php` configuration visible. API may be accessible from any origin. |
| No rate limiting on login endpoints | Medium | Brute-force password attacks possible. |
| Search LIKE injection | Medium | `%keyword%` — while not SQL injection (parameterized), allows ReDoS-style abuse. |
| No Content-Security-Policy header | Medium | Missing CSP makes XSS exploitation easier. |
| `provider` field not validated on OAuth | Medium | Google OAuth in `DosenAuthController` creates users without strict role validation. |
| Session fixation potential | Medium | Custom auth guards don't call `$request->session()->regenerate()` on all login paths. |

### E.4 LOW Vulnerabilities

| Issue | Risk |
|---|---|
| Mixed language (Indonesian/English) in error messages | Low |
| No password complexity validation | Low |
| `MAIL_FROM_ADDRESS` differs from `MAIL_USERNAME` | Low |
| Profile photo stored publicly without content type validation | Low |
| No `Strict-Transport-Security` header | Low |

---

## F. Data Flow & Control Flow Mapping

**Skill Applied:** `database-architect`, `backend-architect`

### F.1 Authentication Flows

```
[API Login] Mahasiswa → NIM+Password → User lookup via Profile.nim → Hash check → Sanctum token
[API Login] Dosen → Email+Password → User lookup → Hash check → Sanctum token
[API Login] Admin → Email+Password → User lookup → Hash check → Sanctum token
[Web Login] Mahasiswa → NIM+Password → Auth::guard('mahasiswa')->login() → Session
[Web Login] Dosen → Email+Password → Auth::guard('dosen')->login() → Session
[Web Login] Admin → Email+Password → Auth::guard('admin')->login() → Session
[OAuth] Dosen → Google → Socialite → find_or_create User → Sanctum/Session token
```

**Warning:** All guards (`web`, `mahasiswa`, `admin`, `dosen`) use the SAME `users` provider and SAME `users` table. Guard separation is only by session name — no role enforcement at guard level. A mahasiswa session cookie could theoretically access admin routes if middleware doesn't explicitly check role.

### F.2 Data Model (Entity Relationships)

```
User (1) ←→ (1) Profile
User (1) ←→ (N) Enrollment (as mahasiswa)
User (1) ←→ (N) Course (as dosen)
User (1) ←→ (N) Cart
User (1) ←→ (N) Favorite
User (1) ←→ (N) Notification
User (1) ←→ (N) Agenda
Course (1) ←→ (N) CourseModule
Course (1) ←→ (N) CourseMaterial
Course (1) ←→ (N) CourseRating
Course (1) ←→ (N) Enrollment
Course (1) ←→ (N) Assignment
CourseModule (1) ←→ (N) CourseMaterial
CourseMaterial (1) ←→ (N) MaterialProgress
Jurusan (1) ←→ (N) Course
Jurusan (1) ←→ (N) Profile
Message → sender_id (User), receiver_id (User)
```

### F.3 Missing Database Constraints

| Table | Missing Constraint | Risk |
|---|---|---|
| `enrollments` | No UNIQUE on `(id_mahasiswa, id_course)` | Double enrollment possible |
| `carts` | No UNIQUE on `(id_mahasiswa, id_course)` | Duplicate cart entries (checked in code but not DB) |
| `favorites` | No UNIQUE on `(id_mahasiswa, id_course)` | Duplicate favorites possible |
| `course_ratings` | No UNIQUE on `(id_mahasiswa, id_course)` | Double rating possible (checked in code) |
| `material_progress` | No UNIQUE on `(id_mahasiswa, id_material)` | Progress duplication possible |
| `courses` | No INDEX on `id_dosen` | Slow query on dosen's courses |
| `courses` | No INDEX on `status` | Slow filtering on active courses |
| `enrollments` | No INDEX on `(id_mahasiswa, id_course)` | Slow enrollment lookups |
| `profiles` | No INDEX on `nim` | Slow login (NIM lookup used for auth) |
| `messages` | No INDEX on `(id_sender, id_receiver)` | Slow conversation queries |

---

## G. Dependency & Supply Chain Audit

**Skill Applied:** `security-auditor` - supply chain security

### G.1 Production Dependencies

| Package | Version | Risk Assessment |
|---|---|---|
| `laravel/framework` | ^12.0 | Low — Active LTS |
| `laravel/sanctum` | ^4.2 | Low — Active maintenance |
| `laravel/socialite` | ^5.24 | Low — Active maintenance |
| `laravel/breeze` | ^2.3 | **Medium** — Only used for scaffolding, adds dead code to project |
| `laravel/tinker` | ^2.10.1 | **Medium** — Should NOT be in production. Security risk. |
| `dedoc/scramble` | ^0.13.6 | **Medium** — API docs should be dev-only. Exposes API schema in production. |

### G.2 Dev Dependencies

| Package | Version | Status |
|---|---|---|
| `phpunit/phpunit` | ^11.5.3 | OK |
| `fakerphp/faker` | ^1.23 | OK |
| `mockery/mockery` | ^1.6 | OK |
| `laravel/pint` | ^1.24 | OK |
| `laravel/pail` | ^1.2.2 | OK |
| `nunomaduro/collision` | ^8.6 | OK |

### G.3 Recommendations

1. Move `laravel/tinker` and `dedoc/scramble` to `require-dev`
2. Remove `laravel/breeze` if scaffolding is complete
3. Add `composer audit` to CI pipeline
4. Consider adding `roave/security-advisories` to prevent installing packages with known vulnerabilities

---

## H. DevOps & Deployment Review

**Skill Applied:** `backend-architect`, `security-auditor`

### H.1 Vercel Deployment

| Issue | Risk | Details |
|---|---|---|
| APP_DEBUG not overridden | **Critical** | `.env` has `APP_DEBUG=true`. Vercel config sets `APP_DEBUG=false` but `.env` is loaded first in some contexts. |
| Session driver `cookie` | **High** | Vercel config uses `SESSION_DRIVER=cookie`. Cookie sessions can be tampered with if `APP_KEY` leaks. |
| No CI/CD pipeline | **High** | No GitHub Actions, no deployment gates, no automated test runs. |
| No health check tests | **Medium** | `/up` endpoint exists but no monitoring configured. |
| Cache driver `array` | **Medium** | Vercel uses `CACHE_DRIVER=array` — cache is per-request only. No persistence. |
| Serverless + MySQL remote | **Medium** | Vercel functions connect to remote MySQL (147.139.241.73). Cold starts + round-trip latency. |

### H.2 Docker (Sail)

| Issue | Risk | Details |
|---|---|---|
| Sail runtime 8.5 | **Low** | Should match PHP 8.2+ from `composer.json`. |
| `MYSQL_ALLOW_EMPTY_PASSWORD: 1` | **Medium** | Allows empty root password in dev — OK for dev but dangerous if exposed. |
| No production Docker config | **Medium** | Only development Sail config exists. No Dockerfile for production. |

### H.3 Missing DevOps

- No `Dockerfile` for production
- No `.github/workflows/` CI/CD
- No database migration automation
- No environment-based config validation
- No secret rotation policy
- No backup strategy documented

---

## I. Performance & Scalability Analysis

**Skill Applied:** `performance-engineer`, `database-architect`

### I.1 Critical Performance Issues

#### I.1.1 No Pagination on API Course Listing
- **File:** `Api\Mahasiswa\CourseController::index()` — `->get()` loads all
- **Impact:** Memory explosion with thousands of courses
- **Fix:** Replace with `->paginate(15)`

#### I.1.2 N+1 Query Patterns
- **Locations:**
  - `DosenController::showDashboard()` — Loops through courses counting enrollments
  - `DosenCourseController::index()` — `$course->enrollments->count()` inside `map()`
  - `DosenMessageController::index()` — N+1 for conversations
- **Fix:** Use `withCount('enrollments')` and eager loading

#### I.1.3 Missing Database Indexes
- See Section F.3 — at least 9 missing indexes on frequently queried columns
- **Impact:** Every login does a full table scan on `profiles.nim`
- **Fix:** Add composite indexes for common query patterns

#### I.1.4 Full Collection Loading
- Multiple controllers use `->get()` then `->map()` instead of pagination
- Notifications, favorites, cart items all loaded without limits

### I.2 Caching Gaps

| What Should Be Cached | Current | Recommendation |
|---|---|---|
| Active course list | Not cached | Cache for 5 mins with tag invalidation |
| Jurusan list | Not cached | Cache for 1 hour (rarely changes) |
| Dashboard stats | Computed on every request | Cache for 1 min |
| User profile | Not cached | Cache per user, invalidate on update |

### I.3 Scalability Constraints

| Constraint | Impact | Mitigation |
|---|---|---|
| Single MySQL instance | Single point of failure | Read replicas for queries |
| No queue processing | Email sent synchronously | Dispatch OTP emails to queue |
| No WebSocket for messaging | HTTP polling required | Consider Pusher/Reverb for real-time |
| Vercel serverless cold starts | ~2s PHP cold start | Consider dedicated hosting for API |
| No CDN for thumbnails | Direct disk serving | Use S3 + CloudFront |

---

## J. Testing Coverage & Gaps

**Skill Applied:** `testing-patterns`

### J.1 Current Test Inventory

| File | Type | Relevant? |
|---|---|---|
| `tests/Unit/ExampleTest.php` | Unit | Default scaffolding — no real tests |
| `tests/Feature/ExampleTest.php` | Feature | Default scaffolding — no real tests |
| `tests/Feature/ProfileTest.php` | Feature | Framework profile test — not customized |
| `tests/Feature/Auth/AuthenticationTest.php` | Feature | Tests Breeze auth — **dead code** (Breeze auth not used) |
| `tests/Feature/Auth/EmailVerificationTest.php` | Feature | Tests Breeze — **dead code** |
| `tests/Feature/Auth/PasswordConfirmationTest.php` | Feature | Tests Breeze — **dead code** |
| `tests/Feature/Auth/PasswordResetTest.php` | Feature | Tests Breeze — **dead code** |
| `tests/Feature/Auth/PasswordUpdateTest.php` | Feature | Tests Breeze — **dead code** |
| `tests/Feature/Auth/RegistrationTest.php` | Feature | Tests Breeze — **dead code** |

### J.2 Effective Test Coverage: **~0%**

**Zero tests exist for the actual application logic.** All existing tests are Breeze scaffolding tests that test the abandoned auth system.

### J.3 Critical Test Gaps (P0)

| Missing Test | Priority | Why |
|---|---|---|
| Mahasiswa login (API + Web) | P0 | Core auth flow |
| Dosen login (API + Web) | P0 | Core auth flow |
| Admin login (Web) | P0 | Core auth flow |
| OTP generation & verification | P0 | Security-critical |
| Course enrollment flow | P0 | Core business logic |
| Material completion & progress | P0 | Core business logic |
| Cart add/remove/checkout | P0 | Payment-adjacent |
| Role-based access control | P0 | Security-critical |
| IDOR prevention | P0 | Security-critical |
| Dosen can only edit own courses | P0 | Authorization |

### J.4 Recommended Test Strategy

1. Create `tests/Feature/Api/Auth/` for all 3 role auth flows
2. Create `tests/Feature/Api/Mahasiswa/` for student endpoints
3. Create `tests/Feature/Api/Dosen/` for dosen endpoints
4. Create `tests/Feature/Web/Admin/` for admin panel
5. Add database factories for all models (only `UserFactory` exists)
6. Target: 80% coverage on controllers, 100% on auth flows

---

## K. Refactor Roadmap

### P0 — IMMEDIATE (Security Critical)

| # | Action | Effort | Impact |
|---|---|---|---|
| 1 | **Rotate all credentials** — DB password, SMTP, Google OAuth, APP_KEY | 1h | Prevents immediate compromise |
| 2 | **Ensure `.env` is not committed** to Git. Run `git rm --cached .env` | 5m | Prevents credential leak |
| 3 | **Fix SQL injection** in `DosenMessageController` | 15m | Prevents data breach |
| 4 | **Add rate limiting** to OTP endpoints: `throttle:5,5` | 30m | Prevents OTP brute-force |
| 5 | **Set `APP_DEBUG=false`** in all production environments | 5m | Prevents info leakage |
| 6 | **Set Sanctum token expiration** to 24h: `'expiration' => 1440` | 5m | Limits token compromise window |
| 7 | **Remove `rating`/`jumlah_ulasan` from Course `$fillable`** | 5m | Prevents rating manipulation |
| 8 | **Remove `$e->getMessage()`** from all API error responses | 30m | Prevents internal info leakage |

### P1 — HIGH PRIORITY (Architecture & Quality)

| # | Action | Effort | Impact |
|---|---|---|---|
| 9 | **Refactor `AdminController`** into 4-5 focused controllers | 4h | Maintainability |
| 10 | **Refactor `DosenController`** into 4-5 focused controllers | 4h | Maintainability |
| 11 | **Extract Service Layer** — `AuthService`, `OtpService`, `CourseService` | 6h | DRY, testability |
| 12 | **Add database unique constraints** on enrollment, cart, favorites, ratings | 1h | Data integrity |
| 13 | **Add database indexes** on `profiles.nim`, `courses.id_dosen`, `courses.status`, `enrollments(id_mahasiswa, id_course)`, `messages(id_sender, id_receiver)` | 1h | Performance |
| 14 | **Add pagination** to all listing endpoints (courses, notifications, messages) | 2h | Memory safety |
| 15 | **Create Laravel Policies** for Course, Enrollment, Material | 3h | Authorization correctness |
| 16 | **Write P0 tests** — auth, enrollment, authorization | 8h | Regression safety |
| 17 | **Delete dead code** — `AuthOld/` folder, Breeze test files | 30m | Code hygiene |
| 18 | **Move `tinker` and `scramble` to `require-dev`** | 5m | Attack surface reduction |
| 19 | **Generic auth error messages** — prevent user enumeration | 1h | Security |
| 20 | **Generate random default passwords** and email them | 2h | Security |

### P2 — IMPROVEMENTS (Scalability & DevOps)

| # | Action | Effort | Impact |
|---|---|---|---|
| 21 | **Add CI/CD pipeline** (GitHub Actions) | 4h | Deployment safety |
| 22 | **Queue OTP emails** instead of sending synchronously | 2h | Performance |
| 23 | **Add caching layer** for courses, jurusan, dashboard stats | 3h | Performance |
| 24 | **Add file upload validation** (MIME, size, content type) | 2h | Security |
| 25 | **Add security headers** (CSP, HSTS, X-Frame-Options) | 1h | Security |
| 26 | **Add CORS configuration** | 1h | Security |
| 27 | **Add request logging / APM** | 3h | Observability |
| 28 | **Add database backup strategy** | 2h | Disaster recovery |
| 29 | **Create production Dockerfile** | 3h | Deployment |
| 30 | **Add WebSocket for messaging** (Reverb/Pusher) | 8h | Real-time UX |

---

## L. Final System Risk Score

### Scoring Methodology

| Category | Weight | Score (0-100, lower is better) | Weighted |
|---|---|---|---|
| Security Posture | 30% | 78 (Very Poor) | 23.4 |
| Code Quality | 20% | 55 (Below Average) | 11.0 |
| Architecture | 15% | 50 (Below Average) | 7.5 |
| Test Coverage | 15% | 95 (Critical) | 14.25 |
| Performance | 10% | 60 (Below Average) | 6.0 |
| DevOps Maturity | 10% | 75 (Poor) | 7.5 |

### **FINAL SYSTEM RISK SCORE: 70 / 100** 🔴

**Interpretation:** HIGH RISK — The application has critical security vulnerabilities (hardcoded credentials, SQL injection, brute-forceable OTP), zero effective test coverage, god-class controllers, missing database constraints, and no CI/CD pipeline. It is **not production-ready** in its current state.

### Risk Summary

| Category | Status |
|---|---|
| **Credentials Exposed** | 🔴 CRITICAL — Rotate immediately |
| **SQL Injection** | 🔴 CRITICAL — Fix immediately |
| **OTP Brute-Force** | 🔴 CRITICAL — Add rate limiting |
| **Test Coverage** | 🔴 CRITICAL — 0% effective coverage |
| **God Controllers** | 🟡 HIGH — Refactor into services |
| **Token Expiration** | 🟡 HIGH — Enable in config |
| **Missing Indexes** | 🟡 HIGH — Performance at scale |
| **No CI/CD** | 🟡 HIGH — Add pipeline |
| **Debug Mode** | 🟡 HIGH — Disable in production |
| **Architecture** | 🟠 MEDIUM — Needs service layer |
| **Caching** | 🟠 MEDIUM — Add caching strategy |
| **Documentation** | 🟢 LOW — Scramble helps with API docs |

---

*Report generated using `.agent/` skill-based orchestration methodology.*  
*Skills applied: laravel-expert, laravel-security-audit, production-code-audit, architect-review, security-auditor, api-design-principles, database-architect, backend-architect, performance-engineer, php-pro, code-review-excellence, testing-patterns*
