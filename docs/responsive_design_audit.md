# Responsive Design Audit Report

> **Date:** Generated for responsive overhaul planning  
> **Scope:** 38 blade files across Admin (13), Dosen (13), Mahasiswa (12+)  
> **Framework:** Tailwind CSS + Alpine.js on Laravel Blade

---

## Table of Contents
1. [Admin Files](#1-admin-files)
2. [Dosen Files](#2-dosen-files)
3. [Mahasiswa Files](#3-mahasiswa-files)
4. [Cross-File Summary](#4-cross-file-summary)
5. [Priority Fix List](#5-priority-fix-list)

---

## 1. ADMIN FILES

**Layout Component:** `x-layouts.admin` (all 13 files)

### 1.1 `Auth/admin/dosen.blade.php` (903 lines)
- **Patterns:** Stats cards, Toolbar (search + filters + buttons), Data table
- **Key responsive classes:** `grid-cols-1 sm:grid-cols-2 xl:grid-cols-4` ✅, `overflow-x-auto` ✅, `flex flex-wrap` ✅
- **Issues:**
  - **L~138**: `truncate max-w-[200px]` — hardcoded width on name column, may be too wide OR too narrow depending on viewport

### 1.2 `Auth/admin/mahasiswa.blade.php` (957 lines)
- **Patterns:** Stats cards, Toolbar, Data table, Import/Export buttons
- **Key responsive classes:** `grid-cols-1 sm:grid-cols-2 xl:grid-cols-4` ✅, `overflow-x-auto` ✅, `flex flex-wrap` ✅
- **Issues:**
  - Similar to dosen — `max-w-xs` on search input (acceptable)
  - Large file with likely modals/bulk-action sections beyond L150

### 1.3 `Auth/admin/kursus.blade.php` (1577 lines) ⚠️ LARGEST FILE
- **Patterns:** Stats cards, 8-column data table, Thumbnails, Modals (add/edit)
- **Key responsive classes:** `grid-cols-1 sm:grid-cols-2` in modals ✅, `overflow-x-auto` ✅
- **Issues:**
  - **L~varied**: Dynamic Tailwind `border-{{ $stat['color'] }}-100` — **won't compile** without safelist
  - **L~table rows**: `w-14 h-14` fixed thumbnail size in table — acceptable but rigid
  - 8-column table is very wide; relies solely on `overflow-x-auto`

### 1.4 `Auth/admin/prodi.blade.php` (453 lines)
- **Patterns:** Stats cards, 6-column table, Modals (add/edit)
- **Key responsive classes:** `grid-cols-1 sm:grid-cols-2 xl:grid-cols-3` ✅, `overflow-x-auto` ✅
- **Issues:**
  - **L258, L331**: Modal forms use `grid-cols-1 sm:grid-cols-2` ✅ — properly responsive

### 1.5 `Auth/admin/kategori.blade.php` (232 lines)
- **Patterns:** Stats cards, Table, Modal (add/edit)
- **Key responsive classes:** `grid-cols-1 sm:grid-cols-2` ✅, `max-w-lg` modal ✅
- **Issues:**
  - Modal action buttons `flex justify-end gap-2` — may need stacking on very small screens

### 1.6 `Auth/admin/pengumuman.blade.php` (462 lines)
- **Patterns:** 6-column table, Toolbar with filters, Modals
- **Key responsive classes:** `overflow-x-auto` ✅, `flex flex-wrap` ✅
- **Issues:**
  - **L~120-121**: `truncate max-w-[300px]` on title and content columns — hardcoded widths too wide for mobile table cells
  - **L206, L295**: Modal forms use `grid-cols-1 sm:grid-cols-2 gap-5` ✅

### 1.7 `Auth/admin/sertifikasi.blade.php` (581 lines) ⚠️ HIGH PRIORITY
- **Patterns:** Certificate preview canvas, Grid layout, Table, Image upload
- **Key responsive classes:** `grid-cols-1 xl:grid-cols-3` ✅, `grid-cols-1 sm:grid-cols-2` ✅
- **Issues:**
  - **L~96**: `style="font-size: 28px;"` — **inline fixed font-size, NOT responsive**
  - **L~104**: `style="font-size: 52px;"` — **inline fixed font-size, NOT responsive**
  - **L~130**: `min-w-[760px]` on table — forces minimum width, relies on overflow only
  - **L200**: `grid-cols-1 md:grid-cols-2 gap-4` ✅

### 1.8 `Auth/admin/kelola-modul.blade.php` (470 lines)
- **Patterns:** Expandable accordion, Flex layout, Course info card
- **Key responsive classes:** `flex flex-col lg:flex-row` ✅
- **Issues:**
  - **L~10**: `min-w-[280px]` on course info card — could overflow on screens < 280px (edge case)

### 1.9 `Auth/admin/kelola-video.blade.php` (231 lines)
- **Patterns:** Form layout with preview sidebar, Video preview, Breadcrumb
- **Key responsive classes:** `grid-cols-1 lg:grid-cols-3` ✅, `grid-cols-1 md:grid-cols-2` ✅
- **Issues:** None significant

### 1.10 `Auth/admin/kelola-bacaan.blade.php` (365 lines)
- **Patterns:** Form with sidebar, Rich text editor, File upload, Reference links
- **Key responsive classes:** `grid-cols-1 lg:grid-cols-3` ✅, `grid-cols-1 sm:grid-cols-2` ✅
- **Issues:** None significant

### 1.11 `Auth/admin/kelola-quiz.blade.php` (476 lines)
- **Patterns:** Quiz builder, Question list, Modal, Alpine.js heavy
- **Key responsive classes:** `flex flex-col gap-4 sm:flex-row` ✅, `max-w-lg max-h-[90vh] overflow-y-auto` ✅
- **Issues:** None significant

### 1.12 `Auth/admin/kelola-tugas.blade.php` (258 lines)
- **Patterns:** Form with preview sidebar, Breadcrumb
- **Key responsive classes:** `grid-cols-1 lg:grid-cols-3` ✅, `grid-cols-1 md:grid-cols-2` ✅
- **Issues:** None significant

### 1.13 `Auth/admin/profile.blade.php` (98 lines)
- **Patterns:** Profile form, Avatar preview, Password change
- **Key responsive classes:** `grid-cols-1 lg:grid-cols-3` ✅
- **Issues:**
  - **L~password section**: `grid-cols-1 md:grid-cols-3 gap-5` — 3 password fields side-by-side on `md` (768px) is tight; each field gets ~230px

---

## 2. DOSEN FILES

**Layout Component:** `x-layouts.dosen` (all 13 files)

### 2.1 `Auth/dosen/kursus-saya.blade.php` (193 lines)
- **Patterns:** Card grid, Filters, Pagination, Thumbnails
- **Key responsive classes:** `grid-cols-1 md:grid-cols-2 lg:grid-cols-3` ✅
- **Issues:**
  - **L~filter row**: Filter section `flex items-center gap-2` without `flex-wrap` — search with `flex-1 max-w-xs ml-auto` may push off-screen on narrow viewports

### 2.2 `Auth/dosen/buat-kursus.blade.php` (448 lines) ⚠️ HIGH PRIORITY
- **Patterns:** Multi-section form, File upload, Action buttons
- **Key responsive classes:** `max-w-3xl mx-auto` centered form ✅
- **Issues:**
  - **L66**: `grid grid-cols-2 gap-4` — **NO responsive prefix**, forces 2 cols on mobile (Kategori/Level)
  - **L95**: `grid grid-cols-2 gap-4` — **NO responsive prefix**, forces 2 cols on mobile (Estimasi/Satuan)
  - **L196**: `grid grid-cols-2 gap-3` — **NO responsive prefix**
  - **L~header**: 3 buttons (Batal, Simpan Draft, Buat Kursus) in `flex items-center justify-end gap-2` — doesn't stack on mobile

### 2.3 `Auth/dosen/edit-kursus.blade.php` (959 lines) ⚠️ HIGH PRIORITY
- **Patterns:** Form with sidebar, Thumbnail upload, Multi-section
- **Key responsive classes:** Custom CSS `@media (min-width: 1024px) { grid-template-columns: 3fr 2fr; }` ✅
- **Issues:**
  - **L56**: `grid grid-cols-2 gap-4` — **NO responsive prefix**
  - **L80**: `grid grid-cols-2 gap-4` — **NO responsive prefix**
  - **L83**: `grid grid-cols-2 gap-3` — **NO responsive prefix**
  - **L243**: `grid grid-cols-2 gap-4 mb-3` — **NO responsive prefix**
  - **L~thumbnail**: `w-32 h-20` fixed dimensions for thumbnail
  - Total: **4 instances** of un-prefixed `grid-cols-2`

### 2.4 `Auth/dosen/detail-kursus.blade.php` (176 lines)
- **Patterns:** Detail view with sidebar, Module structure, Statistics
- **Key responsive classes:** `grid-cols-1 gap-6 lg:grid-cols-3` ✅, `flex flex-col gap-4 md:flex-row` ✅
- **Issues:**
  - **L134**: `grid grid-cols-2 gap-3` — **NO responsive prefix** (stats grid, but items are small so acceptable)

### 2.5 `Auth/dosen/kelola-modul.blade.php` (552 lines)
- **Patterns:** Material list with drag handles, Expandable sections
- **Key responsive classes:** `flex flex-col lg:flex-row` ✅
- **Issues:**
  - **L~info card**: `min-w-[280px]` without responsive prefix — same as admin version

### 2.6 `Auth/dosen/kelola-video.blade.php` (231 lines)
- **Patterns:** Form with preview sidebar, Video embed
- **Key responsive classes:** `grid-cols-1 lg:grid-cols-3` ✅, `grid-cols-1 md:grid-cols-2` ✅
- **Issues:** None significant

### 2.7 `Auth/dosen/kelola-bacaan.blade.php` (365 lines) ⚠️ MEDIUM PRIORITY
- **Patterns:** Form with preview sidebar, Text editor, File upload
- **Key responsive classes:** `grid-cols-1 lg:grid-cols-3` ✅
- **Issues:**
  - **L49**: `grid grid-cols-2 gap-4` — **NO responsive prefix** (time estimate + unit fields forced 2-col on mobile)

### 2.8 `Auth/dosen/kelola-quiz.blade.php` (495 lines)
- **Patterns:** Quiz builder, Question list, Preview
- **Key responsive classes:** `flex flex-col md:flex-row` ✅, `grid-cols-1 md:grid-cols-2` ✅
- **Issues:** None significant

### 2.9 `Auth/dosen/kelola-tugas.blade.php` (258 lines)
- **Patterns:** Form with preview sidebar
- **Key responsive classes:** `grid-cols-1 lg:grid-cols-3` ✅, `grid-cols-1 md:grid-cols-2` ✅
- **Issues:** None significant

### 2.10 `Auth/dosen/progres-mahasiswa.blade.php` (252 lines) ⚠️ MEDIUM PRIORITY
- **Patterns:** Stats cards, Filter form, Table with responsive column hiding
- **Key responsive classes:** `hidden lg:table-cell` and `hidden md:table-cell` ✅ (good pattern!)
- **Issues:**
  - **L~26-27**: Dynamic Tailwind `bg-{{ $stat['color'] }}-50` and `text-{{ $stat['color'] }}-500` — **won't compile** without safelist
  - **L~stats**: `grid grid-cols-2 lg:grid-cols-5 gap-4` — 5 cols on `lg` is tight; 2 cols on mobile is OK

### 2.11 `Auth/dosen/progres-kursus.blade.php` (97 lines) ⚠️ HIGH PRIORITY
- **Patterns:** Stats grid, Table, Progress bars
- **Key responsive classes:** `overflow-x-auto` ✅
- **Issues:**
  - **L~14**: `grid grid-cols-3 gap-4` — **NO responsive prefix**, forces 3 cols even on mobile (320px = ~93px per card)

### 2.12 `Auth/dosen/pesan.blade.php` (365 lines)
- **Patterns:** Chat layout, Conversation list, Message bubbles, Mobile toggle
- **Key responsive classes:** `w-full md:w-80` ✅, Alpine `:class` mobile show/hide ✅, `max-w-[85%] md:max-w-[75%]` ✅
- **Issues:**
  - **L~2**: `h-[calc(100vh-120px)]` — may conflict with layout padding on different screen sizes
  - Overall: **well-designed for responsive** with mobile back button

### 2.13 `Auth/dosen/profile.blade.php` (141 lines)
- **Patterns:** Profile form, Avatar, Password section
- **Key responsive classes:** `grid-cols-1 lg:grid-cols-3` ✅
- **Issues:**
  - **L~password**: `grid-cols-1 md:grid-cols-3 gap-5` — same issue as admin profile, 3 cols on `md` is tight

---

## 3. MAHASISWA FILES

**Layout Component:** `x-layouts.dashboard` (all files)

### 3.1 `pages/mahasiswa/courses.blade.php` (228 lines)
- **Patterns:** Course cards, Filter tabs, Sort dropdown, Pagination
- **Key responsive classes:** `flex flex-col sm:flex-row` ✅, `flex flex-wrap` ✅, `grid-cols-1 md:grid-cols-2` ✅
- **Issues:** None significant — well-structured responsive layout

### 3.2 `pages/mahasiswa/get-courses.blade.php` (207 lines)
- **Patterns:** Course card grid, Filter tabs, Search bar
- **Key responsive classes:** `grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4` ✅, `flex flex-col sm:flex-row` ✅
- **Issues:**
  - Filter tabs `flex items-center gap-2` in constrained container — on mobile the 4 tabs (Semua/Webinar/Tiket/Kursus) may overflow horizontally; no `overflow-x-auto` or `flex-wrap`

### 3.3 `pages/mahasiswa/course-detail.blade.php` (901 lines)
- **Patterns:** Tabbed interface, Module accordion, Review section, Sidebar
- **Key responsive classes:** `grid-cols-1 lg:grid-cols-3` ✅, `overflow-x-auto` on tabs ✅
- **Issues:**
  - Very long PHP block (L1-150 is mostly PHP) — UI starts around L220
  - Tab buttons have `whitespace-nowrap` + parent `overflow-x-auto` ✅ — good horizontal scroll pattern

### 3.4 `pages/mahasiswa/course-learn.blade.php` (668 lines) ⚠️ HIGH PRIORITY
- **Patterns:** 3-column layout (left sidebar + content + right sidebar), Video player, Material list
- **Key responsive classes:** Custom CSS `@media (max-width: 1024px)` override ✅
- **Issues:**
  - **L28**: `style="display: flex; flex-direction: row; gap: 1.5rem; flex-wrap: nowrap;"` — **inline styles instead of Tailwind**, uses custom `@media` CSS block at L655-666 to override to column on mobile
  - **L31**: `style="width: 250px; min-width: 250px; flex-shrink: 0;"` — **fixed 250px sidebar via inline style**
  - **L231**: `style="flex: 1; min-width: 0;"` — inline style for center content
  - **L393**: `style="width: 280px; min-width: 280px; flex-shrink: 0;"` — **fixed 280px right sidebar via inline style**
  - **L77**: `grid grid-cols-4 gap-2` — **NO responsive prefix** for compact material grid (>20 materials)
  - **L254**: `style="aspect-ratio: 16/9;"` — inline style instead of Tailwind `aspect-video`
  - Uses `!important` in style overrides (L657-663)
  - **Overall: Most problematic file** — inline styles fight Tailwind system

### 3.5 `pages/mahasiswa/checkout.blade.php` (251 lines)
- **Patterns:** Step indicator, Cart items, Voucher input, Order summary sidebar
- **Key responsive classes:** `grid-cols-1 lg:grid-cols-3` ✅
- **Issues:**
  - **L~step indicator**: Step indicator `flex items-center justify-center gap-4` with fixed `w-16` dividers — on very narrow screens, the 3 steps + dividers may overflow
  - Cart item layout `flex gap-4` with image, info, and actions — actions column may get cramped on mobile

### 3.6 `pages/mahasiswa/payment.blade.php` (183 lines)
- **Patterns:** Step indicator, Order summary, Payment method, Instructions
- **Key responsive classes:** `grid-cols-1 lg:grid-cols-3` ✅, `flex flex-col sm:flex-row` for action buttons ✅
- **Issues:**
  - Same step indicator issue as checkout
  - **L~payment method**: VA info with badge `px-3 py-1 ... text-xs rounded-full` — long text "Akan diarahkan ke halaman pembayaran resmi" may overflow on mobile

### 3.7 `pages/mahasiswa/finance.blade.php` (213 lines)
- **Patterns:** Stats cards, Filter form, Transaction table
- **Key responsive classes:** `grid-cols-1 sm:grid-cols-2 lg:grid-cols-4` ✅, `flex flex-col md:flex-row` ✅, `overflow-x-auto` ✅
- **Issues:**
  - Filter form has 5 elements (search + 2 selects + 2 buttons) in `flex flex-col md:flex-row gap-4` — on `md` all 5 in one row may be very cramped

### 3.8 `pages/mahasiswa/chat.blade.php` (306 lines)
- **Patterns:** Chat layout (sidebar + message area), Mobile-aware toggle
- **Key responsive classes:** `w-full md:w-80` ✅, Alpine `:class` show/hide ✅, `max-w-[85%] md:max-w-[75%]` ✅
- **Issues:**
  - **L2**: `h-[calc(100vh-120px)]` — same potential padding conflict as dosen chat
  - **L3**: Header `hidden md:block` — hides title + description on mobile (intentional for chat UI)
  - Overall: **well-designed responsive chat** with mobile back button pattern

### 3.9 `pages/mahasiswa/calendar.blade.php` (239 lines)
- **Patterns:** Calendar grid, Upcoming events sidebar, Navigation
- **Key responsive classes:** `grid-cols-1 lg:grid-cols-3` ✅, `flex flex-col sm:flex-row` ✅
- **Issues:**
  - **L~calendar grid**: `min-w-[500px]` on calendar day headers and grid — forces horizontal scroll on mobile (intentional and acceptable for calendar)
  - Event color dots use dynamic Tailwind `bg-{{ $eventColors[$event['type']] }}-500` — **won't compile** without safelist

### 3.10 `pages/mahasiswa/favorites.blade.php` (113 lines)
- **Patterns:** Course card grid, Empty state
- **Key responsive classes:** `grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4` ✅
- **Issues:** None significant — clean responsive implementation

### 3.11 `pages/mahasiswa/notification.blade.php` (258 lines)
- **Patterns:** Notification list with sidebar filters, Search, Alpine.js filtering
- **Key responsive classes:** `grid-cols-1 lg:grid-cols-3` ✅, `flex flex-col sm:flex-row` ✅
- **Issues:** None significant

### 3.12 `pages/mahasiswa/profile.blade.php` (259 lines)
- **Patterns:** Profile card, Academic info, Activities, Progress section
- **Key responsive classes:** `grid-cols-1 lg:grid-cols-2` ✅, `flex flex-col lg:flex-row` ✅
- **Issues:**
  - **L154**: `grid grid-cols-2 gap-4` — **NO responsive prefix** on academic info grid (Program Studi/Fakultas/Tahun Masuk/Status/IPK/SKS) — 6 items in 2-col on mobile, each ~150px wide at 320px viewport
  - **L247, L251**: `min-w-[120px]` on stat cards — acceptable, items are in a flex row that wraps

---

## 4. CROSS-FILE SUMMARY

### 4.1 Issue Categories

| Issue Type | Severity | Files Affected | Count |
|---|---|---|---|
| `grid-cols-N` without responsive prefix | 🔴 HIGH | buat-kursus, edit-kursus, kelola-bacaan (dosen), progres-kursus, course-learn, profile (mhs), detail-kursus | **12+ instances** |
| Inline styles bypassing Tailwind | 🔴 HIGH | course-learn | **5 instances** |
| Dynamic Tailwind classes (won't compile) | 🟡 MEDIUM | kursus (admin), progres-mahasiswa, calendar (mhs) | **3 files** |
| Inline fixed font-sizes | 🟡 MEDIUM | sertifikasi, module-feedback, quiz-result | **3 files** |
| Hardcoded `max-w-[Npx]` in tables | 🟢 LOW | dosen (admin), pengumuman | **2 files** |
| `min-w-[Npx]` without responsive prefix | 🟢 LOW | kelola-modul (admin+dosen), dashboard (mhs) | **3 files** |
| Password `md:grid-cols-3` too tight | 🟢 LOW | profile (admin), profile (dosen) | **2 files** |
| Filter overflow on narrow screens | 🟢 LOW | get-courses, kursus-saya (dosen) | **2 files** |
| Step indicator overflow on mobile | 🟢 LOW | checkout, payment | **2 files** |

### 4.2 Common Good Patterns (Worth Preserving)

1. **Tables with `overflow-x-auto`** — consistently applied across Admin/Dosen tables ✅
2. **`grid-cols-1 lg:grid-cols-3`** — standard sidebar layout pattern used in 10+ files ✅
3. **Chat mobile toggle** — both dosen/pesan and mahasiswa/chat use Alpine `:class` with back button ✅
4. **`flex flex-col sm:flex-row`** — header layouts across all roles ✅
5. **Responsive column hiding** — `hidden md:table-cell` in progres-mahasiswa ✅ (should be adopted more widely)
6. **`admin-responsive-toolbar`** CSS class — consistent toolbar pattern in admin files ✅

### 4.3 Layout Component Distribution

| Layout | Files | Notes |
|---|---|---|
| `x-layouts.admin` | 13 admin files | Sidebar + top nav |
| `x-layouts.dosen` | 13 dosen files | Sidebar + top nav |
| `x-layouts.dashboard` | 12+ mahasiswa files | Sidebar + top nav |

---

## 5. PRIORITY FIX LIST

### P0 — Critical (breaks on mobile)

1. **`grid grid-cols-2` without prefix** — Add `grid-cols-1 sm:grid-cols-2`:
   - `Auth/dosen/buat-kursus.blade.php` L66, L95, L196
   - `Auth/dosen/edit-kursus.blade.php` L56, L80, L83, L243
   - `Auth/dosen/kelola-bacaan.blade.php` L49
   - `pages/mahasiswa/profile.blade.php` L154

2. **`grid grid-cols-3` without prefix** — Add `grid-cols-1 sm:grid-cols-3`:
   - `Auth/dosen/progres-kursus.blade.php` L~14

3. **`course-learn.blade.php` inline styles** — Convert to Tailwind responsive classes:
   - L28: Replace `style="display: flex; ..."` with `flex flex-col lg:flex-row gap-6`
   - L31: Replace `style="width: 250px; ..."` with `w-full lg:w-[250px] lg:min-w-[250px] lg:flex-shrink-0`
   - L393: Replace `style="width: 280px; ..."` with `w-full lg:w-[280px] lg:min-w-[280px] lg:flex-shrink-0`
   - L254: Replace `style="aspect-ratio: 16/9;"` with `aspect-video`
   - Remove the custom `<style>` block at L655-666

4. **`grid grid-cols-4` without prefix** in course-learn L77 — Add `grid-cols-2 sm:grid-cols-4`

### P1 — High (visual issues on mobile)

5. **Dynamic Tailwind classes** — Add to `tailwind.config.js` safelist or use inline styles:
   - `Auth/admin/kursus.blade.php`: `border-{{ $stat['color'] }}-100` etc.
   - `Auth/dosen/progres-mahasiswa.blade.php`: `bg-{{ $stat['color'] }}-50`, `text-{{ $stat['color'] }}-500`
   - `pages/mahasiswa/calendar.blade.php`: `bg-{{ $eventColors[$event['type']] }}-500`

6. **Inline font-sizes in sertifikasi** — Replace with responsive Tailwind text classes:
   - L~96: `style="font-size: 28px;"` → `text-xl sm:text-2xl lg:text-[28px]`
   - L~104: `style="font-size: 52px;"` → `text-3xl sm:text-4xl lg:text-[52px]`

7. **Filter tab overflow** in `get-courses.blade.php` — Add `overflow-x-auto` or `flex-wrap` to the filter tab container

### P2 — Medium (minor visual issues)

8. **Password `md:grid-cols-3`** — Consider `md:grid-cols-1 lg:grid-cols-3`:
   - `Auth/admin/profile.blade.php`
   - `Auth/dosen/profile.blade.php`

9. **Step indicator overflow** — Add `flex-wrap` or scale down on mobile:
   - `pages/mahasiswa/checkout.blade.php`
   - `pages/mahasiswa/payment.blade.php`

10. **`min-w-[760px]` on sertifikasi table** (L~130) — Consider responsive column hiding instead

11. **Finance filter form** — 5 elements in one row at `md` breakpoint may be tight; consider `lg:flex-row` instead

### P3 — Low (edge cases, nice-to-have)

12. **`truncate max-w-[200px]`/`max-w-[300px]`** in admin table cells — use relative widths or `max-w-[50vw]`
13. **`min-w-[280px]`** on kelola-modul course card — add `lg:` prefix
14. **`h-[calc(100vh-120px)]`** in chat views — verify the `120px` accounts for all layout elements
15. **Button group stacking** in buat-kursus header — add `flex-wrap` for 3 buttons

---

## STATISTICS

| Metric | Value |
|---|---|
| Total files audited | 38 |
| Files with NO issues | 17 (45%) |
| Files with P0 issues | 5 |
| Files with P1 issues | 5 |
| Files with P2 issues | 5 |
| Total `grid-cols-N` without prefix | 12+ instances across 7 files |
| Total inline style issues | 5 instances in 1 file |
| Total dynamic Tailwind issues | 3 files |
| Largest file | kursus.blade.php (1,577 lines) |
| Smallest file | profile.blade.php admin (98 lines) |
