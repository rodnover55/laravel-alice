<?php
namespace Rnr\Tests\Alice;


use Illuminate\Database\Eloquent\Model;
use Rnr\Tests\Alice\Mocks\RelationsModel;
use Rnr\Tests\Alice\Mocks\Test2Model;
use Rnr\Tests\Alice\Mocks\TestModel;

class FixturesLoaderTest extends TestCase
{
    public function testLoadData() {
        $objects = $this->fixturesLoader->load('fixtures/data.yml');

        $this->assertObjectsInDatabase($objects);
    }

    public function testBelongsTo() {
        $objects = $this->fixturesLoader->load('fixtures/belongs.yml');

        $this->assertObjectsInDatabase($objects);

        $expected = RelationsModel::with('belongs')->find($objects['belongs-1']->getKey());

        $this->assertNotEmpty($expected->belongs->getKey());
        $this->assertEquals($objects['test-1']->getKey(), $expected->belongs->getKey());
    }

    public function testHasOne() {
        $objects = $this->fixturesLoader->load('fixtures/hasOne.yml');

        $this->assertObjectsInDatabase($objects);

        $expected = TestModel::with('one')->find($objects['test-1']->getKey());

        $this->assertInstanceOf(Test2Model::class, $expected->one);
        $this->assertEquals($objects['test2-1']->getKey(), $expected->one->getKey());
    }

    public function testHasMany() {
        $objects = $this->fixturesLoader->load('fixtures/hasMany.yml');

        $this->assertObjectsInDatabase($objects);

        $expected = TestModel::with('many')->find($objects['test-1']->getKey());

        $actual = array_map(function (Model $model) {
            return $model->getKey();
        }, array_values(array_except($objects, ['test-1'])));

        $this->assertEquals($actual, $expected->many->modelKeys());
    }

    /**
     * @param Model[] $objects
     */
    public function assertObjectsInDatabase($objects) {
        foreach ($objects as $object) {
            $this->seeInDatabase($object->getTable(), $object->attributesToArray());
        }
    }
}