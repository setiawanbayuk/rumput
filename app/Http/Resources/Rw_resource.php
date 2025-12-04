<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class Rw_resource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'   => (string) $this->rw,
            'rw'   => $this->rw,
            'text' => 'RW ' . $this->rw, // atau cukup $this->rw kalau mau tampil "1,2,3"
        ];
    }
}
