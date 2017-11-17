<?php

namespace Rnr\Alice;

use Nelmio\Alice\Generator\InstantiatorInterface;
use Nelmio\Alice\Loader\NativeLoader;

class EloquentLoader extends NativeLoader
{
    protected function createInstantiator(): InstantiatorInterface
    {
        return new ExistingInstanceInstantiator(
            new InstantiatorResolver(
                new InstantiatorRegistry([
                    new NoCallerMethodCallInstantiator(),
                    new NullConstructorInstantiator(),
                    new NoMethodCallInstantiator(),
                    new StaticFactoryInstantiator(),
                ])
            )
        );
    }
}