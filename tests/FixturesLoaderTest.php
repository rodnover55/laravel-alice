<?php
namespace Rnr\Tests\Alice;


use Illuminate\Database\Eloquent\Model;
use Rnr\Tests\Alice\Mocks\RelationsModel;

class FixturesLoaderTest extends TestCase
{
    public function testLoadData() {
        $objects = $this->fixturesLoader->load('fixtures/data.yml');

        $this->assertObjectsInDatabase($objects);
    }

    public function testBelongsTo() {
        $objects = $this->fixturesLoader->load('fixtures/belongs.yml');

        $test1 = $objects['test-1'];

        $this->seeInDatabase($test1->getTable(), $test1->toArray());

        $expected = RelationsModel::with('belongs')->find($objects['belongs-1']->getKey());

        $this->assertNotEmpty($expected);
        $this->assertEquals($objects['test-1']->toArray(), $expected->belongs->toArray());
    }

    /**
     * @param Model[] $objects
     */
    public function assertObjectsInDatabase($objects) {
        foreach ($objects as $object) {
            $this->seeInDatabase($object->getTable(), $object->toArray());
        }
    }
}