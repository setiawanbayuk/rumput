<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Login Super Admin' }} - E-SUKET</title>
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <style>
        body {
            min-height: 100vh;
            background: radial-gradient(circle at top left, rgba(174,160,122,.28), transparent 32%), linear-gradient(135deg, #16213b, #0f172a 58%, #111827);
            font-family: 'Poppins', system-ui, -apple-system, BlinkMacSystemFont, sans-serif;
        }
        .super-login-wrap { min-height: 100vh; display: grid; place-items: center; padding: 24px; }
        .super-login-card {
            width: min(1040px, 100%);
            overflow: hidden;
            border: 1px solid rgba(255,255,255,.14);
            border-radius: 28px;
            background: rgba(255,255,255,.94);
            box-shadow: 0 28px 70px rgba(0,0,0,.28);
        }
        .super-panel {
            background: linear-gradient(145deg, #1f2937, #111827);
            color: white;
            min-height: 100%;
            padding: 42px;
        }
        .super-badge {
            display: inline-flex;
            gap: 8px;
            align-items: center;
            border: 1px solid rgba(255,255,255,.20);
            border-radius: 999px;
            padding: 8px 14px;
            color: #f8e7b0;
            background: rgba(255,255,255,.08);
            font-size: 13px;
            font-weight: 700;
        }
        .super-title { font-size: clamp(32px, 5vw, 52px); font-weight: 900; letter-spacing: -.04em; line-height: .96; margin-top: 26px; }
        .super-subtitle { color: rgba(255,255,255,.76); font-size: 15px; line-height: 1.8; margin-top: 18px; }
        .login-form-area { padding: 42px; }
        .form-control { border-radius: 14px; padding: 12px 14px; border-color: #e5e7eb; }
        .form-control:focus { border-color: #AEA07A; box-shadow: 0 0 0 .2rem rgba(174,160,122,.18); }
        .btn-super { border: 0; border-radius: 14px; padding: 13px 18px; font-weight: 800; background: #1f2937; color: white; }
        .btn-super:hover { background: #111827; color: white; }
        .soft-note { border-radius: 16px; background: #f8fafc; border: 1px solid #eef2f7; padding: 14px; color: #64748b; font-size: 13px; }
    </style>
</head>
<body>
    <div class="super-login-wrap">
        <div class="super-login-card">
            <div class="row g-0">
                <div class="col-lg-6">
                    <div class="super-panel">
                        <div class="super-badge">SUPER ADMIN CONTROL CENTER</div>
                        <div class="super-title">Rumah Kontrol E-SUKET</div>
                        <p class="super-subtitle mb-0">
                            Login khusus role Super Admin untuk memantau semua surat, kontrol status pengajuan,
                            menjalankan TTE sesuai kewenangan, dan mengelola seluruh akun sistem.
                        </p>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="login-form-area">
                        <div class="mb-4">
                            <h3 class="fw-black mb-1" style="font-weight:900">Login Super Admin</h3>
                            <p class="text-muted mb-0">Gunakan akun dengan <b>role_id 7</b>.</p>
                        </div>

                        @if ($errors->any())
                            <div class="alert alert-danger rounded-4">
                                {{ $errors->first() }}
                            </div>
                        @endif
                        @if (session('error'))
                            <div class="alert alert-danger rounded-4">{{ session('error') }}</div>
                        @endif

                        <form method="POST" action="{{ route('login') }}">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Email</label>
                                <input type="email" name="email" value="{{ old('email') }}" class="form-control" required autofocus>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Password</label>
                                <input type="password" name="password" class="form-control" required>
                            </div>
                            <button class="btn btn-super w-100" type="submit">Masuk Super Admin</button>
                        </form>

                        <div class="soft-note mt-4">
                            Area ini tidak menghapus alur lama. Role Admin, Sekkel, Lurah, Sekcam, dan Camat tetap berjalan seperti sebelumnya.
                        </div>
                        <div class="mt-3">
                            <a href="{{ route('login') }}" class="text-decoration-none text-muted">Masuk lewat login umum</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
