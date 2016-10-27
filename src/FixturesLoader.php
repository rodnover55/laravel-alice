<?php
namespace Rnr\Alice;


use Illuminate\Contracts\Container\Container;
use Illuminate\Database\DatabaseManager;
use Illuminate\Database\Eloquent\Model;
use Nelmio\Alice\Fixtures\Loader;
use Rnr\Alice\Instantiators\ModelWrapper;
use Rnr\Alice\Instantiators\ModelWrapperInstantiator;
use Rnr\Alice\Populators\SimplePopulator;

class FixturesLoader
{
    /** @var Container  */
    private $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function load($files) {
        /** @var Model[] $entities */
        $entities = [];

        foreach ((is_array($files) ? $files : [$files]) as $file) {
            $entities += $this->loadFile($file);
        }

        /** @var DatabaseManager $databaseManager */
        $databaseManager = $this->container->make(DatabaseManager::class);

        $connection = $databaseManager->connection();

        $connection->transaction(function () use (&$entities) {
            foreach ($entities as $entity) {
                $entity->save();
            }
        });

        return array_map(function (ModelWrapper $entity) {
            return $entity->getModel();
        }, $entities);
    }

    public function loadFile($file) {
        /** @var Loader $loader */
        $loader = $this->container->make(Loader::class);
        $loader->addPopulator($this->container->make(SimplePopulator::class));
        $loader->addInstantiator($this->container->make(ModelWrapperInstantiator::class));

        return $loader->load($file);
    }
}