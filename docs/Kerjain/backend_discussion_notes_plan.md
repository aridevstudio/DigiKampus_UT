# Backend Implementation Plan: Course Discussions & Notes

## Overview

This plan outlines the required backend changes to fully implement the "Diskusi" and "Catatan" features in the learning dashboard (`course-learn`).

## 1. Database Schema Changes

### A. create_course_discussions_table

Creates a table to store comments specifically linked to a course material/module.

```php
Schema::create('course_discussions', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('id_course');
    $table->unsignedBigInteger('id_material')->nullable(); // Optional, if comments are per specific video/document
    $table->unsignedBigInteger('id_mahasiswa')->nullable(); // Can be null if a Dosen replies
    $table->unsignedBigInteger('id_dosen')->nullable(); // Can be null if a Mahasiswa comments
    $table->text('comment');
    $table->unsignedBigInteger('parent_id')->nullable(); // For threaded replies
    $table->timestamps();

    $table->foreign('id_course')->references('id_course')->on('courses')->onDelete('cascade');
    $table->foreign('id_material')->references('id_material')->on('course_materials')->onDelete('cascade');
});
```

### B. create_course_notes_table

Creates a table to store private notes for students, or pinned notes left by the Dosen for a specific course module.

```php
Schema::create('course_notes', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('id_course');
    $table->unsignedBigInteger('id_material')->nullable();
    $table->unsignedBigInteger('id_user'); // ID of whoever owns the note
    $table->string('user_role'); // 'mahasiswa' or 'dosen' (To query correctly)
    $table->text('note_content');
    $table->boolean('is_public')->default(false); // If true, Dosen exposes this note to all Mahasiswa taking the course
    $table->timestamps();

    $table->foreign('id_course')->references('id_course')->on('courses')->onDelete('cascade');
});
```

## 2. Model Setup

- **CourseDiscussion**:
  - `belongsTo` Course, CourseMaterial, User (Mahasiswa/Dosen).
  - `hasMany` CourseDiscussion (replies) where `parent_id` matches.
- **CourseNote**:
  - `belongsTo` Course, CourseMaterial.

## 3. Controller Actions (CourseController)

### Fetching (for `course-learn` view)

In the `CourseController@learn` method, append logic to eager-load discussions and notes for the specific material/course.

```php
$discussions = CourseDiscussion::with(['mahasiswa.profile', 'dosen'])
    ->where('id_material', $currentMaterialId)
    ->whereNull('parent_id') // Get main threads
    ->latest()
    ->get();

// For Mahasiswa: Get their own private notes AND public notes from Dosen
$notes = CourseNote::where('id_material', $currentMaterialId)
    ->where(function($q) use ($user) {
        $q->where(function($subQ) use ($user) {
            $subQ->where('id_user', $user->id)->where('user_role', 'mahasiswa');
        })->orWhere('is_public', true); // Dosen's public pin
    })
    ->latest()
    ->get();
```

### Endpoints (web.php / mahasiswa.php & dosen.php)

#### Mahasiswa Endpoints:

- `POST /course/{course}/discussion/add`: Creates a new row in `course_discussions`.
- `POST /course/{course}/note/add`: Creates a new row in `course_notes` with `user_role = 'mahasiswa'` and `is_public = false`.

#### Dosen Endpoints:

- `POST /dosen/course/{course}/note/add`: Creates a new row in `course_notes` with `user_role = 'dosen'`. Adds a checkbox/flag to mark it as `is_public` to be visible to all students.
- `POST /dosen/course/{course}/discussion/reply`: Appends a comment to an existing discussion thread.

## 4. Frontend Integration

Once backend routes exist:

1. Wrap the static input boxes in `resources/views/pages/mahasiswa/course-learn.blade.php` inside native HTML `<form>` tags.
2. Target the newly created routes via `action="{{ route('...') }}"` and add `@csrf`.
3. Loop over `$discussions` and `$notes` in the view to replace the Alpine state tracking we currently mock in the frontend.
