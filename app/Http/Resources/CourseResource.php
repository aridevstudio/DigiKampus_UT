<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id_course' => $this->id_course,
            'kode_course' => $this->kode_course,
            'nama_course' => $this->nama_course,
            'deskripsi' => $this->deskripsi,
            'thumbnail' => $this->thumbnail,
            'thumbnail_url' => $this->thumbnail
                ? asset('storage/' . $this->thumbnail)
                : null,
            'tipe' => $this->tipe,
            'harga' => (float) $this->harga,
            'harga_formatted' => 'Rp ' . number_format($this->harga, 0, ',', '.'),
            'rating' => (float) $this->rating,
            'jumlah_ulasan' => $this->jumlah_ulasan,
            'status' => $this->status,
            'dosen' => $this->dosen ? [
                'id' => $this->dosen->id,
                'name' => $this->dosen->name,
            ] : null,
            'jurusan' => $this->jurusan ? [
                'nama_jurusan' => $this->jurusan->nama_jurusan,
            ] : null,
        ];
    }
}
