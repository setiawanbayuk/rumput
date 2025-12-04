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

        // total data
        $total = $items->count();

        // slice data untuk halaman ini
        $results = $items->forPage($page, $perPage)->values();

        return new LengthAwarePaginator(
            $results,
            $total,
            $perPage,
            $page,
            [
                'path'     => request()->url(),
                'query'    => request()->query(),   // tetap bawa ?q=
                'pageName' => $pageName,
            ]
        );
    }
}

