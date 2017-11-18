<?php
namespace Rnr\Alice\Instantiators;


use Illuminate\Database\Eloquent\Model;
use Nelmio\Alice\Definition\Object\SimpleObject;
use Nelmio\Alice\FixtureInterface;
use Nelmio\Alice\Generator\GenerationContext;
use Nelmio\Alice\Generator\InstantiatorInterface;
use Nelmio\Alice\Generator\ResolvedFixtureSet;
use Nelmio\Alice\Generator\ObjectGeneratorInterface;
use Nelmio\Alice\ObjectBag;
use Nelmio\Alice\Throwable\GenerationThrowable;

class ModelWrapperGenerator implements ObjectGeneratorInterface
{
    /** @var ObjectGeneratorInterface */
    private $generator;

    protected function wrap(
        Model $model
    ): ModelWrapper
    {
        $object = new ModelWrapper();

        $object->setModel($model);

        return $object;
    }

    public function __construct(ObjectGeneratorInterface $generator)
    {
        $this->generator = $generator;
    }

    public function generate(
        FixtureInterface $fixture, ResolvedFixtureSet $fixtureSet, GenerationContext $context
    ): ObjectBag
    {
        $objectBag = $this->generator->generate($fixture, $fixtureSet, $context);

        $object = $objectBag->get($fixture)->getInstance();
        $wrappedObject = ($object instanceof Model) ? $this->wrap($object) : $object;

        return $fixtureSet->getObjects()->with(
            new SimpleObject(
                $fixture->getId(),
                $wrappedObject
            )
        );
    }
}