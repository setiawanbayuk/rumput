<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $token = $request->user()->createToken(env('APP_NAME'))->plainTextToken;
            return ['token' => $token];
        }
    }

    public function profile()
    {
        $data = User::find(Auth::id());
        return response()->json($data, 200);
    }

    // Method GET
    public function loginWithSSO(Request $request)
    {
        $ssoToken = $request->query('sso_token');

        $response = Http::withoutVerifying()->withToken($ssoToken)->get(env('APP_URL_SSO') . '/api/auth/profile');
        $userSSO = $response->json();
        if (is_null($userSSO['email'])) {
            return response()->json(['message' => 'Ooops, sepertinya kamu perlu login lagi!'], 401);
        }

        $findUserByEmail = User::where('email', $userSSO['email'])->first();
        if (!$findUserByEmail) {
            return response()->json(['message' => 'Ooops, kamu belum mempunyai akun pada aplikasi ini!'], 403);
        }

        $token = $findUserByEmail->createToken(env('APP_NAME'))->plainTextToken;

        return response()->json(['token' => $token]);
    }

    public function logout(Request $request){
        $user = $request->user();
        $user->tokens()->delete();
        
        return response()->json(['message' => 'Anda berhasil logut!']);
    }
}
