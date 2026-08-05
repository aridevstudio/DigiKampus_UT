<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class AdminSystemLogController extends Controller
{
    /**
     * Tampilkan antarmuka System Log Viewer
     */
    public function index(Request $request)
    {
        $logPath = storage_path('logs/laravel.log');
        $exists = File::exists($logPath);
        $fileSize = $exists ? File::size($logPath) : 0;
        $lastModified = $exists ? File::lastModified($logPath) : null;

        $search = trim($request->get('search', ''));
        $levelFilter = strtolower(trim($request->get('level', 'all')));

        $parsedLogs = [];
        $totalErrors = 0;
        $totalWarnings = 0;
        $totalInfos = 0;

        if ($exists && $fileSize > 0) {
            $content = File::get($logPath);
            $parsedLogs = $this->parseLogContent($content);

            // Hitung statistik sebelum filter
            foreach ($parsedLogs as $log) {
                $lvl = strtolower($log['level']);
                if (in_array($lvl, ['error', 'critical', 'emergency', 'alert'])) {
                    $totalErrors++;
                } elseif ($lvl === 'warning') {
                    $totalWarnings++;
                } elseif ($lvl === 'info') {
                    $totalInfos++;
                }
            }

            // Apply level filter
            if ($levelFilter !== 'all') {
                $parsedLogs = array_filter($parsedLogs, function ($log) use ($levelFilter) {
                    $lvl = strtolower($log['level']);
                    if ($levelFilter === 'error') {
                        return in_array($lvl, ['error', 'critical', 'emergency', 'alert']);
                    }
                    return $lvl === $levelFilter;
                });
            }

            // Apply search filter
            if ($search !== '') {
                $parsedLogs = array_filter($parsedLogs, function ($log) use ($search) {
                    return stripos($log['message'], $search) !== false
                        || stripos($log['context'], $search) !== false
                        || stripos($log['timestamp'], $search) !== false;
                });
            }

            // Limit logs displayed to latest 300 entries for performance
            $parsedLogs = array_values(array_reverse(array_slice(array_reverse($parsedLogs), 0, 300)));
        }

        return view('Auth.admin.system-logs', [
            'logs' => $parsedLogs,
            'exists' => $exists,
            'fileSizeFormatted' => $this->formatBytes($fileSize),
            'lastModifiedFormatted' => $lastModified ? date('d M Y H:i:s', $lastModified) : '-',
            'totalEntries' => count($parsedLogs),
            'totalErrors' => $totalErrors,
            'totalWarnings' => $totalWarnings,
            'totalInfos' => $totalInfos,
            'search' => $search,
            'levelFilter' => $levelFilter,
        ]);
    }

    /**
     * Bersihkan file log laravel.log
     */
    public function clear(Request $request)
    {
        $logPath = storage_path('logs/laravel.log');
        if (File::exists($logPath)) {
            File::put($logPath, '');
        }

        return redirect()->route('admin.system-logs')
            ->with('status', 'File system log berhasil dibersihkan.');
    }

    /**
     * Unduh file log laravel.log
     */
    public function download(): BinaryFileResponse
    {
        $logPath = storage_path('logs/laravel.log');
        if (!File::exists($logPath)) {
            File::put($logPath, '');
        }

        return response()->download($logPath, 'laravel-' . date('Y-m-d') . '.log');
    }

    /**
     * Parse isi berkas log Laravel menjadi daftar entri terstruktur
     */
    private function parseLogContent(string $content): array
    {
        $pattern = '/^\[(\d{4}-\d{2}-\d{2}[ T]\d{2}:\d{2}:\d{2}(?:\.\d+)?)\]\s+([\w\.-]+)\.([A-Z]+):\s+(.*)$/m';

        preg_match_all($pattern, $content, $matches, PREG_OFFSET_CAPTURE);

        if (empty($matches[0])) {
            return [];
        }

        $logs = [];
        $count = count($matches[0]);

        for ($i = 0; $i < $count; $i++) {
            $offset = $matches[0][$i][1];
            $nextOffset = ($i + 1 < $count) ? $matches[0][$i + 1][1] : strlen($content);
            $fullBlock = substr($content, $offset, $nextOffset - $offset);

            $timestamp = $matches[1][$i][0];
            $environment = $matches[2][$i][0];
            $level = $matches[3][$i][0];
            $messageLine = trim($matches[4][$i][0]);

            // Ekstrak stacktrace/detail jika baris berikutnya berisi indentation/trace
            $extraContext = trim(substr($fullBlock, strlen($matches[0][$i][0])));

            $logs[] = [
                'timestamp' => $timestamp,
                'environment' => $environment,
                'level' => $level,
                'message' => $messageLine,
                'context' => $extraContext,
            ];
        }

        return $logs;
    }

    private function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= pow(1024, $pow);

        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}
