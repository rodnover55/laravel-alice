<?php
namespace Rnr\Alice\Populators;


use Nelmio\Alice\Fixtures\Fixture;
use Nelmio\Alice\Instances\Populator\Methods\MethodInterface;
use Rnr\Alice\Instantiators\ModelWrapper;

class BelongsToManyPopulator implements MethodInterface
{
    public function canSet(Fixture $fixture, $object, $property, $value)
    {
        return
            ($object instanceof ModelWrapper) and
            ($object->hasBelongsToMany($property));
    }

    public function set(Fixture $fixture, $object, $property, $value)
    {
        $object->addBelongToMany($property, $value);
    }
}