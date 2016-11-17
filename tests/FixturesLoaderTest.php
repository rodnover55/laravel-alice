<?php
namespace Rnr\Tests\Alice;


use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Rnr\Tests\Alice\Mocks\RelationsModel;
use Rnr\Tests\Alice\Mocks\Test2Model;
use Rnr\Tests\Alice\Mocks\TestModel;

class FixturesLoaderTest extends TestCase
{
    public function testLoadData() {
        $objects = $this->fixturesLoader->load(__DIR__ . '/fixtures/data.yml');

        $this->assertObjectsInDatabase($objects);
    }

    public function testBelongsTo() {
        $objects = $this->fixturesLoader->load(__DIR__ . '/fixtures/belongs.yml');

        $this->assertObjectsInDatabase($objects);

        $expected = RelationsModel::with('belongs')->find($objects['belongs-1']->getKey());

        $this->assertNotEmpty($expected->belongs->getKey());
        $this->assertEquals($objects['test-1']->getKey(), $expected->belongs->getKey());
    }

    public function testHasOne() {
        $objects = $this->fixturesLoader->load(__DIR__ . '/fixtures/hasOne.yml');

        $this->assertObjectsInDatabase($objects);

        $expected = TestModel::with('one')->find($objects['test-1']->getKey());

        $this->assertInstanceOf(Test2Model::class, $expected->one);
        $this->assertEquals($objects['test2-1']->getKey(), $expected->one->getKey());
    }

    public function testHasMany() {
        $objects = $this->fixturesLoader->load(__DIR__ . '/fixtures/hasMany.yml');

        $this->assertObjectsInDatabase($objects);

        $expected = TestModel::with('many')->find($objects['test-1']->getKey());

        $actual = array_map(function (Model $model) {
            return $model->getKey();
        }, array_values(array_except($objects, ['test-1'])));

        $this->assertEquals($actual, $expected->many->modelKeys());
    }

    public function testBelongsToMany() {
        $objects = $this->fixturesLoader->load(__DIR__ . '/fixtures/belongsToMany.yml');

        $this->assertObjectsInDatabase($objects);

        foreach ($objects as $object) {
            $object->fresh('manyToMany');
        }

        $groups = [
            Test2Model::class => [
                'type' => TestModel::class,
                'values' => new Collection(array_values(array_only($objects, ['test-1', 'test-2'])))
            ],
            TestModel::class => [
                'type' => Test2Model::class,
                'values' => new Collection(array_values(array_only($objects, ['test2-1', 'test2-2'])))
            ]
        ];

        /** @var Model[][] $links */
        $links = [
            [$objects['test-1'], $objects['test2-1']],
            [$objects['test-2'], $objects['test2-1']],
            [$objects['test-1'], $objects['test2-2']],
            [$objects['test-2'], $objects['test2-2']]
        ];

        foreach ($links as $link) {
            $this->seeInDatabase('links', [
                'test_id' => $link[0]->getKey(),
                'test2_id' => $link[1]->getKey()
            ]);
        }
    }

    public function testLoadBelongsToById() {
        $this->fixturesLoader->load(__DIR__ . '/fixtures/data.yml');

        $relations = $this->fixturesLoader->load(__DIR__ . '/fixtures/relationBelongsToById.yml');

        $this->seeInDatabase('relations', [
            'id' => $relations['belongs-1']->getKey(),
            'belongs_id' => 1
        ]);
    }

    public function testLoadHasOneById() {
        $objects = $this->fixturesLoader->load(__DIR__ . '/fixtures/data.yml');

        $objects2 = $this->fixturesLoader->load(__DIR__ . '/fixtures/relationHasManyById.yml');

        foreach (['test2-1', 'test2-2', 'test2-3'] as $key) {
            $this->seeInDatabase('test2', [
                'id2' => $objects[$key]->getKey(),
                'intfield' => $objects2['test']->getKey()
            ]);
        }
    }

    public function testLoadBelongsToManyById() {
        $objects = $this->fixturesLoader->load(__DIR__ . '/fixtures/data.yml');

        $objects2 = $this->fixturesLoader->load(__DIR__ . '/fixtures/relationBelongsToManyById.yml');


        foreach (['test2-1', 'test2-2', 'test2-3', 'test2-4'] as $key) {
            $this->seeInDatabase('links', [
                'test_id' => $objects2['test']->getKey(),
                'test2_id' => $objects[$key]->getKey()
            ]);
        }
    }

    public function testValueByRelationField() {
        $objects = $this->fixturesLoader->load(__DIR__ . '/fixtures/relationField.yml');

        $this->seeInDatabase('test2', [
            'id2' => $objects['test2']->getKey(),
            'intfield' => $objects['test']->getKey()
        ]);
    }

    public function testValueByRelationFieldWithoutId() {
        $objects = $this->fixturesLoader->load([
            __DIR__ . '/fixtures/relationFieldWithoutId.yml',
            __DIR__ . '/fixtures/relationFieldWithoutId_test2.yml'
        ]);

        $this->seeInDatabase('test2', [
            'id2' => $objects['test2']->getKey(),
            'intfield' => $objects['test']->getKey()
        ]);
    }

    /**
     * @param Model[] $objects
     */
    public function assertObjectsInDatabase($objects) {
        foreach ($objects as $object) {
            $this->seeInDatabase($object->getTable(), $object->attributesToArray());
        }
    }

    public function assertArrayOfInstance($class, $array) {
        foreach ($array as $item) {
            $this->assertInstanceOf($class, $item);
        }
    }
}