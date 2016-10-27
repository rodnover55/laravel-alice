<?php
namespace Rnr\Alice\Fillers;


use Illuminate\Database\Eloquent\Relations\Relation;
use Rnr\Alice\Support\PrefixCalculator;

abstract class AbstractFiller
{
    /** @var PrefixCalculator */
    protected $prefixer;

    public function __construct()
    {
        $this->prefixer = new PrefixCalculator();
    }

    abstract public function can($data, Relation $relation);
    abstract public function handle($data, Relation $relation);
}