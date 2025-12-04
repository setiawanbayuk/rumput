<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Skpd;
use App\Models\RtRw;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request; // <-- Tambahkan jika belum ada
use Illuminate\Http\JsonResponse; // <-- Tambahkan ini
use Illuminate\Auth\Events\Registered; // <-- Tambahkan jika belum ada



class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'nik' => ['required', 'string', 'size:16', 'unique:users'],
            'phone' => ['required', 'numeric', 'digits_between:10,13'],
            'id_instansi' => ['required', 'string'],
            'id_rw' => ['required', 'string'],
            'id_rt' => ['required', 'string'],
            'password' => ['required', Password::min(8)->mixedCase()->numbers()->symbols()->uncompromised(), 'confirmed'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {

        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'nik' => $data['nik'],
            'phone' => $data['phone'],
            'role_id' => 2,
            'id_instansi' => $data['id_instansi'],
            'id_rw'    => $data['id_rw'],
            'id_rt'    => $data['id_rt'], 
            'password' => Hash::make($data['password']),
        ]);
    }

    /**
     * Handle a registration request FOR MOBILE API.
     * Menangani permintaan registrasi KHUSUS DARI API MOBILE.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function registermobile(Request $request): JsonResponse
    {
        try {
            // 1. Validasi data menggunakan validator yang sudah ada
            $this->validator($request->all())->validate();

            // 2. Buat pengguna baru menggunakan method create yang sudah ada
            event(new Registered($user = $this->create($request->all())));

            // 3. Kembalikan respons JSON sukses
            // (Kita tidak perlu login otomatis pengguna di API)
            return response()->json(['message' => 'Registrasi berhasil!'], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Tangani error validasi
            Log::error('Validation Error (registermobile): ', $e->errors());
            // Kembalikan error validasi pertama agar jelas di Flutter
            $firstError = collect($e->errors())->first()[0] ?? 'Data tidak valid.';
            return response()->json(['message' => $firstError, 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            // Tangani error tak terduga lainnya
            Log::error('General Error (registermobile): ' . $e->getMessage());
            return response()->json(['message' => 'Terjadi kesalahan pada server.'], 500);
        }
    }
}
