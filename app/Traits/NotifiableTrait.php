<?php

namespace App\Traits;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

trait NotifiableTrait
{
    /**
     * Kirim push notification ke SSO untuk satu token FCM.
     *
     * @return array{success: bool, status: int, retryable: bool, error?: string}
     */
    public function sendNotification(string $to, string $message, array $data = []): array
    {
        $url = (string) config('esuket_push.url', 'https://sso.kedirikota.go.id/api/push-notif');

        if (!str_starts_with(strtolower($url), 'https://')) {
            throw new \RuntimeException('Push URL wajib HTTPS.');
        }

        $payload = [
            'client_name' => (string) config('esuket_push.client_name', config('app.name', 'ESUKET')),
            'route' => (string) ($data['route'] ?? ''),
            'param_id' => (string) ($data['id'] ?? ''),
            'title' => (string) config('esuket_push.title', config('app.name', 'ESUKET')),
            'body' => $message,
            'data' => $data,
            'state' => (string) ($data['state'] ?? ''),
            'fcm_token' => $to,
        ];

        try {
            $request = Http::acceptJson()->asJson()
                ->timeout(max(2, (int) config('esuket_push.timeout', 10)));

            $secret = trim((string) config('esuket_push.token', ''));
            if ($secret !== '') {
                $request = $request->withToken($secret);
            }

            $response = $request->post($url, $payload);
            $body = $response->json();
            $declined = is_array($body) && ($body['success'] ?? null) === false;

            return [
                'success' => $response->successful() && !$declined,
                'status' => $response->status(),
                'retryable' => $response->serverError() || $response->status() === 429,
            ];
        } catch (\Throwable $e) {
            // Token FCM dan payload lengkap tidak boleh masuk log.
            Log::warning('ESUKET push: permintaan ke gateway gagal', [
                'exception_type' => get_class($e),
                'surat_id' => $data['id'] ?? null,
            ]);

            return [
                'success' => false,
                'status' => 0,
                'retryable' => true,
                'error' => $e->getMessage(),
            ];
        }
    }
}
