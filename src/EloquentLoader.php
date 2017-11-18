<?php

namespace Rnr\Alice;


use Nelmio\Alice\Generator\ObjectGeneratorInterface;
use Nelmio\Alice\Loader\NativeLoader;
use Rnr\Alice\Instantiators\ModelWrapperGenerator;
use Rnr\Alice\Instantiators\ModelWrapperPropertyAccessor;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

class EloquentLoader extends NativeLoader
{
    protected function createObjectGenerator(): ObjectGeneratorInterface
    {
        return new ModelWrapperGenerator(
            parent::createObjectGenerator()
        );
    }

    protected function createPropertyAccessor(): PropertyAccessorInterface
    {
        $accessor = parent::createPropertyAccessor();


        return new ModelWrapperPropertyAccessor($accessor);
    }
}