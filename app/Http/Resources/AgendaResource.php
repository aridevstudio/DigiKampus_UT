<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AgendaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id_agenda' => $this->id_agenda,
            'judul' => $this->judul,
            'deskripsi' => $this->deskripsi,
            'tanggal' => $this->tanggal?->format('Y-m-d'),
            'hari' => $this->tanggal?->day,
            'waktu_mulai' => $this->waktu_mulai,
            'waktu_selesai' => $this->waktu_selesai,
            'tipe' => $this->tipe,
            'warna' => $this->warna,
        ];
    }
}
