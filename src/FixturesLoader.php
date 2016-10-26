<?php
namespace Rnr\Alice;


use Illuminate\Contracts\Container\Container;
use Illuminate\Database\DatabaseManager;
use Illuminate\Database\Eloquent\Model;
use Nelmio\Alice\Fixtures\Loader;

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
            /** @var Loader $loader */
            $loader = $this->container->make(Loader::class);

            $loader->addPopulator($this->container->make(Populator::class));

            $entities += $loader->load($file);
        }

        /** @var DatabaseManager $databaseManager */
        $databaseManager = $this->container->make(DatabaseManager::class);

        $connection = $databaseManager->connection();

        $connection->transaction(function () use (&$entities) {
            foreach ($entities as $entity) {
                $entity->save();
            }
        });

        return $entities;
    }
}