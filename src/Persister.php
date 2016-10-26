<?php
namespace Rnr\Alice;


use Nelmio\Alice\PersisterInterface;

class Persister implements PersisterInterface
{
    public function persist(array $objects)
    {
        throw new \Exception('Method ' . __CLASS__ . '::' . __FUNCTION__ . ' not implemented');
    }

    public function find($class, $id)
    {
        throw new \Exception('Method ' . __CLASS__ . '::' . __FUNCTION__ . ' not implemented');
    }
}