<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MahasiswaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
            'status' => $this->status,
            'profile' => [
                'nim' => $this->profile->nim ?? null,
                'tempat_lahir' => $this->profile->tempat_lahir ?? null,
                'tanggal_lahir' => $this->profile->tanggal_lahir ?? null,
                'jenis_kelamin' => $this->profile->jenis_kelamin ?? null,
                'alamat' => $this->profile->alamat ?? null,
                'no_hp' => $this->profile->no_hp ?? null,
                'id_jurusan' => $this->profile->id_jurusan ?? null,
                'foto_profile' => $this->profile->foto_profile ?? null,
                'foto_profile_url' => $this->profile->foto_profile
                    ? asset('storage/' . $this->profile->foto_profile)
                    : null,
                'bio' => $this->profile->bio ?? null,
            ],
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
        ];
    }
}
