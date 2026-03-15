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
            'kategori' => $this->kategori,
            'harga' => (float) $this->harga,
            'harga_formatted' => 'Rp ' . number_format($this->harga, 0, ',', '.'),
            'rating' => (float) $this->rating,
            'jumlah_ulasan' => $this->jumlah_ulasan,
            'status' => $this->status,
            'approval_status' => $this->approval_status,
            'approval_notes' => $this->approval_notes,
            'approved_at' => $this->approved_at?->toISOString(),
            'tanggal_webinar' => $this->tanggal_webinar?->format('Y-m-d'),
            'jam_mulai_webinar' => $this->jam_mulai_webinar,
            'jam_selesai_webinar' => $this->jam_selesai_webinar,
            'kuota_peserta' => $this->kuota_peserta,
            'link_meeting' => $this->kategori === 'webinar' ? $this->youtube_playlist : null,
            'youtube_playlist' => $this->youtube_playlist,
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
