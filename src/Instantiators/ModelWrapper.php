<?php
namespace Rnr\Alice\Instantiators;

use Illuminate\Database\Eloquent\Model;

class ModelWrapper
{
    /** @var Model */
    private $model;

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
        return $this->model->save();
    }
}