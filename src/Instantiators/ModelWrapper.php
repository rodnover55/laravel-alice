<?php
namespace Rnr\Alice\Instantiators;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasOneOrMany;
use Illuminate\Database\Eloquent\Relations\Relation;
use ReflectionMethod;
use ReflectionException;
use Rnr\Alice\Exceptions\RelationNotFoundException;

class ModelWrapper
{
    /** @var Model */
    private $model;

    /** @var array|self[]  */
    private $belongsTo = [];

    /** @var array|self[][] */
    private $many = [];

    /** @var array|self[][] */
    private $manyToMany = [];

    private $dirty = false;

    /**
     * @return Model
     */
    public function getModel(): Model
    {
        return $this->model;
    }

    /**
     * @param Model $model
     * @return $this
     */
    public function setModel(Model $model)
    {
        $this->model = $model;

        return $this;
    }

    public function __get($name)
    {
        return $this->model->{$name};
    }

    public function __set($name, $value)
    {
        $this->model->{$name} = $value;
    }

    function __isset($name)
    {
        return array_key_exists($name, $this->model->getAttributes());
    }

    public function save(HasOneOrMany $relation = null) {
        foreach ($this->belongsTo as $name => $id) {
            $model = $this->getRelationModel($name, $id);

            if ($model->isDirty()) {
                $model->save();
            }

            $this->getRelation($name)->associate($model->getModel());
        }

        $result = (empty($relation)) ? ($this->model->save()) : ($relation->save($this->model));
        $this->dirty = false;

        foreach ($this->many as $name => $models) {
            $relation = $this->getRelation($name);

            foreach ($models as $id) {
                $model = $this->getRelationModel($name, $id, true);

//                if ($model->isDirty()) {
                    $model->save($relation);
//                }
            }
        }

        foreach ($this->manyToMany as $name => $models) {
            $ids = [];

            foreach ($models as $id) {
                $model = $this->getRelationModel($name, $id);

                if ($model->isDirty()) {
                    $model->save();
                }

                $ids[] = $model->getModel()->getKey();
            }

            $this->getRelation($name)->sync($ids);
        }

        return $result;
    }

    public function hasBelongTo($name) {
        try {
            $relation = $this->getRelation($name);

            return $relation instanceof BelongsTo;
        } catch (RelationNotFoundException $e) {
            return false;
        }
    }

    public function hasMany($name) {
        try {
            $relation = $this->getRelation($name);

            return $relation instanceof HasOneOrMany;
        } catch (RelationNotFoundException $e) {
            return false;
        }
    }

    public function hasBelongsToMany($name) {
        try {
            $relation = $this->getRelation($name);

            return $relation instanceof BelongsToMany;
        } catch (RelationNotFoundException $e) {
            return false;
        }
    }

    /**
     * @param $name
     * @return BelongsToMany|BelongsTo|HasMany|HasOne
     * @throws RelationNotFoundException
     */
    public function getRelation($name) {
        try {
            $reflectionMethod = new ReflectionMethod($this->model, $name);

            $relation = $reflectionMethod->invoke($this->model, $name);

            if (!($relation instanceof Relation)) {
                throw $this->createException($name);
            }
        } catch (ReflectionException $e) {
            throw $this->createException($name);
        }

        return $relation;
    }

    public function createException($name, $e = null) {
        return new RelationNotFoundException("Relation '{$name}' not found.", 0, $e);
    }

    public function addBelongTo($relation, $object) {
        $this->belongsTo[$relation] = $object;
    }

    public function addMany($relation, array $objects) {
        $this->many[$relation] = ($this->many[$relation] ?? []) + $objects;
    }

    public function addOne($relation, $object) {
        $this->addMany($relation, [$object]);
    }

    public function addBelongToMany($relation, array $objects) {
        $this->manyToMany[$relation] = ($this->manyToMany[$relation] ?? []) + $objects;
    }

    public function isDirty() {
        return $this->dirty || $this->model->isDirty();
    }

    /**
     * @param $name
     * @param int|self $id
     * @param $dirty
     * @return self
     */
    public function getRelationModel($name, $id, $dirty = false) {
        if ($id instanceof self) {
            return $id;
        }

        /** @var Model $model */
        $model = $this->getModel()->{$name}()->getRelated()->newQuery()->find($id);

        $wrapper = new self();

        $wrapper->setModel($model);
        $wrapper->setDirty($dirty);

        return $wrapper;
    }

    /**
     * @param boolean $dirty
     * @return $this
     */
    public function setDirty(bool $dirty)
    {
        $this->dirty = $dirty;

        return $this;
    }


}