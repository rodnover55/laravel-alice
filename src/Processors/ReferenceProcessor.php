<?php

namespace Rnr\Alice\Processors;

use Nelmio\Alice\Instances\Collection;
use \Nelmio\Alice\Instances\Processor\Methods\Reference;
use Rnr\Alice\Support\VirtualPropertyCollection;

/**
 * @author Sergei Melnikov<me@rnr.name>
 */
class ReferenceProcessor extends Reference
{
    protected static $regex = '/^[\',\"]?'
    .'(?:(?<multi>\d+)x\ )?'
    .'@(?<reference>[\p{L}\d\_\.\*\/\-]+)'
    .'(?<sequence>\{(?P<from>\d+)\.\.(?P<to>\d+)\})?'
    .'(?:\->(?<property>[\p{L}\d_.*\/-]+))?'
    .'[\',\"]?$'
    .'/xi'
    ;

    public function setObjects(Collection $objects)
    {
        parent::setObjects(new VirtualPropertyCollection($objects));
    }
}