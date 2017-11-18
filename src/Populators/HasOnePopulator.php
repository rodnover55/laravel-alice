<?php
namespace Rnr\Alice\Populators;


use Nelmio\Alice\Fixtures\Fixture;
use Rnr\Alice\Instantiators\ModelWrapper;
use Nelmio\Alice\Instances\Populator\Methods\MethodInterface;

class HasOnePopulator implements PopulatorInterface
{
    public function can(&$object, $property, $value): bool
    {
        return
            ($object instanceof ModelWrapper) and
            ($object->hasMany($property)) and
            (!is_array($value));
    }

    /**
     * @param ModelWrapper $object
     * @param $property
     * @param $value
     */
    public function setValue(&$object, $property, $value)
    {
        $object->addOne($property, $value);
    }
}