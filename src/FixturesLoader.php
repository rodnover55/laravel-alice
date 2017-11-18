<?php
namespace Rnr\Alice;


use Illuminate\Contracts\Container\Container;
use Illuminate\Database\DatabaseManager;
use Illuminate\Database\Eloquent\Model;
use Nelmio\Alice\Fixtures\Loader;
use Nelmio\Alice\Instances\Processor\Methods\Faker;
use Nelmio\Alice\Instances\Processor\Providers\IdentityProvider;
use Nelmio\Alice\Loader\NativeLoader;
use Rnr\Alice\Instantiators\ModelWrapper;
use Rnr\Alice\Instantiators\ModelWrapperGenerator;
use Rnr\Alice\Populators\BelongsToManyPopulator;
use Rnr\Alice\Populators\BelongsToPopulator;
use Rnr\Alice\Populators\HasManyPopulator;
use Rnr\Alice\Populators\HasOnePopulator;
use Rnr\Alice\Populators\SimplePopulator;
use Rnr\Alice\Processors\ReferenceProcessor;

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