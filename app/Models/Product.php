<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $guarded = ['id'];


    #[Scope]
    protected function lowStock(Builder $query): void
    {
        $query->where('id', '>', 0);
    }
}
