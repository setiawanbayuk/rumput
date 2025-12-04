<?php

namespace App\Traits;

trait HasUiStatus
{
    public function getUiStatusAttribute()
    {
        $status = (int) $this->status;   // ← status dari tabel surat
        $jenis  = $this->getTable();     // contoh: 'surat_skbns'
        $isSKTM = ($jenis === 'surat_sktms');

        // === SELESAI ===
        if (!$isSKTM && $status == 4) {
            return [
                'step'        => 3,
                'label'       => 'SELESAI',
                'color_class' => 'bar--selesai',
            ];
        }

        if ($isSKTM && $status == 9) {   // SKTM selesai = status 9
            return [
                'step'        => 3,
                'label'       => 'SELESAI',
                'color_class' => 'bar--selesai',
            ];
        }

        // === DINILAI ===
        if ($status == 5) {
            return [
                'step'        => 4,
                'label'       => 'DINILAI',
                'color_class' => 'bar--dinilai',
            ];
        }

        // === DITOLAK ===
        if ($status == 6) {
            return [
                'step'        => 1,
                'label'       => 'DITOLAK',
                'color_class' => 'bar--ditolak',
            ];
        }

        // === DIAJUKAN ===
        if ($status == 0) {
            return [
                'step'        => 1,
                'label'       => 'DIAJUKAN',
                'color_class' => 'bar--diajukan',
            ];
        }

        // === DIPROSES ===
        return [
            'step'        => 2,
            'label'       => 'DIPROSES',
            'color_class' => 'bar--diproses',
        ];
    }
}
