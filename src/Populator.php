<?php
namespace Rnr\Alice;

use Illuminate\Database\Eloquent\Model;
use Nelmio\Alice\Fixtures\Fixture;
use Nelmio\Alice\Instances\Populator\Methods\MethodInterface;

class Populator implements MethodInterface
{
    public function canSet(Fixture $fixture, $object, $property, $value)
    {
        return $object instanceof Model;
    }

    public function set(Fixture $fixture, $object, $property, $value)
    {
        $object->{$property} = $value;
    }

}