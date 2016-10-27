<?php
namespace Rnr\Alice;


use Illuminate\Contracts\Container\Container;
use Illuminate\Database\Eloquent\Model;
use Rnr\Alice\Exceptions\FillerNotFoundException;
use Rnr\Alice\Fillers\AbstractFiller;
use Rnr\Alice\Fillers\BelongsToManyFiller;
use Rnr\Alice\Fillers\BelongToFiller;
use Rnr\Alice\Fillers\HasManyFiller;
use Rnr\Alice\Fillers\HasOneFiller;
use Rnr\Alice\Support\PrefixCalculator;

class ModelExtractor
{
    /** @var  Container */
    private $container;

    /** @var PrefixCalculator  */
    private $prefixer;

    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->prefixer = new PrefixCalculator();
    }

    public function extract($criteria) {
        $entities = [];

        foreach ($criteria as $class => $data) {
            if (is_array($data)) {
                $range = $data['range'] ?? '*';
                $relations = $data['relations'] ?? [];
            } else {
                $range = $data;
                $relations = [];
            }

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

            $data = $query->with($relations)->get();

            $entities[$class] = $entities[$class] ?? [];

            /** @var Model $item */
            foreach ($data as $item) {
                $entities[$class][$this->prefixer->getKey($item)] = $this->getArray($item);
            }
        }

        return $entities;
    }


    public function getArray(Model $item) {
        $data = $item->attributesToArray();
        $relations = $item->getRelations();

        foreach ($relations as $name => $relation) {
            $data[$name] = $this->getRelationData($relation, $item->{$name}());
        }

        return $data;
    }

    public function getRelationData($data, $relation) {
        /** @var AbstractFiller $filler */
        $filler = array_first([
            new BelongToFiller(),
            new HasOneFiller(),
            new HasManyFiller(),
            new BelongsToManyFiller()
        ], function (AbstractFiller $filler) use ($data, $relation) {
            return $filler->can($data, $relation);
        });

        if (empty($filler)) {
            throw new FillerNotFoundException('Cannot find filler.');
        }

        return $filler->handle($data, $relation);
    }

}