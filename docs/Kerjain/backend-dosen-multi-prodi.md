# Backend Implementation: Multiple Program Studi for Dosen

## Overview
We have updated the frontend in \esources/views/Auth/admin/dosen.blade.php\ to allow selecting multiple *Program Studi* (jurusan) for a single Dosen using checkboxes.
The array of selected ID Jurusans is passed to the backend as \id_jurusan[]\.

Currently, the \profiles\ table only supports a single \id_jurusan\.
You will need to update the backend structure to allow a many-to-many or JSON attribute relationship.

## Required Tasks

### 1. Database Schema
Option A: Change \id_jurusan\ column in the \profiles\ table to be a JSON array.
Option B (Preferred for normalization): Create a pivot table \dosen_jurusan\ (e.g. \profile_id\, \jurusan_id\) and remove \id_jurusan\ from \profiles\.

### 2. Update \Profile.php\ Model
- Update the \$casts\ to cast \id_jurusan\ to array if using JSON approach, OR
- Add a new Eloquent relationship \public function jurusans()\ if using pivot approach.

### 3. Update \AdminController.php\
- **Validation**:
  In \storeDosen\ and \updateDosen\, validate \id_jurusan\ as an array.
  \'id_jurusan' => 'required|array',\
  \'id_jurusan.*' => 'exists:jurusans,id_jurusan',\
- **Insertion**:
  Save the \id_jurusan\ as a JSON string when creating/updating the profile, or save to Pivot table.
- **Reading Data (getDosen)**:
  Update \getDosen()\ so that \id_jurusan\ is returned as an array of IDs. Example: \'id_jurusan' => ->profile?->id_jurusan\ (if JSON casted).
- **Listing Data (showDosen)**:
  Update the eager loading in \showDosen\ and map the program studi text to return comma-separated \mengajar\ strings instead of just one \
ama_jurusan\. 
  Currently: \'program_studi' => ->profile?->jurusan?->nama_jurusan ?? '-'\
  Should be something like: \'program_studi' => collect(->profile?->id_jurusan)->map(fn() => Jurusan::find()->nama_jurusan)->implode(', ') ?? '-'\

## Notes
- Ensure old data is migrated or defaulted safely.
- The UI array expects to pre-select existing data properly on the edit modal via \data.id_jurusan\.
- The 'import dosen' logic may also need to be updated to map strings of comma-separated program studi matching into array format.

