<?php
/**
 * Idempotent surgical patches to CourseController.php.
 * Fixes the bootcamp live-class dashboard so per-session
 * attendance proof uploads work reliably server-side.
 *
 * Targets (all verified pre-existing text via prior code_searcher output):
 *
 * 1) The $liveClasses[] push for 'Sesi Utama: ' . $course->nama_course
 *    around lines ~3080–3112 (in the `if ($course->tanggal_webinar)` block).
 *    Inject keys: session_key, ended_at, is_ended, can_join_now.
 *
 * 2) The $liveClasses[] push inside the `foreach ($courseModules ...)`
 *    loop for 'Live Q&A' (around lines ~3122–3131).
 *    Inject keys: session_key, ended_at, is_ended, can_join_now.
 *
 * 3) Inside prepareBootcampDashboardData (declared ~line 2864),
 *    add an attendance pre-fetch that returns a keyed map of
 *    session_key => attendance row, and inject the key
 *    'liveClassAttendances' into the function's return array
 *    (sits at the end of the function).
 *
 * Safe to run multiple times — guarded by marker checks.
 */
declare(strict_types=1);

$path = __DIR__ . '/../app/Http/Controllers/Mahasiswa/CourseController.php';
if (!is_file($path)) {
    $path = 'app/Http/Controllers/Mahasiswa/CourseController.php';
}
$orig = file_get_contents($path);
if ($orig === false) {
    fwrite(STDERR, "ERR: cannot read $path\n");
    exit(1);
}

// GUARD: if our marker is already present, skip work (idempotency).
if (strpos($orig, '/* LIVE_CLASS_ATTENDANCE_PATCH_v1 */') !== false) {
    echo "SKIP: patch already applied.\n";
    exit(0);
}

$patched = $orig;
$report = [];

/* ------------------------------------------------------------------
 * PATCH 1 — Sesi Utama: $liveClasses[] push
 *
 * Find the block:
 *     $liveClasses[] = [
 *         'title' => 'Sesi Utama: ' . $course->nama_course,
 *         'mentor' => $course->dosen?->name ?: 'Mentor Utama',
 *         'start_time' => $startTime,
 *         'end_time'   => $endTime,
 *         'link'       => 'https://zoom.us/j/9998887771',
 *         'is_active'  => $isActive,
 *         'countdown'  => now()->lessThan($startTime) ? now()->diffForHumans($startTime, true) : 'Kelas Dimulai',
 *         'recording_url' => $recordingUrl,
 *     ];
 *
 * Add: session_key='primary', ended_at, is_ended, can_join_now.
 * ------------------------------------------------------------------ */
$pattern1 = '/(\$liveClasses\[\]\s*=\s*\[\s*\n\s*\'title\'\s*=>\s*\'Sesi Utama:\s*\'\s*\.\s*\$course->nama_course,\s*\n\s*\'mentor\'\s*=>\s*\$course->dosen\?->name\s*\?:\s*\'Mentor Utama\',\s*\n\s*\'start_time\'\s*=>\s*\$startTime,\s*\n)(\s*\'end_time\'\s*=>\s*\$endTime,)/';
$inject1 = "\$1\n                /* LIVE_CLASS_ATTENDANCE_PATCH_v1 */\n                'session_key' => 'primary',\n                'ended_at' => \$endTime,\n                'is_ended' => now()->greaterThan(\$endTime),\n                'can_join_now' => now()->between(\$startTime, \$endTime),\n\$2";

$tmp = preg_replace($pattern1, $inject1, $patched, 1, $count1);
if ($tmp === null) {
    fwrite(STDERR, "ERR: PATCH 1 failed (preg null). Pattern:\n$pattern1\n");
    exit(2);
}
if ($count1 !== 1) {
    // Try a slightly looser variant
    $pattern1b = '/(\$liveClasses\[\]\s*=\s*\[\s*\n\s*\'title\'\s*=>\s*\'Sesi Utama:\s*\'\s*\.\s*\$course->nama_course,.*?\n)(\s*\'end_time\'\s*=>\s*\$endTime,)/s';
    $tmp = preg_replace($pattern1b, $inject1, $patched, 1, $count1);
    if ($tmp === null || $count1 !== 1) {
        fwrite(STDERR, "WARN: PATCH 1 only matched $count1 times — skipping live-class enrichment of Sesi Utama.\n");
    } else {
        $patched = $tmp;
        $report['patch1'] = $count1;
        echo "INFO: PATCH 1 (Sesi Utama) — applied via fallback pattern ($count1 match)\n";
    }
} else {
    $patched = $tmp;
    $report['patch1'] = $count1;
    echo "OK: PATCH 1 (Sesi Utama) — $count1 match\n";
}

