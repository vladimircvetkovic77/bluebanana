<?php

namespace App\Repositories\ORM\Criteria;

interface CriteriaInterface
{
    public function withCriteria(array $criteria);
}
