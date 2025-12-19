<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MyCourseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $course = $this->course;

        return [
            'id_enroll' => $this->id_enroll,
            'id_course' => $this->id_course,
            'kode_course' => $course->kode_course ?? null,
            'nama_course' => $course->nama_course ?? null,
            'deskripsi' => $course->deskripsi ?? null,
            'thumbnail' => $course->thumbnail,
            'thumbnail_url' => $course->thumbnail
                ? asset('storage/' . $course->thumbnail)
                : null,
            'tipe' => $course->tipe ?? 'kursus',
            'progress' => (int) $this->progress,
            'status' => $this->status,
            'status_label' => $this->status === 'aktif' ? 'Sedang Berlangsung' : 'Selesai',
            'rating' => (float) ($course->rating ?? 0),
            'tanggal_daftar' => $this->tanggal_daftar?->toDateTimeString(),
            'dosen' => $course->dosen ? [
                'name' => $course->dosen->name,
            ] : null,
        ];
    }
}
