<?php

namespace App\Services;

use App\Models\Course;
use App\Models\CourseMaterial;
use App\Models\CourseModule;
use App\Models\YoutubePlaylistVideo;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class YoutubePlaylistService
{
    public const AUTO_MODULE_TITLE = 'Playlist YouTube Otomatis';
    public const AUTO_MODULE_DESCRIPTION = 'Modul ini disinkronkan otomatis dari playlist YouTube kursus.';

    /**
     * Extract playlist ID from a YouTube URL.
     *
     * Supports:
     * - https://www.youtube.com/playlist?list=PLxxxxxx
     * - https://youtube.com/playlist?list=PLxxxxxx
     * - PLxxxxxx (raw ID)
     */
    public static function extractPlaylistId(string $url): ?string
    {
        $url = trim($url);

        // Direct playlist ID (starts with PL, OL, UU, etc.)
        if (preg_match('/^(PL|OL|UU|FL|LL|RD)[a-zA-Z0-9_-]+$/', $url)) {
            return $url;
        }

        // YouTube URL with list parameter
        if (preg_match('/[?&]list=([a-zA-Z0-9_-]+)/', $url, $matches)) {
            return $matches[1];
        }

        return null;
    }

    /**
     * Fetch playlist videos using YouTube oEmbed + noembed API (no API key needed).
     * Falls back to scraping the playlist page as RSS.
     */
    public static function fetchPlaylistVideos(string $playlistId): array
    {
        $apiKey = config('services.youtube.api_key');
        if ($apiKey) {
            try {
                return self::fetchFromYouTubeApi($playlistId, $apiKey);
            } catch (\Exception $e) {
                Log::warning('YouTube API fetch failed, falling back', [
                    'playlist' => $playlistId,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        try {
            return self::fetchFromYoutubeWeb($playlistId);
        } catch (\Exception $e) {
            Log::warning('YouTube web playlist fetch failed, falling back', [
                'playlist' => $playlistId,
                'error' => $e->getMessage(),
            ]);
        }

        // Try YouTube RSS feed first (no API key needed, returns up to 15 items)
        $rssUrl = "https://www.youtube.com/feeds/videos.xml?playlist_id={$playlistId}";

        try {
            $response = Http::timeout(15)->get($rssUrl);

            if ($response->successful()) {
                return self::parseRssFeed($response->body(), $playlistId);
            }
        } catch (\Exception $e) {
            Log::warning('YouTube RSS fetch failed', ['playlist' => $playlistId, 'error' => $e->getMessage()]);
        }

        // Fallback: try noembed for the playlist page
        try {
            $noembedUrl = "https://noembed.com/embed?url=https://www.youtube.com/playlist?list={$playlistId}";
            $response = Http::timeout(10)->get($noembedUrl);

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['error'])) {
                    throw new \RuntimeException('Playlist tidak ditemukan atau tidak publik.');
                }
                // noembed returns basic info but not individual videos
                // Return empty with a flag that we got valid playlist
                return [
                    'title' => $data['title'] ?? 'Unknown Playlist',
                    'videos' => [],
                    'source' => 'noembed',
                    'message' => 'Playlist valid tetapi detail video tidak tersedia dari endpoint publik YouTube.',
                ];
            }
        } catch (\RuntimeException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::warning('noembed fetch failed', ['playlist' => $playlistId, 'error' => $e->getMessage()]);
        }

        throw new \RuntimeException('Gagal mengambil data playlist. Pastikan playlist ID valid dan public.');
    }

    /**
     * Parse YouTube RSS feed XML into video array.
     */
    private static function parseRssFeed(string $xml, string $playlistId): array
    {
        $feed = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOERROR);

        if (!$feed) {
            throw new \RuntimeException('Gagal parse RSS feed dari YouTube.');
        }

        // Register namespaces
        $namespaces = $feed->getNamespaces(true);
        $videos = [];
        $order = 0;

        foreach ($feed->entry as $entry) {
            $yt = $entry->children($namespaces['yt'] ?? 'http://www.youtube.com/xml/schemas/2015');
            $media = $entry->children($namespaces['media'] ?? 'http://search.yahoo.com/mrss/');

            $videoId = (string)($yt->videoId ?? '');
            $title = (string)($entry->title ?? 'Untitled');

            $thumbnail = '';
            if (isset($media->group->thumbnail)) {
                $attrs = $media->group->thumbnail->attributes();
                $thumbnail = (string)($attrs['url'] ?? '');
            }

            if (empty($thumbnail) && $videoId) {
                $thumbnail = "https://img.youtube.com/vi/{$videoId}/mqdefault.jpg";
            }

            if ($videoId) {
                $videos[] = [
                    'youtube_id' => $videoId,
                    'title' => $title,
                    'thumbnail_url' => $thumbnail,
                    'urutan' => $order++,
                    'duration_seconds' => 0,
                ];
            }
        }

        if (empty($videos)) {
            throw new \RuntimeException('Playlist kosong atau tidak ditemukan video publik.');
        }

        return [
            'title' => (string)($feed->title ?? 'YouTube Playlist'),
            'videos' => $videos,
            'source' => 'rss',
            'message' => 'Playlist dibaca dari RSS YouTube. Feed ini biasanya hanya memuat sebagian video.',
        ];
    }

    private static function fetchFromYoutubeWeb(string $playlistId): array
    {
        $response = Http::withHeaders(self::youtubeWebHeaders())
            ->timeout(20)
            ->get("https://www.youtube.com/playlist?list={$playlistId}&hl=id");

        if (!$response->successful()) {
            throw new \RuntimeException('Gagal memuat halaman playlist YouTube.');
        }

        $html = $response->body();
        $initialData = self::extractJsonObject($html, 'ytInitialData');
        if (!$initialData) {
            throw new \RuntimeException('Data awal playlist YouTube tidak ditemukan.');
        }

        $ytCfg = self::extractYtConfig($html);
        $apiKey = $ytCfg['INNERTUBE_API_KEY'] ?? null;
        $clientName = $ytCfg['INNERTUBE_CONTEXT_CLIENT_NAME'] ?? 1;
        $clientVersion = $ytCfg['INNERTUBE_CONTEXT_CLIENT_VERSION'] ?? '2.20240101.00.00';
        $visitorData = $ytCfg['VISITOR_DATA'] ?? null;

        $videos = self::extractPlaylistVideoRenderers($initialData);
        $continuation = self::extractContinuationToken($initialData);

        while ($continuation && count($videos) < 500 && $apiKey) {
            $continuationData = self::fetchPlaylistContinuation(
                $continuation,
                $apiKey,
                $clientName,
                $clientVersion,
                $visitorData
            );

            $chunk = self::extractPlaylistVideoRenderers($continuationData);
            if (empty($chunk)) {
                break;
            }

            $videos = array_merge($videos, $chunk);
            $nextContinuation = self::extractContinuationToken($continuationData);
            if ($nextContinuation === $continuation) {
                break;
            }
            $continuation = $nextContinuation;
        }

        $normalized = self::normalizeVideoRenderers($videos);
        if (empty($normalized)) {
            throw new \RuntimeException('Playlist kosong atau tidak ditemukan video publik.');
        }

        return [
            'title' => self::extractPlaylistTitle($initialData) ?? 'YouTube Playlist',
            'videos' => $normalized,
            'source' => 'youtube-web',
            'message' => null,
        ];
    }

    private static function fetchPlaylistContinuation(
        string $continuation,
        string $apiKey,
        int|string $clientName,
        string $clientVersion,
        ?string $visitorData = null
    ): array {
        $payload = [
            'context' => [
                'client' => [
                    'clientName' => is_numeric($clientName) ? 'WEB' : $clientName,
                    'clientVersion' => $clientVersion,
                    'hl' => 'id',
                    'gl' => 'ID',
                ],
            ],
            'continuation' => $continuation,
        ];

        if ($visitorData) {
            $payload['context']['client']['visitorData'] = $visitorData;
        }

        $response = Http::withHeaders(array_merge(
                self::youtubeWebHeaders(),
                ['X-YouTube-Client-Name' => (string) $clientName, 'X-YouTube-Client-Version' => $clientVersion]
            ))
            ->timeout(20)
            ->post("https://www.youtube.com/youtubei/v1/browse?key={$apiKey}", $payload);

        if (!$response->successful()) {
            throw new \RuntimeException('Gagal memuat lanjutan playlist YouTube.');
        }

        return $response->json() ?? [];
    }

    private static function normalizeVideoRenderers(array $renderers): array
    {
        $videos = [];
        $seen = [];

        foreach (array_values($renderers) as $index => $renderer) {
            $videoId = $renderer['videoId'] ?? null;
            if (!$videoId || isset($seen[$videoId])) {
                continue;
            }

            $seen[$videoId] = true;
            $videos[] = [
                'youtube_id' => $videoId,
                'title' => self::readText($renderer['title'] ?? null) ?: 'Untitled',
                'thumbnail_url' => self::extractThumbnailUrl($renderer),
                'urutan' => count($videos),
                'duration_seconds' => self::extractDurationSeconds($renderer),
            ];
        }

        return $videos;
    }

    private static function extractPlaylistVideoRenderers(array $data): array
    {
        $results = [];

        $walker = function ($node) use (&$walker, &$results) {
            if (!is_array($node)) {
                return;
            }

            if (isset($node['playlistVideoRenderer']) && is_array($node['playlistVideoRenderer'])) {
                $results[] = $node['playlistVideoRenderer'];
            }

            foreach ($node as $value) {
                if (is_array($value)) {
                    $walker($value);
                }
            }
        };

        $walker($data);

        return $results;
    }

    private static function extractContinuationToken(array $data): ?string
    {
        $token = null;

        $walker = function ($node) use (&$walker, &$token) {
            if ($token || !is_array($node)) {
                return;
            }

            if (isset($node['continuationEndpoint']['continuationCommand']['token'])) {
                $token = $node['continuationEndpoint']['continuationCommand']['token'];
                return;
            }

            if (isset($node['nextContinuationData']['continuation'])) {
                $token = $node['nextContinuationData']['continuation'];
                return;
            }

            foreach ($node as $value) {
                if (is_array($value)) {
                    $walker($value);
                }
            }
        };

        $walker($data);

        return $token;
    }

    private static function extractPlaylistTitle(array $data): ?string
    {
        return data_get($data, 'metadata.playlistMetadataRenderer.title')
            ?? self::readText(data_get($data, 'sidebar.playlistSidebarRenderer.items.0.playlistSidebarPrimaryInfoRenderer.title'))
            ?? self::readText(data_get($data, 'header.playlistHeaderRenderer.title'));
    }

    private static function extractThumbnailUrl(array $renderer): string
    {
        $thumbnails = data_get($renderer, 'thumbnail.thumbnails', []);
        if (is_array($thumbnails) && !empty($thumbnails)) {
            $last = end($thumbnails);
            if (is_array($last) && !empty($last['url'])) {
                return $last['url'];
            }
        }

        $videoId = $renderer['videoId'] ?? '';

        return $videoId ? "https://img.youtube.com/vi/{$videoId}/mqdefault.jpg" : '';
    }

    private static function extractDurationSeconds(array $renderer): int
    {
        $lengthSeconds = (int) ($renderer['lengthSeconds'] ?? 0);
        if ($lengthSeconds > 0) {
            return $lengthSeconds;
        }

        $durationText = self::readText(data_get($renderer, 'lengthText'))
            ?? self::readText(data_get($renderer, 'thumbnailOverlays.0.thumbnailOverlayTimeStatusRenderer.text'));

        return self::parseDurationText($durationText);
    }

    private static function parseDurationText(?string $duration): int
    {
        if (!$duration) {
            return 0;
        }

        $parts = array_map('intval', explode(':', trim($duration)));
        if (count($parts) === 3) {
            return ($parts[0] * 3600) + ($parts[1] * 60) + $parts[2];
        }
        if (count($parts) === 2) {
            return ($parts[0] * 60) + $parts[1];
        }

        return 0;
    }

    private static function readText($textNode): ?string
    {
        if (is_string($textNode)) {
            return trim($textNode);
        }

        if (!is_array($textNode)) {
            return null;
        }

        if (!empty($textNode['simpleText'])) {
            return trim((string) $textNode['simpleText']);
        }

        if (!empty($textNode['runs']) && is_array($textNode['runs'])) {
            return trim(collect($textNode['runs'])->pluck('text')->implode(''));
        }

        return null;
    }

    private static function extractYtConfig(string $html): array
    {
        if (preg_match('/ytcfg\.set\(({.*?})\);\s*<\/script>/s', $html, $matches)) {
            $decoded = json_decode($matches[1], true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return $decoded;
            }
        }

        $config = [];
        foreach (['INNERTUBE_API_KEY', 'INNERTUBE_CONTEXT_CLIENT_NAME', 'INNERTUBE_CONTEXT_CLIENT_VERSION', 'VISITOR_DATA'] as $key) {
            if (preg_match('/"' . preg_quote($key, '/') . '":"([^"]+)"/', $html, $matches)) {
                $config[$key] = Str::replace('\/', '/', $matches[1]);
            }
        }

        return $config;
    }

    private static function extractJsonObject(string $html, string $variable): ?array
    {
        $patterns = [
            'var ' . $variable . ' = ',
            'window["' . $variable . '"] = ',
            'window[\'' . $variable . '\'] = ',
        ];

        foreach ($patterns as $pattern) {
            $position = strpos($html, $pattern);
            if ($position === false) {
                continue;
            }

            $start = strpos($html, '{', $position);
            if ($start === false) {
                continue;
            }

            $json = self::sliceBalancedJson($html, $start);
            if (!$json) {
                continue;
            }

            $decoded = json_decode($json, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return $decoded;
            }
        }

        return null;
    }

    private static function sliceBalancedJson(string $html, int $start): ?string
    {
        $depth = 0;
        $inString = false;
        $escape = false;
        $length = strlen($html);

        for ($i = $start; $i < $length; $i++) {
            $char = $html[$i];

            if ($inString) {
                if ($escape) {
                    $escape = false;
                } elseif ($char === '\\') {
                    $escape = true;
                } elseif ($char === '"') {
                    $inString = false;
                }
                continue;
            }

            if ($char === '"') {
                $inString = true;
                continue;
            }

            if ($char === '{') {
                $depth++;
            } elseif ($char === '}') {
                $depth--;
                if ($depth === 0) {
                    return substr($html, $start, $i - $start + 1);
                }
            }
        }

        return null;
    }

    private static function youtubeWebHeaders(): array
    {
        return [
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36',
            'Accept-Language' => 'id-ID,id;q=0.9,en-US;q=0.8,en;q=0.7',
            'Accept' => 'text/html,application/json',
            'Referer' => 'https://www.youtube.com/',
        ];
    }

    /**
     * Fetch from YouTube Data API v3 (requires API key).
     */
    private static function fetchFromYouTubeApi(string $playlistId, string $apiKey): array
    {
        $videos = [];
        $pageToken = null;
        $order = 0;

        do {
            $url = 'https://www.googleapis.com/youtube/v3/playlistItems?' . http_build_query(array_filter([
                'part' => 'snippet',
                'playlistId' => $playlistId,
                'maxResults' => 50,
                'pageToken' => $pageToken,
                'key' => $apiKey,
            ]));

            $response = Http::timeout(15)->get($url);

            if (!$response->successful()) {
                $error = $response->json('error.message', 'Unknown error');
                if ($response->status() === 403) {
                    throw new \RuntimeException("YouTube API quota terlampaui. Coba lagi besok. ({$error})");
                }
                throw new \RuntimeException("YouTube API error: {$error}");
            }

            $data = $response->json();
            $pageToken = $data['nextPageToken'] ?? null;

            foreach ($data['items'] ?? [] as $item) {
                $snippet = $item['snippet'] ?? [];
                $videoId = $snippet['resourceId']['videoId'] ?? '';

                if ($videoId) {
                    $thumbnails = $snippet['thumbnails'] ?? [];
                    $thumb = $thumbnails['medium']['url']
                        ?? $thumbnails['default']['url']
                        ?? "https://img.youtube.com/vi/{$videoId}/mqdefault.jpg";

                    $videos[] = [
                        'youtube_id' => $videoId,
                        'title' => $snippet['title'] ?? 'Untitled',
                        'thumbnail_url' => $thumb,
                        'urutan' => $order++,
                        'duration_seconds' => 0,
                    ];
                }
            }
        } while ($pageToken && $order < 200); // Safety limit

        if (empty($videos)) {
            throw new \RuntimeException('Playlist kosong atau tidak ditemukan video publik.');
        }

        return [
            'title' => 'YouTube Playlist',
            'videos' => $videos,
            'source' => 'api',
            'message' => null,
        ];
    }

    /**
     * Sync playlist videos into DB for a given course.
     * Returns summary of changes.
     */
    public static function syncToDatabase(int $courseId, array $videosData): array
    {
        $existing = YoutubePlaylistVideo::where('id_course', $courseId)
            ->pluck('youtube_id')
            ->toArray();

        $newIds = collect($videosData)->pluck('youtube_id')->toArray();

        $added = 0;
        $updated = 0;
        $removed = 0;

        // Upsert videos
        foreach ($videosData as $video) {
            $record = YoutubePlaylistVideo::updateOrCreate(
                ['id_course' => $courseId, 'youtube_id' => $video['youtube_id']],
                [
                    'title' => $video['title'],
                    'thumbnail_url' => $video['thumbnail_url'] ?? null,
                    'urutan' => $video['urutan'],
                    'duration_seconds' => $video['duration_seconds'] ?? 0,
                ]
            );

            if ($record->wasRecentlyCreated) {
                $added++;
            } else {
                $updated++;
            }
        }

        // Remove videos no longer in playlist
        $toRemove = array_diff($existing, $newIds);
        if (!empty($toRemove)) {
            $removed = YoutubePlaylistVideo::where('id_course', $courseId)
                ->whereIn('youtube_id', $toRemove)
                ->delete();
        }

        return [
            'added' => $added,
            'updated' => $updated,
            'removed' => $removed,
            'total' => count($videosData),
        ];
    }

    public static function syncCourseContent(int $courseId, array $videosData): array
    {
        return DB::transaction(function () use ($courseId, $videosData) {
            return [
                'database' => self::syncToDatabase($courseId, $videosData),
                'materials' => self::syncMaterials($courseId, $videosData),
            ];
        });
    }

    public static function clearCourseContent(int $courseId): array
    {
        return DB::transaction(function () use ($courseId) {
            $videoCount = YoutubePlaylistVideo::where('id_course', $courseId)->delete();

            $module = CourseModule::where('id_course', $courseId)
                ->where('judul_module', self::AUTO_MODULE_TITLE)
                ->first();

            $materialCount = 0;
            $moduleDeleted = false;

            if ($module) {
                $materialCount = CourseMaterial::where('id_module', $module->id_module)->delete();
                $moduleDeleted = (bool) $module->delete();
            }

            return [
                'videos_deleted' => $videoCount,
                'materials_deleted' => $materialCount,
                'module_deleted' => $moduleDeleted,
            ];
        });
    }

    private static function syncMaterials(int $courseId, array $videosData): array
    {
        $module = CourseModule::where('id_course', $courseId)
            ->where('judul_module', self::AUTO_MODULE_TITLE)
            ->first();

        if (!$module) {
            $module = CourseModule::create([
                'id_course' => $courseId,
                'judul_module' => self::AUTO_MODULE_TITLE,
                'deskripsi' => self::AUTO_MODULE_DESCRIPTION,
                'urutan' => ((int) CourseModule::where('id_course', $courseId)->max('urutan')) + 1,
            ]);
        }

        $added = 0;
        $updated = 0;
        $playlistUrls = [];

        foreach (array_values($videosData) as $index => $video) {
            $videoUrl = self::buildVideoUrl($video['youtube_id']);
            $playlistUrls[] = $videoUrl;

            $material = CourseMaterial::updateOrCreate(
                [
                    'id_course' => $courseId,
                    'id_module' => $module->id_module,
                    'video_url' => $videoUrl,
                ],
                [
                    'judul_material' => $video['title'],
                    'tipe' => 'video',
                    'konten' => 'Materi video hasil sinkronisasi otomatis dari playlist YouTube.',
                    'urutan' => $index + 1,
                    'durasi' => self::toMinutes((int) ($video['duration_seconds'] ?? 0)),
                ]
            );

            if ($material->wasRecentlyCreated) {
                $added++;
            } else {
                $updated++;
            }
        }

        $removed = 0;
        $query = CourseMaterial::where('id_course', $courseId)
            ->where('id_module', $module->id_module)
            ->where('tipe', 'video');

        if (!empty($playlistUrls)) {
            $removed = (clone $query)->whereNotIn('video_url', $playlistUrls)->delete();
        } else {
            $removed = (clone $query)->delete();
        }

        return [
            'module_id' => $module->id_module,
            'added' => $added,
            'updated' => $updated,
            'removed' => $removed,
            'total' => count($videosData),
        ];
    }

    private static function buildVideoUrl(string $youtubeId): string
    {
        return 'https://www.youtube.com/watch?v=' . $youtubeId;
    }

    private static function toMinutes(int $seconds): int
    {
        if ($seconds <= 0) {
            return 0;
        }

        return (int) ceil($seconds / 60);
    }
}
