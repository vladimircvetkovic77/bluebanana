<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait IsLive
{
    public function scopeLive(Builder $builder)
    {
        return $builder->where('live', true);
    }
}
