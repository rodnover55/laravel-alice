<?php

namespace Rnr\Tests\Alice\Support;
use Nelmio\Alice\Instances\Collection;
use Rnr\Alice\Instantiators\ModelWrapper;
use Rnr\Alice\Support\VirtualPropertyCollection;
use Rnr\Tests\Alice\Mocks\TestModel;
use Rnr\Tests\Alice\TestCase;

/**
 * @author Sergei Melnikov<me@rnr.name>
 */
class VirtualPropertyCollectionTest extends TestCase
{
    public function testFind() {
        $collection = new VirtualPropertyCollection(
            new Collection()
        );

        $excepted = new ModelWrapper();
        $excepted
            ->setModel(
                new TestModel([
                    'id' => 5
                ])
            );


        $collection->set('test', $excepted);

        $actual = $collection->find('test', 'id');

        $this->assertEquals($excepted->getModel()->id, $actual);
    }
}