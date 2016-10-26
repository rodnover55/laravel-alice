<?php
namespace Rnr\Alice\Support;


use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;

class RangeQueryObject
{
    private $range;
    private $field = 'id';

    /**
     * @param QueryBuilder|EloquentBuilder $query
     * @return QueryBuilder|EloquentBuilder
     */
    public function apply($query) {
        $ranges = $this->parse($this->range);

        $this
            ->applySingle($ranges['in'], $query)
            ->applyBetween($ranges['ranges'], $query);

        return $query;
    }

    /**
     * @param array $items
     * @param QueryBuilder|EloquentBuilder $query
     * @return $this
     */
    protected function applySingle(array $items, $query) {
        $query->whereIn($this->field, $items);

        return $this;
    }

    /**
     * @param array $ranges
     * @param QueryBuilder|EloquentBuilder $query
     * @return $this
     */
    protected function applyBetween($ranges, $query) {
        foreach ($ranges as $range) {
            $query->orWhereBetween($this->field, $range);
        }

        return $this;
    }


    public function parse($value) {
        $ranges = [
            'in' => [],
            'ranges' => []
        ];

        $value = str_replace(' ', '', $value);
        $intervals = explode(',', $value);

        foreach ($intervals as $interval) {
            $range = explode('-', $interval);

            if (count($range) == 2) {
                $ranges['ranges'][] = $range;
            } else {
                $ranges['in'][] = $range[0];
            }
        }

        return $ranges;
    }

    /**
     * @return mixed
     */
    public function getRange()
    {
        return $this->range;
    }

    /**
     * @param mixed $range
     * @return $this
     */
    public function setRange($range)
    {
        $this->range = $range;

        return $this;
    }

    /**
     * @return string
     */
    public function getField(): string
    {
        return $this->field;
    }

    /**
     * @param string $field
     * @return $this
     */
    public function setField(string $field)
    {
        $this->field = $field;

        return $this;
    }
}