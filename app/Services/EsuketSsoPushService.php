<?php

namespace App\Services;

use App\Traits\NotifiableTrait;

class EsuketSsoPushService
{
    use NotifiableTrait;

    /**
     * SSO gateway menerima FCM token milik device; tidak dikirim ke frontend lain.
     * TLS verification TETAP ON. Throw pada gangguan jaringan/HTTP transient.
     *
     * @return array{success: bool, status: int, retryable: bool}
     */
    public function send(string $fcmToken, string $message, array $data): array
    {
        $result = $this->sendNotification($fcmToken, $message, $data);
        if (isset($result['error'])) {
            throw new \RuntimeException('SSO push tidak dapat dihubungi: ' . $result['error']);
        }

        return $result;
    }
}
