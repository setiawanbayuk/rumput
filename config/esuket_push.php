<?php

return [
    'enabled' => (bool) env('ESUKET_PUSH_ENABLED', true),
    'url' => env('ESUKET_PUSH_URL', 'https://sso.kedirikota.go.id/api/push-notif'),
    'client_name' => env('ESUKET_PUSH_CLIENT_NAME', 'ESUKET'),
    'title' => env('ESUKET_PUSH_TITLE', 'E-SUKET Kota Kediri'),
    'token' => env('ESUKET_PUSH_BEARER_TOKEN', ''),
    'timeout' => (int) env('ESUKET_PUSH_TIMEOUT', 10),
];
