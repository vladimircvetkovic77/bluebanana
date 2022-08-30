<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Builder;

/**
 * Trait IsOrderable
 * |---------------------------------------------------------------------------------------
 * This trait is used to order models by a column ORDER which needs to exist in the model.
 * |---------------------------------------------------------------------------------------
 */

trait IsOrderable
{
    public function scopeOrdered(Builder $builder, $direction = 'asc')
    {
        $builder->orderBy('order', $direction);
    }
}
