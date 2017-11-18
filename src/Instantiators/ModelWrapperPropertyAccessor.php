<?php

namespace Rnr\Alice\Instantiators;

use Rnr\Alice\Populators\BelongsToManyPopulator;
use Rnr\Alice\Populators\BelongsToPopulator;
use Rnr\Alice\Populators\HasManyPopulator;
use Rnr\Alice\Populators\HasOnePopulator;
use Rnr\Alice\Populators\PopulatorInterface;
use Rnr\Alice\Populators\SimplePopulator;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;


class ModelWrapperPropertyAccessor implements PropertyAccessorInterface
{
    /** @var PropertyAccessorInterface */
    private $accessor;

    /** @var array|PopulatorInterface[]  */
    private $populators = [];

    public function __construct(PropertyAccessorInterface $accessor)
    {
        $this->accessor = $accessor;

        $this->populators = array_map(function ($class) {
            return new $class();
        }, [
            BelongsToManyPopulator::class,
            HasOnePopulator::class,
            HasManyPopulator::class,
            BelongsToPopulator::class,
            SimplePopulator::class
        ]);
    }

    public function setValue(&$objectOrArray, $propertyPath, $value)
    {
        foreach ($this->populators as $populator) {
            if ($populator->can($objectOrArray, $propertyPath, $value)) {
                $populator->setValue($objectOrArray, $propertyPath, $value);
                return;
            }
        }

        $this->accessor->setValue($objectOrArray, $propertyPath, $value);
    }

    public function getValue($objectOrArray, $propertyPath)
    {
        return $this->accessor->getValue($objectOrArray, $propertyPath);
    }

    public function isWritable($objectOrArray, $propertyPath)
    {
        return $this->isWritable($objectOrArray, $propertyPath);
    }

    public function isReadable($objectOrArray, $propertyPath)
    {
        return $this->isReadable($objectOrArray, $propertyPath);
    }
}