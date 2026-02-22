<?php

namespace App\Services;

use App\Models\Course;
use App\Models\YoutubePlaylistVideo;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class YoutubePlaylistService
{
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
                    'message' => 'Playlist valid tetapi detail video tidak tersedia tanpa YouTube API key. Tambahkan YOUTUBE_API_KEY di .env untuk sinkronisasi penuh.',
                ];
            }
        } catch (\RuntimeException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::warning('noembed fetch failed', ['playlist' => $playlistId, 'error' => $e->getMessage()]);
        }

        // Try YouTube Data API v3 if key is configured
        $apiKey = config('services.youtube.api_key');
        if ($apiKey) {
            return self::fetchFromYouTubeApi($playlistId, $apiKey);
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
            'message' => null,
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
}
