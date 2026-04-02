<?php

namespace App\Traits;

trait StatusSuratTrait
{
    /**
     * Accessor universal untuk mendapatkan nama dan warna status.
     * Dipanggil via $model->st
     */
    public function getStAttribute()
    {
        return match ((int)$this->status) {
            1 => ['name' => 'Proses', 'color' => 'blue'],
            2 => ['name' => 'Dinaikkan ke Sekkel', 'color' => 'orange'],
            3 => ['name' => 'Dinaikkan ke Lurah', 'color' => 'orange'],
            4 => ['name' => 'Disetujui', 'color' => 'green'],
            5 => ['name' => 'Dinilai', 'color' => '#EFBF04'],
            6 => ['name' => 'Ditolak', 'color' => 'red'],
            7 => ['name' => 'Dihapus', 'color' => 'red'],
            default => ['name' => 'Pengajuan', 'color' => 'black'],
        };
    }

    /**
     * Helper jika hanya butuh Nama Status saja
     */
    public function getStatusName()
    {
        return $this->st['name'];
    }
}
