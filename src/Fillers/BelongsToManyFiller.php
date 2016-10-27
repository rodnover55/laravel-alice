<?php
namespace Rnr\Alice\Fillers;


use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\Relation;

class BelongsToManyFiller extends CollectionFiller
{
    public function can($data, Relation $relation)
    {
        return $relation instanceof BelongsToMany;
    }
}