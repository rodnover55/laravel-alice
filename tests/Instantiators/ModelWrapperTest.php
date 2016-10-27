<?php
namespace Rnr\Tests\Alice\Instantiators;


use Rnr\Alice\FixturesLoader;
use Rnr\Alice\Instantiators\ModelWrapper;
use Rnr\Tests\Alice\TestCase;

class ModelWrapperTest extends TestCase
{
    public function testInstantiate() {
        /** @var FixturesLoader $loader */
        $loader = $this->app->make(FixturesLoader::class);

        $entities = $loader->loadFile(dirname(__DIR__) . '/fixtures/data.yml');

        foreach ($entities as $entity) {
            $this->assertInstanceOf(ModelWrapper::class, $entity);
        }
    }
}