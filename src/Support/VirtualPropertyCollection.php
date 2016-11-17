<?php

namespace Rnr\Alice\Support;
use Nelmio\Alice\Instances\Collection;
use UnexpectedValueException;

/**
 * @author Sergei Melnikov<me@rnr.name>
 */
class VirtualPropertyCollection extends Collection
{
    /**
     * @var Collection
     */
    private $collection;

    public function __construct(Collection $collection)
    {
        $this->collection = $collection;
    }

    public function find($name, $property = null)
    {
        $object = $this->collection->find($name);

        if (is_null($property)) {
            return $object;
        }

        if (isset($object->{$property})) {
            return $object->{$property};
        }

        throw new UnexpectedValueException(
            sprintf('Property %s is not defined for instance %s', $property, $name)
        );
    }

    public function toArray()
    {
        return $this->collection->toArray();
    }

    public function containsKey($name)
    {
        return $this->collection->containsKey($name);
    }

    public function get($name)
    {
        return $this->collection->get($name);
    }

    public function set($name, $instance)
    {
        $this->collection->set($name, $instance);
    }

    public function remove($name)
    {
        return $this->collection->remove($name);
    }

    protected function getKeysByMask($mask)
    {
        return $this->collection->getKeysByMask($mask);
    }

    public function random($mask, $count = 1, $property = null)
    {
        return $this->collection->random($mask, $count, $property);
    }

    public function clear()
    {
        $this->collection->clear();
    }
}