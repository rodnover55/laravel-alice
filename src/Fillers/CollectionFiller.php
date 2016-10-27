<?php
namespace Rnr\Alice\Fillers;


use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\Relation;

abstract class CollectionFiller extends AbstractFiller
{
    /**
     * @param array $data
     * @param Relation $relation
     * @return array|string[]
     */
    public function handle($data, Relation $relation)
    {
        if ($data instanceof Collection) {
            $data = $data->all();
        }

        return $this->prefixer->getArray($data);
    }
}