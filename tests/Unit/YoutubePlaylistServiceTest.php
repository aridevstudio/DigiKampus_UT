<?php

namespace Tests\Unit;

use App\Services\YoutubePlaylistService;
use PHPUnit\Framework\TestCase;

class YoutubePlaylistServiceTest extends TestCase
{
    /**
     * @dataProvider playlistUrlProvider
     */
    public function test_extract_playlist_id(string $url, ?string $expected): void
    {
        $this->assertSame($expected, YoutubePlaylistService::extractPlaylistId($url));
    }

    public static function playlistUrlProvider(): array
    {
        return [
            'standard url' => [
                'https://www.youtube.com/playlist?list=PLrAXtmErZgOeiKm4sgNOknGvNjby9efdf',
                'PLrAXtmErZgOeiKm4sgNOknGvNjby9efdf',
            ],
            'short url' => [
                'https://youtube.com/playlist?list=PLrAXtmErZgOeiKm4sgNOknGvNjby9efdf',
                'PLrAXtmErZgOeiKm4sgNOknGvNjby9efdf',
            ],
            'url with extra params' => [
                'https://www.youtube.com/playlist?list=PLrAXtmErZgOeiKm4sgNOknGvNjby9efdf&si=abc',
                'PLrAXtmErZgOeiKm4sgNOknGvNjby9efdf',
            ],
            'embed url' => [
                'https://www.youtube.com/embed/videoseries?list=PLrAXtmErZgOeiKm4sgNOknGvNjby9efdf',
                'PLrAXtmErZgOeiKm4sgNOknGvNjby9efdf',
            ],
            'just playlist id' => [
                'PLrAXtmErZgOeiKm4sgNOknGvNjby9efdf',
                'PLrAXtmErZgOeiKm4sgNOknGvNjby9efdf',
            ],
            'invalid url' => [
                'https://www.google.com',
                null,
            ],
            'empty string' => [
                '',
                null,
            ],
            'video url (not playlist)' => [
                'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                null,
            ],
        ];
    }
}
