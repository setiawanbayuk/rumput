<?php

return [
    // Aktifkan hanya setelah diuji dengan gateway SSO dan aplikasi mobile.
    'enabled' => (bool) env('ESUKET_PUSH_ENABLED', false),
    'url' => env('ESUKET_PUSH_URL', 'https://sso.kedirikota.go.id/api/push-notif'),
    'client_name' => env('ESUKET_PUSH_CLIENT_NAME', 'ESUKET'),
    'title' => env('ESUKET_PUSH_TITLE', 'E-SUKET Kota Kediri'),
    // Nama rute aplikasi ditetapkan oleh tim Android/iOS; kosong masih menampilkan notifikasi.
    'mobile_route' => env('ESUKET_PUSH_MOBILE_ROUTE', ''),
    'token' => env('ESUKET_PUSH_BEARER_TOKEN', ''),
    'timeout' => (int) env('ESUKET_PUSH_TIMEOUT', 10),
];
