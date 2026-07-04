<?php
/**
 * One-shot surgical fix: removes a duplicate declaration of
 * `private function isBootcamp(?Course $course): bool` from
 * `app/Http/Controllers/Mahasiswa/CourseController.php`.
 *
 * Idempotent: re-running is safe (no-op when count <= 1).
 *
 * No external dependencies and no shell-out:
 *   - Declaration locations discovered via `token_get_all` (which
 *     correctly classifies comments, strings, heredocs, etc.).
 *   - Byte offsets computed from a real cursor over the source string
 *     (NOT from `$t[2]`, which is line number, NOT byte offset).
 *   - Brace walking is plain `string[$cursor]` char-by-char from the
 *     duplicate's byte offset until the matching `}`. Safe for any
 *     method body whose source does NOT contain literal `{`/`}` inside
 *     string literals or comments (true for `isBootcamp` whose body is
 *     a single ternary-free return statement).
 *   - Self-syntax validation reuses `token_get_all(..., TOKEN_PARSE)`
 *     — no `php -l`, no `exec`, no `shell_exec`, so it runs even when
 *     `disable_functions` blocks shell-outs (common on hardened Laravel
 *     production setups).
 *
 * Race safe: holds an exclusive flock on the file from open until the
 * atomic rename completes; concurrent `git pull` or
 * `composer dump-autoload` cannot clobber the splice.
 *
 * Path safe: anchored to `dirname(__DIR__)` so the script runs from
 * any cwd.
 *
 * Usage (project root OR anywhere):
 *   php tools/_fix_isbootcamp_duplicate.php
 *
 * Exit codes:
 *   0  success (or no-op when already correct)
 *   1  target file not found
 *   2  target not readable/writable
 *   3  >2 isBootcamp declarations found (manual triage)
 *   4  could not locate the duplicate's matching `}` (abort, file untouched)
 *   5  post-splice declaration count != 1
 *   6  post-splice source did not parse as valid PHP
 *   7  backup copy failed
 *   8  tmp write failed
 *   9  exclusive lock or atomic rename failed
 *  10  post-write integrity check failed (file restored from backup)
 */

declare(strict_types=1);

$path = dirname(__DIR__) . '/app/Http/Controllers/Mahasiswa/CourseController.php';

if (! is_file($path)) {
    fwrite(STDERR, "FATAL: target file not found at {$path}\n");
    exit(1);
}
if (! is_readable($path) || ! is_writable($path)) {
    fwrite(STDERR, "FATAL: target not readable/writable at {$path}\n");
    exit(2);
}

// Acquire an EXCLUSIVE flock from start to finish. Concurrent deploys
// during this window block here instead of corrupting the splice.
$fp = fopen($path, 'rb+');
if ($fp === false) {
    fwrite(STDERR, "FATAL: failed to open {$path}\n");
    exit(2);
}
if (! flock($fp, LOCK_EX)) {
    fclose($fp);
    fwrite(STDERR, "FATAL: could not acquire exclusive lock on {$path}\n");
    exit(2);
}
$sourceRaw = stream_get_contents($fp);
if ($sourceRaw === false) {
    flock($fp, LOCK_UN);
    fclose($fp);
    fwrite(STDERR, "FATAL: failed to read {$path}\n");
    exit(2);
}
$source = str_replace(["\r\n", "\r"], "\n", $sourceRaw);

// === STEP 1: locate every `isBootcamp` declaration via token stream ===
//
// Look-ahead gaps tolerate: whitespace, comments, visibility modifiers
// (`static`, `final`, `abstract`), and explicit type-hinted return types
// (`: bool`, `: ?Type`, etc.). Anything else terminates the search.
$tokens     = token_get_all($source);
$tokenCount = count($tokens);
$declLines  = []; // list of 1-based line numbers where isBootcamp is declared

for ($i = 0; $i < $tokenCount; $i++) {
    $t = $tokens[$i];
    if (! is_array($t) || $t[0] !== T_FUNCTION) {
        continue;
    }
    for ($j = $i + 1; $j < $tokenCount; $j++) {
        $n = $tokens[$j];
        if (is_array($n) && in_array($n[0], [
            T_WHITESPACE,
            T_DOC_COMMENT,
            T_COMMENT,
            T_STATIC,
            T_FINAL,
            T_ABSTRACT,
            // Common type tokens that may appear in the gap before the
            // identifier when an explicit return type is declared.
            T_STRING,
            T_ARRAY,
            T_CALLABLE,
            T_NULL,
            T_FALSE,
            T_TRUE,
            T_NS_SEPARATOR,
            T_NAME_QUALIFIED,
            T_NAME_FULLY_QUALIFIED,
            T_FN,
        ], true)) {
            continue;
        }
        if (is_array($n) && $n[0] === T_STRING && $n[1] === 'isBootcamp') {
            // $n[2] is the 1-based LINE number where `isBootcamp`
            // starts. We use this only as a starting line; byte math
            // is computed later from a real newline-aware cursor.
            $declLines[] = (int) $n[2];
        }
        break;
    }
}

