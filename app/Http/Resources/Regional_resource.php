<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class Regional_resource extends JsonResource
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
            'no_prop' => $this->no_prop,
            'no_kab' => $this->no_kab,
            'no_kec' => $this->no_kec,
            'no_kel' => $this->no_kel,
            'nama' => $this->nama,
            'skpd' => $this->skpd
        ];
    }
}
