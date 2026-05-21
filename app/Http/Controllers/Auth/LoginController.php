<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
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
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    /**
     * Handle an authentication attempt.
     */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            if (auth()->user()->role_id == 2) {
                return redirect()->intended(route('warga'));
            }

            if ((int) auth()->user()->role_id === 7) {
                return redirect()->intended(route('super-admin.dashboard'));
            }

            return redirect()->intended(route('home'));
        }

        return back()->with('error', 'Email atau Password anda salah!');
    }

    public function sso(Request $request)
    {
        $request->session()->put('state', $state = Str::random(40));
        $query = http_build_query([
            'client_id' => env('OAUTH_CLIENT_ID'),
            'redirect_uri' => env('APP_URL') . '/callback',
            'response_type' => 'code',
            'scope' => 'view-user',
            'state' => $state,
        ]);
        return redirect(env('APP_URL_SSO') . '/oauth/authorize?' . $query);
    }

    public function callback(Request $request)
    {
        $state = $request->session()->pull('state');

        $response = Http::withoutVerifying()->asForm()->post(env('APP_URL_SPLP') . '/oauth/token', [
            'grant_type' => 'authorization_code',
            'client_id' => env('OAUTH_CLIENT_ID'),
            'client_secret' => env('OAUTH_CLIENT_SECRET'),
            'redirect_uri' => env('APP_URL') . '/callback',
            'code' => $request->code,
        ]);

        $request->session()->put($response->json());

        return redirect()->route('users.profile');
    }

    public function profile(Request $request)
    {
        $access_token = $request->session()->get('access_token');
        $response = Http::withoutVerifying()->withHeaders([
            'auth' => 'Bearer ' . $access_token
        ])->get(env('APP_URL_SPLP') . '/api/user');

        $userArray = $response->json();

        // return response()->json($userArray);

        try {
            $name = $userArray['name'];
            $email = $userArray['email'];
            $nik = $userArray['nik'];
            $nip = $userArray['nip'];
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Failed to get login information! Try again.'], 403);
        }

        $user = User::where('email', $email)->first();
        if (!$user) {
            return redirect()->route('register');
        }

        Auth::login($user);

        if ((int) $user->role_id === 7) {
            return redirect()->route('super-admin.dashboard');
        }

        return redirect()->route('home');
    }

    private function _logoutFromSSOServer()
    {
        $access_token = session()->get('access_token');
        $response = Http::withoutVerifying()->withHeaders([
            'auth' => 'Bearer ' . $access_token
        ])->get(env('APP_URL_SPLP') . '/api/logmeout');

        return $response;
    }

    public function logout(Request $request)
    {
        $this->_logoutFromSSOServer();

        $this->guard()->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        if ($response = $this->loggedOut($request)) {
            return $response;
        }

        return $request->wantsJson()
            ? new JsonResponse([], 204)
            : redirect('/');
    }

    protected function sendLoginResponse(Request $request)
    {
        $request->session()->regenerate();

        $this->clearLoginAttempts($request);

        if ($response = $this->authenticated($request, $this->guard()->user())) {
            return $response;
        }

        if ((int) $this->guard()->user()->role_id === 7) {
            return $request->wantsJson()
                ? new JsonResponse([], 204)
                : to_route('super-admin.dashboard');
        }

        return $request->wantsJson()
            ? new JsonResponse([], 204)
            : to_route('home');
    }
}
