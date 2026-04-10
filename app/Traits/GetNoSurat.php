<?php

namespace App\Traits;

use App\Models\Pejabat;
use App\Models\Skpd;
use Carbon\Carbon;

trait GetNoSurat
{
    public function getNoSrt($surat): string
    {
        $idKel = (int) ($surat->id_kel ?? 0);

        $skpd = Skpd::with('kecamatan')->find($idKel);
        if (!$skpd) {
            throw new \Exception("Data SKPD untuk id_kel {$idKel} tidak ditemukan.");
        }

        $pejabat = Pejabat::with(['skpd.kecamatan', 'jabatan'])
            ->where('id_skpd', $idKel)
            ->first();

        if (!$pejabat) {
            throw new \Exception("Data pejabat penandatangan untuk SKPD {$idKel} belum disetting.");
        }

        $tahun = '';
        if (!empty($surat->tgl_surat)) {
            try {
                $tahun = Carbon::parse($surat->tgl_surat)->format('Y');
            } catch (\Throwable $e) {
                $tahun = date('Y');
            }
        } else {
            $tahun = date('Y');
        }

        $kdJenis = trim((string) ($surat->kd_jenis_surat ?? ''));
        $noUrut = trim((string) ($surat->no_urut_surat ?? ''));
        $kodeInstansi = trim((string) ($skpd->instansi_kode ?? ''));

        return $kdJenis . '/' . $noUrut . '/' . $kodeInstansi . '/' . $tahun;
    }
}