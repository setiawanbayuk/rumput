<?php

namespace App\Observers;

use App\Jobs\SendSuratFinalPush;
use App\Models\SuratPengajuan;

class SuratPengajuanPushObserver
{
    public function updated(SuratPengajuan $surat): void
    {
        if (!config('esuket_push.enabled', false)) {
            return;
        }

        $jenis = strtolower(trim((string) $surat->jenis_surat));
        $variable = is_array($surat->variable) ? $surat->variable : [];
        $isManual = !empty($variable['manual_signature'])
            || (($variable['signature_mode'] ?? null) === 'manual');

        // Status 4 menandakan TTE Lurah; status 9 menandakan TTE Camat.
        // SKTM menerima notifikasi untuk kedua tanda tangan tersebut.
        if ($surat->wasChanged('status') && !$isManual && !empty($surat->file)) {
            $status = (int) $surat->status;
            $event = match (true) {
                $status === 4 => 'tte_lurah',
                $jenis === 'sktm' && $status === 9 => 'tte_camat',
                default => null,
            };
            if ($event !== null) {
                SendSuratFinalPush::dispatch((int) $surat->id, $event)
                    ->onQueue('esuket-push')
                    ->afterCommit();
            }
        }

        // Surat TTD basah: selesai setelah BUKTI diunggah (status boleh tetap 7/8).
        // Pengecekan nilai sebelumnya mencegah kirim ulang setiap edit variable JSON.
        if ($surat->wasChanged('variable') && $isManual && !empty($variable['bukti_ttd_basah'])) {
            $previous = $surat->getRawOriginal('variable');
            $previousData = is_array($previous) ? $previous : json_decode((string) $previous, true);
            $previousProof = is_array($previousData) ? ($previousData['bukti_ttd_basah'] ?? null) : null;

            if (empty($previousProof)) {
                SendSuratFinalPush::dispatch((int) $surat->id, 'manual')
                    ->onQueue('esuket-push')
                    ->afterCommit();
            }
        }
    }
}
