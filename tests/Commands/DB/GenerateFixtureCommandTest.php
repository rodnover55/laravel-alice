<?php
namespace Rnr\Tests\Alice\Commands\DB;


use Illuminate\Contracts\Console\Kernel as KernelContract;
use Illuminate\Contracts\Container\Container;
use Illuminate\Database\Eloquent\Model;
use Rnr\Alice\Commands\DB\GenerateFixtureCommand;
use Rnr\Alice\ModelExtractor;
use Rnr\Tests\Alice\Mocks\Test2Model;
use Rnr\Tests\Alice\Mocks\TestModel;
use Rnr\Tests\Alice\TestCase;
use Illuminate\Foundation\Console\Kernel;

class GenerateFixtureCommandTest extends TestCase
{
    /** @var ModelExtractor */
    private $extractor;


    /**
     * @dataProvider argumentsProvider
     * @param $actual
     * @param $arguments
     */
    public function testExecute($actual, $arguments) {
        $this->artisan('db:generate-fixture', [
            'models' => [$arguments]
        ]);

        $this->assertEquals($actual, $this->extractor->criteria);
    }

    public function argumentsProvider() {
        return [
            'is series' => [
                [TestModel::class => [
                    'range' =>'1,2,3'
                ]],
                TestModel::class . '=1,2,3'
            ],
            'range' => [
                [TestModel::class => [
                    'range' => '1-15'
                ]],
                TestModel::class . '=1-15'
            ],
            'all' => [
                [TestModel::class => [
                    'range' => '*'
                ]],
                TestModel::class
            ],
            'all with relations' => [
                [TestModel::class => [
                        'range' => '*',
                        'relations' =>['test', 'test.test2']
                ]],
                TestModel::class . '(relations:test,test.test2)'
            ],
            'all with range' => [
                [TestModel::class => [
                    'range' => '1-5',
                    'relations' =>['test', 'test.test2']
                ]],
                TestModel::class . '(relations:test,test.test2)=1-5'
            ]
        ];
    }

    protected function setUp()
    {
        parent::setUp();

        $this->app->singleton(ModelExtractor::class, function (Container $app) {
            return new class($app) extends ModelExtractor {
                public $criteria;

                public function extract($criteria)
                {
                    TestCase::assertEmpty($this->criteria);

                    $this->criteria = $criteria;

                    return [];
                }
            };
        });

        $this->extractor = $this->app->make(ModelExtractor::class);

        /** @var Kernel $kernel */
        $kernel = $this->app->make(KernelContract::class);
        $kernel->registerCommand($this->app->make(GenerateFixtureCommand::class));
    }
}