<?php

namespace App\Http\Controllers\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

trait Sortable
{
    /**
     * Apply sorting to the query based on request parameters.
     *
     * @param Builder $query
     * @param Request $request
     * @param array $allowedColumns
     * @param string $defaultSort
     * @param string $defaultDirection
     * @return Builder
     */
    protected function applySorting(Builder $query, Request $request, array $allowedColumns = [], string $defaultSort = 'created_at', string $defaultDirection = 'desc')
    {
        $sort = $request->input('sort', $defaultSort);
        $direction = $request->input('direction', $defaultDirection);

        if (!in_array($direction, ['asc', 'desc'])) {
            $direction = 'desc';
        }

        if (in_array($sort, $allowedColumns)) {
            return $query->orderBy($sort, $direction);
        }

        return $query->orderBy($defaultSort, $defaultDirection);
    }
}
