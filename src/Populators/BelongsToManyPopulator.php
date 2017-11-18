<?php
namespace Rnr\Alice\Populators;


use Rnr\Alice\Instantiators\ModelWrapper;

class BelongsToManyPopulator implements PopulatorInterface
{
    public function can(&$object, $property, $value): bool
    {
        return
            ($object instanceof ModelWrapper) and
            ($object->hasBelongsToMany($property));
    }

    /**
     * @param ModelWrapper $object
     * @param $property
     * @param $value
     */
    public function setValue(&$object, $property, $value)
    {
        $object->addBelongToMany($property, $value);
    }
}