<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EsuketPushDevice;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class EsuketDeviceTokenController extends Controller
{
    /**
     * Dipanggil Android/iOS setelah pengguna LOGIN dan mendapatkan token FCM.
     * user_id SELALU dari Sanctum, bukan dari body request.
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'device_id' => ['required', 'string', 'max:120'],
            'platform' => ['required', Rule::in(['android', 'ios'])],
            'fcm_token' => ['required', 'string', 'min:20', 'max:4096'],
        ]);

        $hash = hash('sha256', $data['fcm_token']);
        $userId = (int) $request->user()->getAuthIdentifier();

        DB::transaction(function () use ($hash, $data, $userId) {
            // Ketika token FCM pindah akun, jangan kirim notifikasi akun lama.
            EsuketPushDevice::where('token_hash', $hash)
                ->where('user_id', '!=', $userId)
                ->delete();

            // Token FCM berubah ketika Firebase me-refresh token perangkat.
            // Gunakan pasangan user_id + device_id agar token lama diganti.
            EsuketPushDevice::where('token_hash', $hash)
                ->where('user_id', $userId)
                ->where('device_id', '!=', $data['device_id'])
                ->delete();

            EsuketPushDevice::updateOrCreate(
                ['user_id' => $userId, 'device_id' => $data['device_id']],
                [
                    'platform' => $data['platform'],
                    'fcm_token' => $data['fcm_token'],
                    'token_hash' => $hash,
                ]
            );
        });

        return response()->json([
            'success' => true,
            'message' => 'Perangkat berhasil didaftarkan untuk notifikasi E-SUKET.',
            'platform' => $data['platform'],
        ]);
    }

    /** Panggil sebelum logout; perangkat lain tidak ikut terhapus. */
    public function destroy(Request $request): JsonResponse
    {
        $data = $request->validate([
            'device_id' => ['required', 'string', 'max:120'],
        ]);

        EsuketPushDevice::where('user_id', $request->user()->getAuthIdentifier())
            ->where('device_id', $data['device_id'])
            ->delete();

        return response()->json(['success' => true, 'message' => 'Notifikasi perangkat ini dinonaktifkan.']);
    }
}
