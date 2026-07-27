<?php

namespace App\Helpers;

use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;

class SearchHelper
{
    /**
     * Apply fuzzy, multi-word, case-insensitive search filter to a query builder.
     *
     * @param QueryBuilder|EloquentBuilder $query
     * @param string|null $search
     * @param array $columns
     * @return QueryBuilder|EloquentBuilder
     */
    public static function applyFuzzySearch($query, ?string $search, array $columns)
    {
        if (empty($search) || empty($columns)) {
            return $query;
        }

        // Split search query into individual words for multi-word fuzzy matching
        $words = array_filter(preg_split('/\s+/', trim($search)));
        if (empty($words)) {
            return $query;
        }

        foreach ($words as $word) {
            $like = '%' . mb_strtolower($word) . '%';
            $query->where(function ($q) use ($columns, $like) {
                foreach ($columns as $index => $column) {
                    if ($index === 0) {
                        $q->whereRaw("LOWER({$column}) LIKE ?", [$like]);
                    } else {
                        $q->orWhereRaw("LOWER({$column}) LIKE ?", [$like]);
                    }
                }
            });
        }

        return $query;
    }
}