/* ------------------------------------------------------------------
 * PATCH 2 — Live Q&A: $liveClasses[] push (foreach modules)
 *
 * Find the block:
 *     $liveClasses[] = [
 *         'title' => 'Live Q&A: ' . ($module->judul_module ?: 'Modul ' . ($index + 1)),
 *         'mentor' => $course->dosen?->name ?: 'Mentor Utama',
 *         'start_time' => $startTime,
 *         'end_time' => $endTime,
 *         'link' => 'https://meet.google.com/abc-defg-hij',
 *         ...
 *     ];
 *
 * Add: session_key, ended_at, is_ended, can_join_now.
 * The session_key encodes the per-module offset so it matches
 * collectLiveClassSessionKeys(): 'qa_<moduleId>_<start_iso>'.
 * ------------------------------------------------------------------ */
$pattern2 = '/(\$liveClasses\[\]\s*=\s*\[\s*\n\s*\'title\'\s*=>\s*\'Live Q&A:\s*\'\s*\.\s*\(\$module->judul_module\s*\?\:\s*\'Modul\s*\'\s*\.\s*\(\$index\s*\+\s*1\)\),\s*\n\s*\'mentor\'\s*=>\s*\$course->dosen\?->name\s*\?:\s*\'Mentor Utama\',\s*\n\s*\'start_time\'\s*=>\s*\$startTime,\s*\n)(\s*\'end_time\'\s*=>\s*\$endTime,)/s';
$inject2 = "\$1\n                'session_key' => 'qa_' . (int) \$module->id_module . '_' . \$startTime->format('Y-m-d\\TH:i'),\n                'ended_at' => \$endTime,\n                'is_ended' => now()->greaterThan(\$endTime),\n                'can_join_now' => now()->between(\$startTime, \$endTime),\n\$2";

$count2 = 0;
$tmp = preg_replace($pattern2, $inject2, $patched, 1, $count2);
if ($tmp !== null && $count2 === 1) {
    $patched = $tmp;
    $report['patch2'] = $count2;
    echo "OK: PATCH 2 (Live Q&A) — $count2 match\n";
} else {
    fwrite(STDERR, "WARN: PATCH 2 (Live Q&A) — only matched $count2 times, skipping.\n");
}

/* ------------------------------------------------------------------
 * PATCH 3 — prepareBootcampDashboardData return array
 *
 * We append a keyed attendance fetch right before the final
 * outer 'return [' of prepareBootcampDashboardData and
 * inject 'liveClassAttendances' => $liveClassAttendances as the
 * last tuple of that return.
 *
 * The function is private and lives right after
 * `collectLiveClassSessionKeys(Course $course): array`.
 *
 * Approach: find the LAST occurrence of the literal
 *     "'certificateChecklist' => \$certificateChecklist,"
 * (which is the second-to-last item in the return array) and
 *     (a) before the surrounding `return [`, prepend the attendance fetch.
 *     (b) after `'certificateChecklist' => $certificateChecklist,`,
 *         insert the new key.
 *
 * If the "certificateChecklist" anchor is not found, fall back to
 * matching `eligible' => $certificateEligible,\n            'issuedCertificate' => $issuedCertificate,`
 * which is the last known key.
 *
 * Either way, mark with the comment `/* LIVE_CLASS_ATTENDANCE_PATCH_v1 *\/`
 * to make idempotency robust.
 * ------------------------------------------------------------------ */

$anchorFound = false;
if (strpos($patched, "'certificateChecklist' => \$certificateChecklist,") !== false) {
    // Inject after certificateChecklist
    $patched = str_replace(
        "'certificateChecklist' => \$certificateChecklist,",
        "'certificateChecklist' => \$certificateChecklist,\n            'liveClassAttendances' => \$liveClassAttendances,",
        $patched,
        $cnt3a
    );
    if ($cnt3a > 0) {
        $anchorFound = true;
        $report['patch3a'] = $cnt3a;
        echo "OK: PATCH 3a (return key) — $cnt3a replacement\n";
    }
}

