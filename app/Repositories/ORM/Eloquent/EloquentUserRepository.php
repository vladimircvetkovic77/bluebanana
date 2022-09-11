<?php

namespace App\Repositories\ORM\Eloquent;

use App\Models\User;
use App\Repositories\ORM\Eloquent\EloquentAbstractRepository;
use App\Repositories\ORM\Contracts\OrmUserRepositoryInterface;

class EloquentUserRepository extends EloquentAbstractRepository implements OrmUserRepositoryInterface
{
    public function entity(): string
    {
        return User::class;
    }
}
