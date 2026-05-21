<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rules\Password;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Auth\Events\Registered;

class RegisterController extends Controller
{
    use RegistersUsers;

    protected $redirectTo = '/home';

    public function __construct()
    {
        // Middleware guest hanya dipakai untuk proses register.
        // GET list/detail/profile dibuat publik agar mudah dites dari Postman/mobile.
        $this->middleware('guest')->only(['registermobile']);
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name'         => ['required', 'string', 'max:255'],
            'email'        => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'nik'          => ['required', 'string', 'size:16', 'unique:users,nik'],
            'phone'        => ['required', 'numeric', 'digits_between:10,13'],
            'id_instansi'  => ['required', 'string'],
            'id_rw'        => ['required', 'string'],
            'id_rt'        => ['required', 'string'],
            'password'     => ['required', Password::min(8)->mixedCase()->numbers()->symbols()->uncompromised(), 'confirmed'],

            // Mobile bisa mengirim salah satu nama field file berikut.
            'foto'         => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'foto_profile' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'profile'      => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);
    }

    protected function create(array $data)
    {
        return User::create([
            'name'         => $data['name'],
            'email'        => $data['email'],
            'nik'          => $data['nik'],
            'phone'        => $data['phone'],
            'role_id'      => 2, // role warga/mobile
            'id_instansi'  => $data['id_instansi'],
            'id_rw'        => $data['id_rw'],
            'id_rt'        => $data['id_rt'],
            'password'     => Hash::make($data['password']),
        ]);
    }

    public function registermobile(Request $request): JsonResponse
    {
        try {
            $this->validator($request->all())->validate();

            $user = $this->create($request->all());

            $fotoPath = $this->uploadFotoProfil($request);

            if ($fotoPath) {
                $this->saveFotoProfil($user, $fotoPath);
            }

            event(new Registered($user));

            return response()->json([
                'status'  => true,
                'message' => 'Registrasi berhasil!',
                'data'    => $this->formatUserMobile($user->fresh()),
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation Error (registermobile): ', $e->errors());

            $firstError = collect($e->errors())->first()[0] ?? 'Data tidak valid.';

            return response()->json([
                'status'  => false,
                'message' => $firstError,
                'errors'  => $e->errors(),
            ], 422);

        } catch (\Exception $e) {
            Log::error('General Error (registermobile): ' . $e->getMessage());

            return response()->json([
                'status'  => false,
                'message' => 'Terjadi kesalahan pada server.',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * GET semua warga yang daftar dari mobile.
     * Endpoint: GET /api/registermobile/list
     */
    public function listRegisterMobile(Request $request): JsonResponse
    {
        try {
            $query = User::query()
                ->where('role_id', 2)
                ->orderByDesc('id');

            // Optional filter untuk Postman/mobile:
            // /api/registermobile/list?search=fani
            if ($request->filled('search')) {
                $search = $request->get('search');
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%')
                        ->orWhere('nik', 'like', '%' . $search . '%')
                        ->orWhere('phone', 'like', '%' . $search . '%');
                });
            }

            // Optional pagination:
            // /api/registermobile/list?per_page=10
            $perPage = (int) $request->get('per_page', 0);

            if ($perPage > 0) {
                $users = $query->paginate($perPage);

                return response()->json([
                    'status'  => true,
                    'message' => 'Data warga berhasil diambil.',
                    'data'    => collect($users->items())->map(fn ($user) => $this->formatUserMobile($user))->values(),
                    'meta'    => [
                        'current_page' => $users->currentPage(),
                        'per_page'     => $users->perPage(),
                        'total'        => $users->total(),
                        'last_page'    => $users->lastPage(),
                    ],
                ], 200);
            }

            $users = $query->get()->map(fn ($user) => $this->formatUserMobile($user))->values();

            return response()->json([
                'status'  => true,
                'message' => 'Data warga berhasil diambil.',
                'data'    => $users,
            ], 200);

        } catch (\Exception $e) {
            Log::error('General Error (listRegisterMobile): ' . $e->getMessage());

            return response()->json([
                'status'  => false,
                'message' => 'Terjadi kesalahan saat mengambil data warga.',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * GET detail warga berdasarkan ID user.
     * Endpoint: GET /api/registermobile/detail/{id}
     */
    public function detailRegisterMobile($id): JsonResponse
    {
        return $this->profile($id);
    }

    /**
     * GET profil warga berdasarkan ID user.
     * Endpoint: GET /api/profile/{id}
     */
    public function profile($id): JsonResponse
    {
        try {
            $user = User::where('role_id', 2)->where('id', $id)->first();

            if (!$user) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Data profil warga tidak ditemukan.',
                ], 404);
            }

            return response()->json([
                'status'  => true,
                'message' => 'Data profil warga berhasil diambil.',
                'data'    => $this->formatUserMobile($user),
            ], 200);

        } catch (\Exception $e) {
            Log::error('General Error (profile): ' . $e->getMessage());

            return response()->json([
                'status'  => false,
                'message' => 'Terjadi kesalahan saat mengambil profil warga.',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    private function uploadFotoProfil(Request $request): ?string
    {
        if ($request->hasFile('foto')) {
            return $request->file('foto')->store('profile', 'public');
        }

        if ($request->hasFile('foto_profile')) {
            return $request->file('foto_profile')->store('profile', 'public');
        }

        if ($request->hasFile('profile')) {
            return $request->file('profile')->store('profile', 'public');

        }

        return null;
    }

    private function saveFotoProfil(User $user, string $fotoPath): void
    {
        if (Schema::hasColumn('users', 'foto')) {
            $user->foto = $fotoPath;
        } elseif (Schema::hasColumn('users', 'foto_profile')) {
            $user->foto_profile = $fotoPath;
        } elseif (Schema::hasColumn('users', 'profile_photo_path')) {
            $user->profile_photo_path = $fotoPath;
        }

        $user->save();
    }

    private function getFotoPath(User $user): ?string
    {
        if (Schema::hasColumn('users', 'foto')) {
            return $user->foto;
        }

        if (Schema::hasColumn('users', 'foto_profile')) {
            return $user->foto_profile;
        }

        if (Schema::hasColumn('users', 'profile_photo_path')) {
            return $user->profile_photo_path;
        }

        return null;
    }

    private function formatUserMobile(User $user): array
    {
        $fotoDb = $this->getFotoPath($user);

        return [
            'id'           => $user->id,
            'name'         => $user->name,
            'email'        => $user->email,
            'nik'          => $user->nik,
            'phone'        => $user->phone,
            'role_id'      => $user->role_id,
            'id_instansi'  => $user->id_instansi,
            'id_rw'        => $user->id_rw,
            'id_rt'        => $user->id_rt,

            // Path yang tersimpan di database, contoh: profile/xxx.jpg
            'foto'         => $fotoDb,

            // URL lengkap untuk ImageView/mobile, contoh: http://domain/storage/profile/xxx.jpg
            'foto_url'     => $fotoDb ? asset('storage/' . $fotoDb) : null,

            'created_at'   => optional($user->created_at)->toDateTimeString(),
            'updated_at'   => optional($user->updated_at)->toDateTimeString(),
        ];
    }
}
