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

    public function testExecute() {
        $this->artisan('db:generate-fixture', [
            'models' => [
                TestModel::class . '=1,2,3',
                Test2Model::class . '=1-15',
                Model::class
            ]
        ]);

        $this->assertEquals([
            TestModel::class => '1,2,3',
            Test2Model::class => '1-15',
            Model::class => '*'
        ], $this->extractor->criteria);
    }

    public function testAssociation() {}

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