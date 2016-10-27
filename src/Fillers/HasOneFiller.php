<?php
namespace Rnr\Alice\Fillers;


use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\Relation;

class HasOneFiller extends SingleFiller
{
    public function can($data, Relation $relation)
    {
        return $relation instanceof HasOne;
    }
}