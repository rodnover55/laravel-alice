<?php
namespace Rnr\Alice\Instantiators;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
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

    function __get($name)
    {
        return $this->model->{$name};
    }

    function __set($name, $value)
    {
        $this->model->{$name} = $value;
    }

    public function save(HasOneOrMany $relation = null) {
        foreach ($this->belongsTo as $name => $model) {
            if ($model->isDirty()) {
                $model->save();
            }

            $this->getRelation($name)->associate($model->getModel());
        }

        $result = (empty($relation)) ? ($this->model->save()) : ($relation->save($this->model));

        foreach ($this->many as $name => $models) {
            $relation = $this->getRelation($name);

            foreach ($models as $model) {
                if ($model->isDirty()) {
                    $model->save($relation);
                }
            }
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

    /**
     * @param $name
     * @return HasMany
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

    public function isDirty() {
        return $this->model->isDirty();
    }
}