<?php
namespace Rnr\Tests\Alice;


use Illuminate\Database\Eloquent\Model;
use Rnr\Alice\ModelExtractor;
use Rnr\Tests\Alice\Mocks\Test2Model;
use Rnr\Tests\Alice\Mocks\TestModel;
use Symfony\Component\Yaml\Yaml;

class ModelExtractorTest extends TestCase
{
    /** @var ModelExtractor $extractor */
    private $extractor;

    /** @var Model[] */
    private $objects;

    private $fixture;

    public function testExtractSpecifiedTables() {
        $data = $this->extractor->extract([
            TestModel::class => '*',
            Test2Model::class => '*'
        ]);

        $this->assertEquals(
            trim(file_get_contents($this->fixture)),
            trim(Yaml::dump($data, 3, 2))
        );
    }

    public function testExtractSelectively() {
        $data = $this->extractor->extract([
            TestModel::class => '1,2,3',
            Test2Model::class => '4-5'
        ]);

        $this->assertEquals([
                TestModel::class => array_only($this->objects, ['test-1', 'test-2', 'test-3']),
                Test2Model::class => array_only($this->objects, ['test2-4', 'test2-5'])
            ], $data
        );
    }

    protected function prepareData() {

    }

    protected function setUp()
    {
        parent::setUp();

        $this->fixture = realpath(__DIR__ . '/fixtures/data.yml');

        $this->objects = array_map(function (Model $item) {
            return $item->toArray();
        }, $this->fixturesLoader->load($this->fixture));

        $this->extractor = $this->app->make(ModelExtractor::class);
    }


}