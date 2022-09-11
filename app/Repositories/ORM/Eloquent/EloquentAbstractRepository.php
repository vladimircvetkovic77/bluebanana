<?php

namespace App\Repositories\ORM\Eloquent;

use App\Repositories\Exceptions\NoEntityDefined;
use App\Repositories\ORM\Criteria\CriteriaInterface;
use App\Repositories\ORM\Contracts\OrmAbstractRepositoryInterface;
use Illuminate\Contracts\Container\BindingResolutionException;

abstract class EloquentAbstractRepository implements
    OrmAbstractRepositoryInterface,
    CriteriaInterface
{
    protected $entity;

    /**
     * @throws BindingResolutionException
     * @throws NoEntityDefined
     */
    public function __construct()
    {
        $this->entity = $this->resolveEntity();
    }

    public function all()
    {
        return $this->entity->get();
    }

    public function find($id)
    {
        return $this->entity->findOrFail($id);
    }

    public function findWhere($column, $value)
    {
        return $this->entity->where($column, $value)->get();
    }

    public function findWhereFirst($column, $value)
    {
        return $this->entity->where($column, $value)->firstOrFail();
    }

    public function paginate($perPage = 10)
    {
        return $this->entity->paginate($perPage);
    }

    public function create(array $properties)
    {
        return $this->entity->create($properties);
    }

    public function update($id, array $properties)
    {
        return $this->find($id)->update($properties);
    }

    public function delete($id)
    {
        return $this->find($id)->delete();
    }

    public function withCriteria(array $criteria): static
    {
        foreach ($criteria as $criterion) {
            //criterion je objekat koji je definisan sa new CriteriaClass
            //kao da je prethodno napisano $criterion = new CriterionClass
            $this->entity = $criterion->apply($this->entity);
        }
        return $this;
    }

    /**
     * @throws BindingResolutionException
     * @throws NoEntityDefined
     */
    public function resolveEntity()
    {
        if (!method_exists($this, 'entity')) {
            throw new NoEntityDefined();
        }
        return app()->make($this->entity());
    }
}
