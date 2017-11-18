<?php

namespace Rnr\Alice;

use Illuminate\Contracts\Container\Container;
use Nelmio\Alice\Generator\HydratorInterface;
use Nelmio\Alice\Generator\Instantiator\Chainable\NoCallerMethodCallInstantiator;
use Nelmio\Alice\Generator\Instantiator\Chainable\NoMethodCallInstantiator;
use Nelmio\Alice\Generator\Instantiator\Chainable\NullConstructorInstantiator;
use Nelmio\Alice\Generator\Instantiator\Chainable\StaticFactoryInstantiator;
use Nelmio\Alice\Generator\Instantiator\ExistingInstanceInstantiator;
use Nelmio\Alice\Generator\Instantiator\InstantiatorRegistry;
use Nelmio\Alice\Generator\Instantiator\InstantiatorResolver;
use Nelmio\Alice\Generator\InstantiatorInterface;
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