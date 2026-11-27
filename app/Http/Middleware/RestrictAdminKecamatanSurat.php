<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RestrictAdminKecamatanSurat
{
    public function handle(Request $request, Closure $next): Response
    {
        if ((int) optional($request->user())->role_id === 10) {
            $action = (string) optional($request->route())->getActionName();
            $method = str_contains($action, '@') ? substr($action, strrpos($action, '@') + 1) : '';
            abort_unless(in_array($method, ['index', 'naik', 'turunkan', 'preview', 'cetak'], true),
                403, 'Admin Kecamatan hanya dapat memproses SKTM dan melihat dokumen.');
        }
        return $next($request);
    }
}
