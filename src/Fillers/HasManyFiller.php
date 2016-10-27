<?php
namespace Rnr\Alice\Fillers;


use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\Relation;

class HasManyFiller extends CollectionFiller
{
    public function can($data, Relation $relation)
    {
        return $relation instanceof HasMany;
    }
}