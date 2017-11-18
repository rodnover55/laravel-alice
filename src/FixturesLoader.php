<?php
namespace Rnr\Alice;


use Illuminate\Contracts\Container\Container;
use Illuminate\Database\DatabaseManager;
use Illuminate\Database\Eloquent\Model;
use Rnr\Alice\Instantiators\ModelWrapper;

class FixturesLoader
{
    /** @var Container  */
    private $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * @param string|array $files
     * @return array|Model[]
     */
    public function load($files) {
        /** @var DatabaseManager $databaseManager */
        $databaseManager = $this->container->make(DatabaseManager::class);

        $connection = $databaseManager->connection();

        $loader = new EloquentLoader();

        $files = (is_array($files)) ? ($files) : ([$files]);

        /**
         * @var ModelWrapper[] $entities
         */
        $entities = $loader->loadFiles($files)->getObjects();

        $connection->transaction(function () use (&$entities) {
            foreach ($entities as $entity) {
                if ($entity->isDirty()) {
                    $entity->save();
                }
            }
        });

        return array_map(function (ModelWrapper $entity) {
            return $entity->getModel();
        }, $entities);
    }
}