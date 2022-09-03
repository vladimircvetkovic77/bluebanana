<?php

namespace App\Repositories\ORM\Criteria;

interface CriterionInterface
{
    public function apply($entity);
}
