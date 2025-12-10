<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Builder;

class Product extends Model
{
    protected $guarded = ['id'];


    #[Scope]
    protected function scopeLowStock(Builder $query): void
    {
        $query->where('stock', '<', 5);
    }
}
