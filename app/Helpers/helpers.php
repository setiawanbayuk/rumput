<?php

use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Paginate a Laravel Collection manually.
 *
 * @param Collection $items
 * @param int $perPage
 * @param string $pageName
 * @return LengthAwarePaginator
 */
if (! function_exists('paginate_collection')) {
    function paginate_collection(Collection $items, int $perPage = 10, string $pageName = 'page')
    {
        $page = LengthAwarePaginator::resolveCurrentPage($pageName);

        $total = $items->count();
        $results = $items->forPage($page, $perPage)->values();

        return new LengthAwarePaginator(
            $results,
            $total,
            $perPage,
            $page,
            [
                'path'     => request()->url(),
                'query'    => request()->query(),
                'pageName' => $pageName,
            ]
        );
    }
}

if (! function_exists('decode_json_data')) {
    /**
     * Membaca data lama dan baru dengan aman.
     * Support:
     * - array dari Eloquent cast
     * - JSON object normal: {"name":"..."}
     * - double encoded JSON: "{\"name\":\"...\"}"
     * - serialize lama: a:1:{s:4:"name";s:3:"...";}
     */
    function decode_json_data($value): array
    {
        if ($value === null || $value === '') {
            return [];
        }

        if (is_array($value)) {
            return $value;
        }

        if (is_object($value)) {
            $objectDecoded = json_decode(json_encode($value), true);
            return is_array($objectDecoded) ? $objectDecoded : [];
        }

        if (! is_string($value)) {
            return [];
        }

        $value = trim($value);

        if ($value === '') {
            return [];
        }

        $current = $value;

        for ($i = 0; $i < 3; $i++) {
            $decoded = json_decode($current, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                break;
            }

            if (is_array($decoded)) {
                return $decoded;
            }

            if (is_string($decoded)) {
                $current = trim($decoded);
                continue;
            }

            break;
        }

        $unserialized = @unserialize($value);

        if ($unserialized !== false && is_array($unserialized)) {
            return $unserialized;
        }

        return [];
    }
}

if (! function_exists('normalize_json_data')) {
    /**
     * Normalisasi sebelum disimpan ke kolom yang memakai cast array.
     * Return array, bukan string JSON, supaya tidak double encoded.
     */
    function normalize_json_data($value): array
    {
        return decode_json_data($value);
    }
}

if (! function_exists('resident_data_order')) {
    /**
     * Merapikan urutan field residents.data agar hasil input dari web dan mobile sama.
     *
     * Urutan ini mengikuti struktur datapemohon dari form website:
     * kk, name, gender, gender_nm, status_kwn, status_kwn_nm, kewarganegaraan,
     * kewarganegaraan_nm, tempat_lhr, tgl_lhr, agama, agama_nm, pendidikan,
     * pendidikan_nm, pekerjaan, pekerjaan_nm, provinsi, provinsi_nm, kabko,
     * kabko_nm, kecamatan, kecamatan_nm, kelurahan, kelurahan_nm, rw, rw_nm,
     * rt, rt_nm, alamat.
     */
    function resident_data_order($value): array
    {
        $data = decode_json_data($value);

        $orderedKeys = [
            'kk',
            'name',
            'gender',
            'gender_nm',
            'status_kwn',
            'status_kwn_nm',
            'kewarganegaraan',
            'kewarganegaraan_nm',
            'tempat_lhr',
            'tgl_lhr',
            'agama',
            'agama_nm',
            'pendidikan',
            'pendidikan_nm',
            'pekerjaan',
            'pekerjaan_nm',
            'provinsi',
            'provinsi_nm',
            'kabko',
            'kabko_nm',
            'kecamatan',
            'kecamatan_nm',
            'kelurahan',
            'kelurahan_nm',
            'rw',
            'rw_nm',
            'rt',
            'rt_nm',
            'alamat',
        ];

        $ordered = [];

        foreach ($orderedKeys as $key) {
            $ordered[$key] = array_key_exists($key, $data) ? $data[$key] : null;
        }

        // Jaga-jaga kalau mobile mengirim field tambahan, jangan dibuang.
        foreach ($data as $key => $value) {
            if (! array_key_exists($key, $ordered)) {
                $ordered[$key] = $value;
            }
        }

        return $ordered;
    }
}
