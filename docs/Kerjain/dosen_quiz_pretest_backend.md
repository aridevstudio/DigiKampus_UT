# Backend Changes Needed: Dosen Quiz Pretest & Points

## Summary
The dosen `kelola-quiz.blade.php` frontend now sends `is_pretest`, `passing_score`, `acak_soal`, `tampilkan_nilai`, and per-question `penjelasan` fields. The backend needs to be updated to accept and store these.

## Current Architecture Problem
The dosen quiz system stores quiz data as a **JSON blob in `course_materials.konten`**, while the admin side uses the proper `Quiz` + `QuizQuestion` models with dedicated DB tables. This means the new fields are sent but not persisted properly.

## Option A: Quick Fix (Store in JSON)
Update `DosenContentApiController::addModule()` to include the new fields in the JSON blob stored in `course_materials.konten`.

**File:** `app/Http/Controllers/Auth/DosenContentApiController.php`  
**Method:** `addModule()` (around line 469)

The `form.konten` already contains the serialized questions JSON. The additional quiz-level fields (`is_pretest`, `passing_score`, `acak_soal`, `tampilkan_nilai`) need to be stored either:
- As separate columns on `course_materials` (requires migration), OR
- Wrapped into the JSON blob alongside questions

## Option B: Proper Fix (Use Quiz Model)
Migrate dosen quiz creation to use the same `Quiz` + `QuizQuestion` models that admin uses.

### Steps:
1. **Create new route** in `routes/dosen.php`:
   ```php
   Route::post('/api/courses/{courseId}/module/{moduleId}/quiz', [DosenContentApiController::class, 'createQuiz']);
   ```

2. **Add `createQuiz()` method** to `DosenContentApiController`:
   - Create `CourseModule` record
   - Create `Quiz` record with `is_pretest`, `passing_score`, `acak_soal`, `tampilkan_nilai`
   - Create `QuizQuestion` records for each question with `bobot` and `penjelasan`

3. **Reference:** See `AdminController` lines ~1164+ for the admin implementation of pretest handling.

## New Fields Frontend Sends
| Field | Type | Default | Where |
|-------|------|---------|-------|
| `form.is_pretest` | boolean | false | Quiz level |
| `form.passing_score` | number | 60 | Quiz level |
| `form.acak_soal` | boolean | false | Quiz level |
| `form.tampilkan_nilai` | boolean | true | Quiz level |
| `modalForm.penjelasan` | string | '' | Per question |
| `modalForm.bobot` | number | 10 | Per question (already existed) |

## Models Already Support This
- `Quiz` model has `is_pretest`, `passing_score`, `acak_soal`, `tampilkan_nilai` in fillable
- `QuizQuestion` model has `bobot`, `penjelasan` in fillable
- `quizzes` migration already has all these columns
- `quiz_questions` migration already has `bobot` and `penjelasan` columns
