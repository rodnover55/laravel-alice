<?php
namespace Rnr\Alice\Populators;

use Nelmio\Alice\Fixtures\Fixture;
use Nelmio\Alice\Instances\Populator\Methods\MethodInterface;
use Rnr\Alice\Instantiators\ModelWrapper;

class BelongsToPopulator implements MethodInterface
{
    public function canSet(Fixture $fixture, $object, $property, $value)
    {
        return
            ($object instanceof ModelWrapper) and
            ($object->hasBelongTo($property));
    }

    /**
     * @param Fixture $fixture
     * @param ModelWrapper $object
     * @param string $property
     * @param mixed $value
     */
    public function set(Fixture $fixture, $object, $property, $value)
    {
        $object->addBelongTo($property, $value);
    }
}