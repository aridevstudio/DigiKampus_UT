<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NewsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id_news' => $this->id_news,
            'judul' => $this->judul,
            'konten' => $this->konten,
            'thumbnail' => $this->thumbnail,
            'thumbnail_url' => $this->thumbnail
                ? asset('storage/' . $this->thumbnail)
                : null,
            'kategori' => $this->kategori,
            'tanggal_publish' => $this->tanggal_publish?->toDateTimeString(),
            'waktu_relatif' => $this->waktu_relatif,
        ];
    }
}
