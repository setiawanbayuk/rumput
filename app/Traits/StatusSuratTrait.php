<?php

namespace App\Traits;

trait StatusSuratTrait
{
    public function getStAttribute()
    {
        return match ((int) $this->status) {
            1 => ['name' => 'Proses', 'color' => 'blue'],
            2 => ['name' => 'Dinaikkan ke Sekkel', 'color' => 'orange'],
            3 => ['name' => 'Dinaikkan ke Lurah', 'color' => 'orange'],
            4 => ['name' => 'Disetujui', 'color' => 'green'],
            5 => ['name' => 'Dinilai', 'color' => '#EFBF04'],
            6 => ['name' => 'Ditolak', 'color' => 'red'],
            7 => ['name' => 'Dihapus', 'color' => 'red'],
            8 => ['name' => 'Dinaikkan ke Camat', 'color' => '#B2784A'],
            9 => ['name' => 'Disetujui Camat', 'color' => '#A78BFA'],
            default => ['name' => 'Pengajuan', 'color' => 'black'],
        };
    }

    public function getStatusName()
    {
        return $this->st['name'];
    }
}