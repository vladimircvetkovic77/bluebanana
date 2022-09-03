<?php

namespace App\Repositories\ORM\Eloquent\Criteria;

use App\Repositories\ORM\Criteria\CriterionInterface;

class IsLive implements CriterionInterface
{
    public function apply($entity)
    {
        return $entity->live();
    }
}