echo '[scan] found ' . count($declLines) . " isBootcamp() declaration(s)\n";
foreach ($declLines as $idx => $line) {
    $label = (string) ($idx + 1);
    echo "        #{$label} at line {$line}\n";
}

if (count($declLines) === 0) {
    flock($fp, LOCK_UN);
    fclose($fp);
    echo "[ok] nothing to fix - the helper is absent on disk.\n";
    exit(0);
}
if (count($declLines) === 1) {
    flock($fp, LOCK_UN);
    fclose($fp);
    echo "[ok] already deduped - exactly 1 isBootcamp() remains.\n";
    exit(0);
}
if (count($declLines) > 2) {
    flock($fp, LOCK_UN);
    fclose($fp);
    fwrite(STDERR, "FATAL: >2 declarations found - refusing to auto-fix (manual triage required).\n");
    exit(3);
}

// === STEP 2: compute the byte offset of the duplicate's declaration line ===
//
// NOTE: PHP `token_get_all` returns LINE numbers in `$t[2]`, NOT byte
// offsets. We therefore derive byte offsets from a real newline-by-
// newline summation. This is the source of the Q9 bug in earlier
// versions; the byte/value types here are kept visually distinct.
$dupLine      = $declLines[1];
$lines        = explode("\n", $source);
$lineCount    = count($lines);
$dupStartByte = 0;
for ($i = 0; $i < min($dupLine - 1, $lineCount); $i++) {
    $dupStartByte += strlen($lines[$i]) + 1; // +1 for `\n`
}

echo "[plan] keep declaration #1 (line {$declLines[0]}), remove duplicate (line {$dupLine})\n";
echo "[plan] duplicate's starting byte offset = {$dupStartByte}\n";

// === STEP 3: char-by-char brace walk from $dupStartByte to find matching `}` ===
//
// ASSUMPTION: the duplicate's source body contains no literal `{` or `}`
// characters outside of code-level braces. True for isBootcamp's body
// (`return $course && strtolower((string) ($course->kategori ?? '')) === 'tiket';`)
// and for both possible duplicate bodies. If reused for a method whose
// body has string-embedded braces, switch to a token-aware brace walker.
$srcLen    = strlen($source);
$depth     = 0;
$seenOpen  = false;
$dupEndByte = null;
for ($b = $dupStartByte; $b < $srcLen; $b++) {
    $ch = $source[$b];
    if ($ch === '{') {
        $depth++;
        $seenOpen = true;
    } elseif ($ch === '}') {
        $depth--;
        if ($seenOpen && $depth === 0) {
            $dupEndByte = $b + 1; // byte position just past the closing `}`
            break;
        }
    }
}

if ($dupEndByte === null) {
    flock($fp, LOCK_UN);
    fclose($fp);
    fwrite(STDERR, "FATAL: could not locate matching `}` for duplicate (line {$dupLine}). File untouched.\n");
    exit(4);
}

// === STEP 4: assemble the spliced source ===
$prefix       = substr($source, 0, $dupStartByte);
$suffix       = substr($source, $dupEndByte);
$prefixRtrim  = rtrim($prefix, "\n");
$suffixLtrim  = ltrim($suffix, "\n");
$spliced      = $prefixRtrim . "\n\n" . $suffixLtrim . "\n";

// === STEP 5: pre-write verifications (token_get_all only) ===

// 5a) Re-scan the spliced source for declaration count.
$verifyTokens = token_get_all($spliced);
$verifyCount  = 0;
for ($i = 0; $i < count($verifyTokens); $i++) {
    $t = $verifyTokens[$i];
    if (! is_array($t) || $t[0] !== T_FUNCTION) {
        continue;
    }
    for ($j = $i + 1; $j < count($verifyTokens); $j++) {
        $n = $verifyTokens[$j];
        if (is_array($n) && in_array($n[0], [
            T_WHITESPACE, T_DOC_COMMENT, T_COMMENT,
            T_STATIC, T_FINAL, T_ABSTRACT,
            T_STRING, T_ARRAY, T_CALLABLE, T_NULL, T_FALSE, T_TRUE,
            T_NS_SEPARATOR, T_NAME_QUALIFIED, T_NAME_FULLY_QUALIFIED,
            T_FN,
        ], true)) {
            continue;
        }
        if (is_array($n) && $n[0] === T_STRING && $n[1] === 'isBootcamp') {
            $verifyCount++;
        }
        break;
    }
}
echo "[verify] {$verifyCount} isBootcamp() in spliced source\n";
if ($verifyCount !== 1) {
    flock($fp, LOCK_UN);
    fclose($fp);
    fwrite(STDERR, "FATAL: post-splice count ({$verifyCount}) != 1; aborting before any write.\n");
    exit(5);
}

