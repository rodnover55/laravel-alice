<?php
namespace Rnr\Alice\Populators;


use Nelmio\Alice\Fixtures\Fixture;
use Nelmio\Alice\Instances\Populator\Methods\MethodInterface;
use Rnr\Alice\Instantiators\ModelWrapper;

class HasManyPopulator implements MethodInterface
{
    public function canSet(Fixture $fixture, $object, $property, $value)
    {
        return
            ($object instanceof ModelWrapper) and
            ($object->hasMany($property)) and
            (is_array($value));
    }

    public function set(Fixture $fixture, $object, $property, $value)
    {
        $object->addMany($property, $value);
    }
}