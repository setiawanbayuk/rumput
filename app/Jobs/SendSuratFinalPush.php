<?php

namespace App\Jobs;

use App\Models\EsuketPushDelivery;
use App\Models\EsuketPushDevice;
use App\Models\SuratPengajuan;
use App\Models\User;
use App\Services\EsuketSsoPushService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class SendSuratFinalPush implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 30;

    public function __construct(
        public int $suratId,
        public string $eventName = 'tte'
    ) {}

    public function backoff(): array
    {
        return [10, 60, 180];
    }

    public function handle(EsuketSsoPushService $gateway): void
    {
        if (!config('esuket_push.enabled', false)) {
            return;
        }

        $surat = SuratPengajuan::find($this->suratId);
        if (!$surat || !$this->stillFinished($surat)) {
            return;
        }

        // Identitas pemohon berasal dari data surat dan NIK akun login mobile.
        // JANGAN mengambil user_id dari payload atau me-broadcast ke semua warga.
        $user = User::query()->where('nik', $surat->nik)->first();
        if (!$user) {
            Log::info('ESUKET push: pemohon tidak memiliki akun pengguna', ['surat_id' => $surat->id]);
            return;
        }

        $devices = EsuketPushDevice::where('user_id', $user->id)->get();
        $kind = strtoupper((string) $surat->jenis_surat);
        $signer = $this->eventName === 'tte_camat' ? 'Camat' : 'Lurah';
        $message = $this->eventName === 'manual'
            ? "Pengajuan surat {$kind} Anda telah selesai. Silakan buka E-SUKET untuk melihat hasilnya."
            : "Surat {$kind} Anda telah ditandatangani {$signer}. Silakan buka E-SUKET untuk melihat progresnya.";
        $data = [
            'route' => $this->mobileRoute((string) $surat->jenis_surat),
            'id' => (string) $surat->id,
            'state' => 'success',
            'event' => $this->eventName === 'manual' ? 'surat_selesai' : $this->eventName,
            'jenis_surat' => strtolower($kind),
        ];

        foreach ($devices as $device) {
            $delivery = EsuketPushDelivery::firstOrCreate(
                [
                    'surat_pengajuan_id' => $surat->id,
                    'push_device_id' => $device->id,
                    'event_name' => $this->eventName,
                ],
                ['status' => 'pending', 'attempts' => 0]
            );

            // Mencegah notifikasi ganda ketika job di-retry.
            if ($delivery->sent_at !== null || $delivery->status === 'permanent_fail') {
                continue;
            }

            $delivery->increment('attempts');

            try {
                $result = $gateway->send($device->fcm_token, $message, $data);
                if ($result['success']) {
                    $delivery->update([
                        'status' => 'sent',
                        'sent_at' => now(),
                        'http_status' => $result['status'],
                        'error_code' => null,
                    ]);
                    Log::info('ESUKET push terkirim ke gateway', [
                        'surat_id' => $surat->id,
                        'device_id' => $device->id,
                        'http_status' => $result['status'],
                    ]);
                    continue;
                }

                $delivery->update([
                    'status' => $result['retryable'] ? 'retry' : 'permanent_fail',
                    'http_status' => $result['status'],
                    'error_code' => 'gateway_rejected',
                ]);
                if ($result['retryable']) {
                    throw new \RuntimeException('SSO push sementara gagal: HTTP ' . $result['status']);
                }

                Log::warning('ESUKET push ditolak gateway', [
                    'surat_id' => $surat->id,
                    'device_id' => $device->id,
                    'http_status' => $result['status'],
                ]);
            } catch (\Throwable $e) {
                // Jangan log token FCM, NIK, isi dokumen, atau payload lengkap.
                if ($delivery->status !== 'permanent_fail') {
                    $delivery->update(['status' => 'retry', 'error_code' => 'request_failed']);
                    Log::warning('ESUKET push gagal; antrean akan mencoba ulang', [
                        'surat_id' => $surat->id,
                        'device_id' => $device->id,
                        'exception_type' => get_class($e),
                    ]);
                    throw $e;
                }
            }
        }
    }

    private function stillFinished(SuratPengajuan $surat): bool
    {
        $variable = is_array($surat->variable) ? $surat->variable : [];
        $isManual = !empty($variable['manual_signature'])
            || (($variable['signature_mode'] ?? null) === 'manual');

        if ($this->eventName === 'manual') {
            return $isManual && !empty($variable['bukti_ttd_basah']);
        }
        if (!in_array($this->eventName, ['tte', 'tte_lurah', 'tte_camat'], true) || $isManual || empty($surat->file)) {
            return false;
        }

        $status = (int) $surat->status;
        $isSktm = strtolower((string) $surat->jenis_surat) === 'sktm';
        if ($this->eventName === 'tte_lurah') {
            return $isSktm
                ? in_array($status, [4, 11, 8, 9, 5], true)
                : in_array($status, [4, 5], true);
        }
        if ($this->eventName === 'tte_camat') {
            return $isSktm && in_array($status, [9, 5], true);
        }

        return $isSktm
            ? in_array($status, [9, 5], true)
            : in_array($status, [4, 5], true);
    }

    private function mobileRoute(string $jenis): string
    {
        return (string) (DB::table('jenis_surats')
            ->where('jenis', strtolower(trim($jenis)))
            ->value('push_route') ?? '');
    }
}
