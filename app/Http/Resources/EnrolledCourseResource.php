<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EnrolledCourseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id_enroll' => $this->id_enroll,
            'id_course' => $this->id_course,
            'kode_course' => $this->course->kode_course ?? null,
            'nama_course' => $this->course->nama_course ?? null,
            'thumbnail' => $this->course->thumbnail ?? null,
            'thumbnail_url' => $this->course->thumbnail
                ? asset('storage/' . $this->course->thumbnail)
                : null,
            'progress' => round($this->progress, 0),
            'status' => $this->status,
            'tanggal_daftar' => $this->tanggal_daftar?->toDateTimeString(),
            'dosen' => $this->course->dosen ? [
                'name' => $this->course->dosen->name,
            ] : null,
        ];
    }
}
