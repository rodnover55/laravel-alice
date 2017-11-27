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
    protected $description = 'Create database fixtures from the database.';

    public function handle(ModelExtractor $extractor) {
        $criteria = [];

        foreach ($this->argument('models') as $data) {
            $matches = [];

            if (preg_match('/^(.+)\((.+)\)=(.+)$/', $data, $matches) ||
                preg_match('/^(.+)\((.+)\)$/', $data, $matches)) {
                // Nothing to do. Only to fill $matches
            } else if (preg_match('/^(.+)=(.+)$/', $data, $matches)) {
                $matches[3] = $matches[2];
                $matches[2] = '';
            } else {
                $matches[1] = $data;
            }

            $model = $matches[1];

            $criteria[$model] = array_merge($this->parseOptions($matches[2] ?? ''), [
                'range' => $matches[3] ?? '*'
            ]);
        }

        $entities = $extractor->extract($criteria);

        $this->output->write(Yaml::dump($entities, 3, 2));

    }

    protected function parseOptions($options) {
        if (empty($options)) {
            return [];
        }

        $fields = ['relations'];

        $data = [];

        foreach(explode(';', $options) as $option) {
            list($key, $value) = explode(':', $option);

            if (in_array($key, $fields)) {
                $data[$key] = explode(',', $value);
            }
        };

        return $data;
    }
}