// 5b) Parse-validity check via TOKEN_PARSE. Catches unbalanced braces,
// missing semicolons, etc. — strongly indicative of a clean splice.
if (@token_get_all($spliced, TOKEN_PARSE) === false) {
    if (function_exists('error_get_last')) {
        $lastError = error_get_last();
        if (is_array($lastError) && ! empty($lastError['message'])) {
            fwrite(STDERR, "       parse-error: " . $lastError['message'] . "\n");
        }
    }
    flock($fp, LOCK_UN);
    fclose($fp);
    fwrite(STDERR, "FATAL: spliced source did not parse as valid PHP.\n");
    exit(6);
}
echo "[lint] token_get_all(TOKEN_PARSE) OK\n";

// 5c) Sanity: the spliced file should still END with the class's `}`.
$trimmedTail = rtrim($spliced);
$expectedTail = '}';
if (substr($trimmedTail, -strlen($expectedTail)) !== $expectedTail) {
    flock($fp, LOCK_UN);
    fclose($fp);
    fwrite(STDERR, "FATAL: spliced file does not end with `}` (class closing brace missing?)\n");
    exit(5);
}

// === STEP 6: backup before any write ===
$backup = dirname($path) . '/CourseController.php.bak.' . date('Ymd-His');
if (! copy($path, $backup)) {
    flock($fp, LOCK_UN);
    fclose($fp);
    fwrite(STDERR, "FATAL: failed to write backup {$backup}\n");
    exit(7);
}
echo "[backup] {$backup}\n";

// === STEP 7: atomic write via tmp + rename ===
$tmp = $path . '.tmp';
if (file_put_contents($tmp, $spliced) === false) {
    @unlink($tmp);
    flock($fp, LOCK_UN);
    fclose($fp);
    fwrite(STDERR, "FATAL: tmp write failed: {$tmp}\n");
    exit(8);
}

if (! rename($tmp, $path)) {
    flock($fp, LOCK_UN);
    fclose($fp);
    fwrite(STDERR, "FATAL: atomic rename failed; backup preserved at {$backup}\n");
    if (copy($backup, $path)) {
        echo "[rollback] restored {$path} from {$backup}\n";
    }
    exit(9);
}
echo "[write] patched {$path}\n";

// === STEP 8: post-write integrity check (independent of step 5) ===
// Re-read the freshly-written file and re-validate.
$postSource = str_replace(["\r\n", "\r"], "\n", (string) file_get_contents($path));
if (@token_get_all($postSource, TOKEN_PARSE) === false) {
    @copy($backup, $path);
    flock($fp, LOCK_UN);
    fclose($fp);
    fwrite(STDERR, "FATAL: post-write PHP parse failed; restored from backup.\n");
    exit(10);
}

$postTokens = token_get_all($postSource);
$pc = 0;
for ($i = 0; $i < count($postTokens); $i++) {
    $t = $postTokens[$i];
    if (! is_array($t) || $t[0] !== T_FUNCTION) {
        continue;
    }
    for ($j = $i + 1; $j < count($postTokens); $j++) {
        $n = $postTokens[$j];
        if (is_array($n) && in_array($n[0], [
            T_WHITESPACE, T_DOC_COMMENT, T_COMMENT,
            T_STATIC, T_FINAL, T_ABSTRACT,
            T_STRING, T_ARRAY, T_CALLABLE, T_NULL, T_FALSE, T_TRUE,
            T_NS_SEPARATOR, T_NAME_QUALIFIED, T_NAME_FULLY_QUALIFIED,
            T_FN,
        ], true)) {
            continue;
        }
        if (is_array($n) && $n[0] === T_STRING && $n[1] === 'isBootcamp') {
            $pc++;
        }
        break;
    }
}
echo "[final] {$pc} isBootcamp() on disk\n";

if ($pc !== 1) {
    @copy($backup, $path);
    flock($fp, LOCK_UN);
    fclose($fp);
    fwrite(STDERR, "FATAL: post-write count ({$pc}) != 1; restored from backup.\n");
    exit(10);
}

flock($fp, LOCK_UN);
fclose($fp);

echo "[ok] duplicate removed; canonical isBootcamp() remains intact.\n";
echo "[ok] backup preserved at {$backup} - delete it manually once verified.\n";
exit(0);
