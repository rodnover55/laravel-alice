<?php
namespace Rnr\Tests\Alice;


use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Rnr\Alice\ModelExtractor;
use Rnr\Alice\Support\PrefixCalculator;
use Rnr\Tests\Alice\Mocks\RelationsModel;
use Rnr\Tests\Alice\Mocks\Test2Model;
use Rnr\Tests\Alice\Mocks\TestModel;
use Symfony\Component\Yaml\Yaml;

class ModelExtractorTest extends TestCase
{
    /** @var ModelExtractor $extractor */
    private $extractor;

    private $fixture;

    public function testExtractSpecifiedTables() {
        $this->prepareData($this->fixture);

        $data = $this->extractor->extract([
            TestModel::class => '*',
            Test2Model::class => '*'
        ]);

        $expected = $this->normalize(file_get_contents($this->fixture));
        $actual = $this->normalize(Yaml::dump($data, 3, 2));

        $this->assertEquals($expected, $actual);
    }

    protected function normalize($content) {
        return str_replace("\r\n", "\n", trim($content));
    }

    public function testExtractSelectively() {
        $objects = $this->prepareData($this->fixture);


        $data = $this->extractor->extract([
            TestModel::class => '1,2,3',
            Test2Model::class => '4-5'
        ]);

        $this->assertEquals([
                TestModel::class => array_only($objects, ['test-1', 'test-2', 'test-3']),
                Test2Model::class => array_only($objects, ['test2-4', 'test2-5'])
            ], $data
        );
    }

    public function testExtractAssociations() {
        $fixture = __DIR__ . '/fixtures/extractAssociations.yml';
        $this->fixturesLoader->load($fixture);

        $data = $this->extractor->extract([
            TestModel::class => [
                'range' => '1',
                'relations' => ['many', 'one', 'manyToMany']
            ],
            RelationsModel::class => [
                'relations' => ['belongs']
            ]
        ]);

        $expected = $this->normalize(file_get_contents($fixture));
        $actual = $this->normalize(Yaml::dump($data, 3, 2));

        $this->assertEquals($expected, $actual);
    }

    public function testSimpleArray() {
        $item = new TestModel([
            'id' => 1,
            'field1' => '123'
        ]);

        $this->assertEquals($item->attributesToArray(), $this->extractor->getArray($item));
    }

    public function testArrayWithHasOneMany() {
        $item = new TestModel([
            'id' => 1,
            'field1' => '123'
        ]);

        $relation = new Test2Model([
            'id2' => 5,
            'intfield' => 123
        ]);

        $item->setRelations([
            'many' => new Collection([$relation])
        ]);

        $this->assertEquals([
            'many' => ['@test2-5']
        ] + $item->attributesToArray(), $this->extractor->getArray($item));
    }

    public function testBelongsTo() {
        $testModel = new TestModel([
            'id' => 1
        ]);

        $this->assertRelationData('@test-1', $testModel,
            new BelongsTo($testModel->newQuery(), $testModel, '', '', '')
        );
    }

    public function testHasOne() {
        $testModel = new TestModel([
            'id' => 1
        ]);

        $this->assertRelationData('@test-1', $testModel,
            new HasOne($testModel->newQuery(), $testModel, '', '', '')
        );
    }

    public function testHasMany() {
        $testModel = new TestModel([
            'id' => 1
        ]);

        $this->assertRelationData(['@test-1'], [$testModel],
            new HasMany($testModel->newQuery(), $testModel, '', '', '')
        );
    }

    public function testBelongsToMany() {
        $testModel = new TestModel([
            'id' => 1
        ]);

        $this->assertRelationData(['@test-1'], [$testModel],
            new BelongsToMany($testModel->newQuery(), $testModel, 'links', '', '', '')
        );
    }

    public function testAddElement() {
        $test2 = array_map(function ($id) {
            return new Test2Model([
                'id2' => $id
            ]);
        }, range(1, 4));

        $test1 = new TestModel([
            'id' => 1
        ]);

        $test1->setRelations([
            'one' => $test2[0],
            'many' => $test2,
            'manyToMany' => $test2
        ]);

        $relation = new RelationsModel([
            'id' => 1
        ]);

        $relation->setRelations([
            'belongs' => $test1
        ]);

        $actual = $this->extractor->addElements([], [$relation]);

        $prefixer = new PrefixCalculator();
        $expected = [];

        foreach ($test2 as $item) {
            $expected[$prefixer->getKey($item)] = $item;
        }

        $expected[$prefixer->getKey($test1)] = $test1;
        $expected[$prefixer->getKey($relation)] = $relation;


        $this->assertEquals($expected, $actual);
    }

    public function assertRelationData($actual, $data, $relation) {
        $this->assertEquals($actual, $this->extractor->getRelationData($data, $relation));
    }

    protected function prepareData($fixture) {
        return array_map(function (Model $item) {
            return $item->attributesToArray();
        }, $this->fixturesLoader->load($fixture));
    }

    public function setUp()
    {
        parent::setUp();

        $this->fixture = realpath(__DIR__ . '/fixtures/data.yml');

        $this->extractor = $this->app->make(ModelExtractor::class);
    }
}