<?php
namespace Rnr\Alice\Fillers;


use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Relation;

class BelongToFiller extends SingleFiller
{
    public function can($data, Relation $relation)
    {
        return $relation instanceof BelongsTo;
    }
}