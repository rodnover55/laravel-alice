<?php
namespace Rnr\Alice\Populators;

use Illuminate\Database\Eloquent\Model;
use Nelmio\Alice\Fixtures\Fixture;
use Nelmio\Alice\Instances\Populator\Methods\MethodInterface;
use Rnr\Alice\Instantiators\ModelWrapper;

class SimplePopulator implements MethodInterface
{
    public function canSet(Fixture $fixture, $object, $property, $value)
    {
        return $object instanceof ModelWrapper;
    }

    public function set(Fixture $fixture, $object, $property, $value)
    {
        $object->{$property} = $value;
    }

}