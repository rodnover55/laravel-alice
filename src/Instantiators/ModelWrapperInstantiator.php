<?php
namespace Rnr\Alice\Instantiators;


use Nelmio\Alice\Instances\Instantiator\Methods\MethodInterface;
use Nelmio\Alice\Fixtures\Fixture;

class ModelWrapperInstantiator implements MethodInterface
{

    public function canInstantiate(Fixture $fixture)
    {
        return true;
    }

    public function instantiate(Fixture $fixture)
    {
        $object = new ModelWrapper();

        $model = $fixture->getClass();

        $object->setModel(new $model());

        return $object;
    }
}