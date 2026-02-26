<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Http\Request;

trait HandlesIncludes
{
    /**
     * Parse allowed includes from query string.
     *
     * @param  array<string, string>  $allowed
     * @return array<int, string>
     */
    protected function parseIncludes(Request $request, array $allowed): array
    {
        $raw = (string) $request->query('includes', '');
        if ($raw === '') {
            return [];
        }

        $requested = collect(explode(',', $raw))
            ->map(fn ($value) => trim($value))
            ->filter()
            ->unique();

        return $requested
            ->filter(fn ($value) => array_key_exists($value, $allowed))
            ->map(fn ($value) => $allowed[$value])
            ->values()
            ->all();
    }
}
