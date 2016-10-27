<?php
namespace Rnr\Alice\Populators;


use Nelmio\Alice\Fixtures\Fixture;
use Rnr\Alice\Instantiators\ModelWrapper;
use Nelmio\Alice\Instances\Populator\Methods\MethodInterface;

class HasOnePopulator implements MethodInterface
{
    public function canSet(Fixture $fixture, $object, $property, $value)
    {
        return
            ($object instanceof ModelWrapper) and
            ($object->hasMany($property)) and
            (!is_array($value));
    }

    public function set(Fixture $fixture, $object, $property, $value)
    {
        $object->addOne($property, $value);
    }
}