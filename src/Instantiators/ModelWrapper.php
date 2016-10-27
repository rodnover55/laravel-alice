<?php
namespace Rnr\Alice\Instantiators;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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

    public function save() {
        foreach ($this->belongsTo as $name => $model) {
            if ($model->isDirty()) {
                $model->save();
            }

            $this->getRelation($name)->associate($model->getModel());
        }

        return $this->model->save();
    }

    public function hasBelongTo($name) {
        try {
            $relation = $this->getRelation($name);

            return $relation instanceof BelongsTo;
        } catch (RelationNotFoundException $e) {
            return false;
        }
    }

    /**
     * @param $name
     * @return BelongsTo
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

    public function isDirty() {
        return $this->model->isDirty();
    }
}