if (!$anchorFound && strpos($patched, "'issuedCertificate' => \$issuedCertificate,") !== false) {
    $patched = str_replace(
        "'issuedCertificate' => \$issuedCertificate,",
        "'issuedCertificate' => \$issuedCertificate,\n            'liveClassAttendances' => \$liveClassAttendances,",
        $patched,
        $cnt3b
    );
    if ($cnt3b > 0) {
        $anchorFound = true;
        $report['patch3b'] = $cnt3b;
        echo "OK: PATCH 3b (return key fallback) — $cnt3b replacement\n";
    }
}

if (!$anchorFound) {
    fwrite(STDERR, "WARN: PATCH 3 anchors not found — skipping liveClassAttendances return.\n");
}

/* ------------------------------------------------------------------
 * PATCH 3-pre — attendance pre-fetch in prepareBootcampDashboardData
 *
 * We need a $liveClassAttendances variable to be computed before
 * the return statement. We locate the first `'certificateChecklist' => $certificateChecklist,`
 * INSIDE the function and prepend a small COmputation block right above it.
 *
 * Algorithm:
 *   1) Find the start of prepareBootcampDashboardData (anchor: unique string)
 *   2) Find first occurrence of the certificateChecklist key within
 *      the window from that start
 *   3) Insert the liveClassAttendances pre-fetch ASSIGNMENT just above.
 *
 * If the signature is already in place, skip (idempotent).
 * ------------------------------------------------------------------ */
$funcSig = 'private function prepareBootcampDashboardData(';
$procVar = '/* LIVE_CLASS_ATTENDANCE_PATCH_v1 */ $liveClassAttendances = BootcampLiveClassAttendance::where(\'id_user\', $user->id)
            ->where(\'id_course\', $course->id_course)
            ->get()
            ->keyBy(\'session_key\')
            ->map(fn ($att) => [
                \'id\' => $att->id_bootcamp_live_class_attendance ?? null,
                \'status\' => $att->status,
                \'proof_file\' => $att->proof_file,
                \'catatan_mahasiswa\' => $att->catatan_mahasiswa,
                \'catatan_reviewer\' => $att->catatan_reviewer,
                \'reviewed_at\' => optional($att->reviewed_at)->toIso8601String(),
            ])
            ->all();';

if (strpos($patched, $procVar) === false) {
    $fnStart = strpos($patched, $funcSig);
    if ($fnStart !== false) {
        $keyNeedle = "'certificateChecklist' => \$certificateChecklist,";
        $keyPos = strpos($patched, $keyNeedle, $fnStart);
        if ($keyPos !== false) {
            // Walk back to the start of this logical line.
            $lineStart = strrpos(substr($patched, 0, $keyPos), "\n");
            $insertPoint = $lineStart !== false ? $lineStart + 1 : $keyPos;
            $insertion = "\n        " . $procVar . "\n        ";
            $patched = substr($patched, 0, $insertPoint) . $insertion . substr($patched, $insertPoint);
            $report['patch3pre'] = 1;
            echo "OK: PATCH 3-pre (pre-fetch) — inserted above first certificateChecklist in function.\n";
        } else {
            // Fallback: try issuedCertificate
            $keyPos = strpos($patched, "'issuedCertificate' => \$issuedCertificate,", $fnStart);
            if ($keyPos !== false) {
                $lineStart = strrpos(substr($patched, 0, $keyPos), "\n");
                $insertPoint = $lineStart !== false ? $lineStart + 1 : $keyPos;
                $insertion = "\n        " . $procVar . "\n        ";
                $patched = substr($patched, 0, $insertPoint) . $insertion . substr($patched, $insertPoint);
                $report['patch3pre'] = 1;
                echo "OK: PATCH 3-pre fallback (issuedCertificate anchor)\n";
            } else {
                fwrite(STDERR, "WARN: PATCH 3-pre anchor not found in prepareBootcampDashboardData.\n");
            }
        }
    } else {
        fwrite(STDERR, "WARN: prepareBootcampDashboardData not found in file.\n");
    }
}

/* ------------------------------------------------------------------
 * WRITE
 * ------------------------------------------------------------------ */
if ($patched === $orig) {
    echo "NO CHANGE: every patch either failed or was already applied.\n";
} else {
    $bytes = file_put_contents($path, $patched);
    if ($bytes === false) {
        fwrite(STDERR, "ERR: cannot write back $path\n");
        exit(3);
    }
    echo "WROTE $bytes bytes to $path\n";
}

echo json_encode($report, JSON_PRETTY_PRINT) . "\n";
echo "DONE.\n";
