<?php
namespace Rnr\Alice\Fillers;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

abstract class SingleFiller extends AbstractFiller
{
    /**
     * @param Model $data
     * @param Relation $relation
     * @return string
     */
    public function handle($data, Relation $relation)
    {
        return $this->prefixer->getVariable($data);
    }
}