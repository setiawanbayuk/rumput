<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class Pejabat_resource extends JsonResource
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
            'skpd' => new Skpd_resource($this->skpd),
            'nip' => $this->nip,
            'nama' => $this->nama,
            'jabatan' => new Jabatan_resource($this->jabatan),
            'pangkat' => new Pangkat_resource($this->pangkat),
        ];
    }
}
