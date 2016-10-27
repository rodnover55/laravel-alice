<?php
namespace Rnr\Alice;


use Illuminate\Contracts\Container\Container;
use Illuminate\Database\DatabaseManager;
use Illuminate\Database\Eloquent\Model;
use Nelmio\Alice\Fixtures\Loader;
use Rnr\Alice\Instantiators\ModelWrapper;
use Rnr\Alice\Instantiators\ModelWrapperInstantiator;
use Rnr\Alice\Populators\BelongsToManyPopulator;
use Rnr\Alice\Populators\BelongsToPopulator;
use Rnr\Alice\Populators\HasManyPopulator;
use Rnr\Alice\Populators\HasOnePopulator;
use Rnr\Alice\Populators\SimplePopulator;

class FixturesLoader
{
    /** @var Container  */
    private $container;

    private $populators = [
        SimplePopulator::class,
        BelongsToPopulator::class,
        HasManyPopulator::class,
        HasOnePopulator::class,
        BelongsToManyPopulator::class
    ];

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * @param string|array $files
     * @return array|Model[]
     */
    public function load($files) {
        /** @var ModelWrapper[] $entities */
        $entities = [];

        foreach ((is_array($files) ? $files : [$files]) as $file) {
            $entities += $this->loadFile($file);
        }

        /** @var DatabaseManager $databaseManager */
        $databaseManager = $this->container->make(DatabaseManager::class);

        $connection = $databaseManager->connection();

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

    /**
     * @param string $file
     * @return ModelWrapper[]|object[]
     */
    public function loadFile($file) {
        /** @var Loader $loader */
        $loader = $this->container->make(Loader::class);

        foreach ($this->populators as $populator) {
            $loader->addPopulator($this->container->make($populator));
        }

        $loader->addInstantiator($this->container->make(ModelWrapperInstantiator::class));

        return $loader->load($file);
    }
}