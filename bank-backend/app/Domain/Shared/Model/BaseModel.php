<?php

namespace App\Domain\Shared\Model;

use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Database\Eloquent\Model;

abstract class BaseModel extends Model
{
    public static function paginate(Builder $query, int $page = 1, int $pageSize = 10): Builder
    {
        if ($page > 0) {
            $page -= 1;
        }
        return $query->offset($page * $pageSize)->limit($pageSize);
    }
}
