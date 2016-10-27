<?php
namespace Rnr\Alice\Support;


use Illuminate\Database\Eloquent\Model;

class PrefixCalculator
{
    /**
     * @param Model $item
     * @return string
     */
    public function getKey(Model $item) {
        return "{$item->getTable()}-{$item->getKey()}";
    }

    /**
     * @param Model $item
     * @return string
     */
    public function getVariable(Model $item) {
        return "@{$this->getKey($item)}";
    }

    /**
     * @param array|Model[] $items
     * @return array|string[]
     */
    public function getArray($items) {
        return array_map(function ($item) {
            return $this->getVariable($item);
        }, $items);
    }
}