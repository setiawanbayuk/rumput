<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class Rt_resource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'   => (string) $this->rt,
            'rt'   => $this->rt,
            'text' => 'RT ' . $this->rt, // atau cukup $this->rt kalau mau tampil "1,2,3"
        ];
    }
}
