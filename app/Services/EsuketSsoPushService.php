<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class EsuketSsoPushService
{
    /**
     * SSO gateway menerima FCM token milik device; tidak dikirim ke frontend lain.
     * TLS verification TETAP ON. Throw pada gangguan jaringan/HTTP transient.
     *
     * @return array{success: bool, status: int, retryable: bool}
     */
    public function send(string $fcmToken, string $message, array $data): array
    {
        $url = (string) config('esuket_push.url');

        if (!str_starts_with(strtolower($url), 'https://')) {
            throw new \RuntimeException('Push URL wajib HTTPS.');
        }

        $payload = [
            'client_name' => (string) config('esuket_push.client_name', 'ESUKET'),
            'route' => (string) config('esuket_push.mobile_route', ''),
            'param_id' => (string) ($data['id'] ?? ''),
            'title' => (string) config('esuket_push.title', 'E-SUKET Kota Kediri'),
            'body' => $message,
            'data' => $data,
            'state' => 'success',
            'fcm_token' => $fcmToken,
        ];

        $request = Http::acceptJson()
            ->asJson()
            ->timeout(max(2, (int) config('esuket_push.timeout', 10)));

        // Hanya bila pihak SSO mensyaratkan Bearer token.
        $secret = trim((string) config('esuket_push.token', ''));
        if ($secret !== '') {
            $request = $request->withToken($secret);
        }

        $response = $request->post($url, $payload);
        $body = $response->json();
        $declined = is_array($body) && array_key_exists('success', $body) && $body['success'] === false;
        $success = $response->successful() && !$declined;

        return [
            'success' => $success,
            'status' => $response->status(),
            'retryable' => $response->serverError() || $response->status() === 429,
        ];
    }
}
