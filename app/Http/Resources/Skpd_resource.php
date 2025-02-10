<?php

namespace App\Http\Resources;

use App\Models\Regional;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class Skpd_resource extends JsonResource
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
            'nama' => $this->nama,
            'region' => new Regional_resource($this->regional),
            'instansi_kode' => $this->instansi_kode,
            'instansi_telp' => $this->instansi_telp,
            'instansi_fax' => $this->instansi_fax,
            'instansi_alamat' => $this->instansi_alamat,
            'instansi_email' => $this->instansi_email
        ];
        // return parent::toArray($request);
    }
}
