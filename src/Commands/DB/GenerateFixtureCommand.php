<?php
namespace Rnr\Alice\Commands\DB;


use Illuminate\Console\Command;
use Rnr\Alice\ModelExtractor;
use Symfony\Component\Yaml\Yaml;

class GenerateFixtureCommand extends Command
{
    protected $signature =
        'db:generate-fixture
            {models* : Models with ids to extract }';
    protected $description = 'Start web socket server.';

    public function handle(ModelExtractor $extractor) {
        $criteria = [];

        foreach ($this->argument('models') as $data) {
            $parts = explode('=', $data);

            if (count($parts) == 1) {
                $parts[1] = '*';
            }

            list($model, $range) = $parts;

            $criteria[$model] = $range;
        }

        $entities = $extractor->extract($criteria);

        $this->output->write(Yaml::dump($entities, 3, 2));

    }
}