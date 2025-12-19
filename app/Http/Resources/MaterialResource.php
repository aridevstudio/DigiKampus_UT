<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MaterialResource extends JsonResource
{
    /**
     * The mahasiswa ID for progress check.
     */
    protected ?int $mahasiswaId = null;

    /**
     * Set mahasiswa ID for progress check.
     */
    public function forMahasiswa(int $mahasiswaId): self
    {
        $this->mahasiswaId = $mahasiswaId;
        return $this;
    }

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $isCompleted = false;
        $watchedDuration = 0;

        if ($this->mahasiswaId) {
            $progress = $this->getProgressFor($this->mahasiswaId);
            if ($progress) {
                $isCompleted = $progress->is_completed;
                $watchedDuration = $progress->watched_duration;
            }
        }

        return [
            'id_material' => $this->id_material,
            'judul_material' => $this->judul_material,
            'tipe' => $this->tipe,
            'konten' => $this->konten,
            'video_url' => $this->video_url,
            'urutan' => $this->urutan,
            'durasi' => $this->durasi,
            'durasi_formatted' => $this->durasi . ' menit',
            'is_completed' => $isCompleted,
            'watched_duration' => $watchedDuration,
        ];
    }
}
