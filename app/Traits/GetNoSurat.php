<?php

namespace App\Traits;

use App\Models\Skpd;
use Carbon\Carbon;

trait GetNoSurat
{
    public function getNoSrt($surat): string
    {
        $idKel = (int) ($surat->id_kel ?? 0);

        $skpd = Skpd::with('kecamatan')->find($idKel);

        $tahun = date('Y');

        if (!empty($surat->tgl_surat)) {
            try {
                $tahun = Carbon::parse($surat->tgl_surat)->format('Y');
            } catch (\Throwable $e) {
                $tahun = date('Y');
            }
        }

        $kdJenis = trim((string) ($surat->kd_jenis_surat ?? ''));
        $noUrut = trim((string) ($surat->no_urut_surat ?? ''));
        $kodeInstansi = $skpd ? trim((string) ($skpd->instansi_kode ?? '')) : '';

        if ($kodeInstansi === '') {
            $kodeInstansi = (string) $idKel;
        }

        return $kdJenis . '/' . $noUrut . '/' . $kodeInstansi . '/' . $tahun;
    }
}