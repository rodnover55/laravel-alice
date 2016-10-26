<?php
namespace Rnr\Tests\Alice;

use Orchestra\Testbench\TestCase as parentTestCase;
use Illuminate\Config\Repository as Config;
use Rnr\Alice\FixturesLoader;

class TestCase extends ParentTestCase
{
    /** @var FixturesLoader */
    protected $fixturesLoader;

    protected function getEnvironmentSetUp($app)
    {
        /** @var Config $config */
        $config = $app->make(Config::class);

        $config->set('database.default', 'testing');
    }

    protected function setUp()
    {
        parent::setUp();

        $this->fixturesLoader = $this->app->make(FixturesLoader::class);

        $this->loadMigrationsFrom([
            '--database' => 'testing',
            '--realpath' => realpath(__DIR__ . '/migrations')
        ]);
    }
}