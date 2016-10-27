<?php
namespace Rnr\Alice;


use Illuminate\Contracts\Container\Container;
use Illuminate\Database\Eloquent\Model;

class ModelExtractor
{
    /** @var  Container */
    private $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function extract($criteria) {
        $entities = [];

        foreach ($criteria as $class => $range) {
            /** @var Model $model */
            $model = new $class();
            $query = $model->newQuery();

            if ($range != '*') {
                /** @var RangeQueryObject $ranger */
                $ranger = $this->container->make(RangeQueryObject::class);

                $ranger
                    ->setField($model->getKeyName())
                    ->setRange($range);

                $ranger->applyTo($query);
            }

            $data = $query->get();

            $entities[$class] = $entities[$class] ?? [];

            /** @var Model $item */
            foreach ($data as $item) {
                $entities[$class]["{$item->getTable()}-{$item->getKey()}"] = $item->toArray();
            }
        }

        return $entities;
    }

}