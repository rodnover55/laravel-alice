<?php
namespace Rnr\Alice\Populators;

use Nelmio\Alice\Fixtures\Fixture;
use Nelmio\Alice\Instances\Populator\Methods\MethodInterface;
use Rnr\Alice\Instantiators\ModelWrapper;

class BelongsToPopulator implements PopulatorInterface
{
    public function can(&$object, $property, $value): bool
    {
        return
            ($object instanceof ModelWrapper) and
            ($object->hasBelongTo($property));
    }

    /**
     * @param ModelWrapper $object
     * @param $property
     * @param $value
     */
    public function setValue(&$object, $property, $value)
    {
        $object->addBelongTo($property, $value);
    }
}