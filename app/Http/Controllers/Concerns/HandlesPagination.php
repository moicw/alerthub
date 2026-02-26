<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

trait HandlesPagination
{
    /**
     * Paginate using cursor or offset based on request.
     */
    protected function paginate(Builder $query, Request $request, int $defaultPerPage = 15)
    {
        $perPage = (int) $request->query('per_page', $defaultPerPage);
        $perPage = max(1, min($perPage, 100));

        if ($request->filled('cursor')) {
            return $query->cursorPaginate($perPage)->withQueryString();
        }

        return $query->paginate($perPage)->withQueryString();
    }
}
