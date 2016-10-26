<?php
namespace Rnr\Tests\Alice;


use Illuminate\Database\DatabaseManager;
use Rnr\Alice\RangeQueryObject;

class RangeQueryObjectTest extends TestCase
{
    /** @var RangeQueryObject */
    private $ranger;

    public function testRangeParse() {
        /** @var DatabaseManager $databaseManager */
        $databaseManager = $this->app->make(DatabaseManager::class);

        $query = $databaseManager->connection()->table('test');
        $this->ranger
            ->setRange('1,5,7-10, 15, 18-20')
            ->setField('field');

        $this->ranger->applyTo($query);

        $this->assertEquals(
            'select * from "test" where "field" in (?, ?, ?) or ' .
            '"field" between ? and ? or ' .
            '"field" between ? and ?', $query->toSql());

        $this->assertEquals([
            1, 5, 15, 7, 10, 18, 20
        ], $query->getBindings());
    }

    /**
     * @dataProvider rangesProvider
     * @param $expected
     * @param $value
     */
    public function testParse($value, $expected) {
        $this->assertEquals($expected, $this->ranger->parse($value));
    }

    public function rangesProvider() {
        return [
            'one element' => [
                '1', ['in' => ['1'], 'ranges' => []]
            ],
            'many element' => [
                '1,2', ['in' => ['1', '2'], 'ranges' => []]
            ],
            'range' => [
                '1-5', ['ranges' => [['1', '5']], 'in' => []]
            ],
            'many ranges' => [
                '1-5,8-10', ['ranges' => [['1', '5'], ['8', '10']], 'in' => []]
            ],
            'spaces' => [
                ' 1, 5 ', ['in' => ['1', '5'], 'ranges' => []]
            ],
            'combined' => [
                '1,5,7-10, 15, 18-20', [
                    'in' => ['1', '5', '15'],
                    'ranges' => [['7', '10'], ['18', '20']]
                ]
            ]
        ];
    }

    protected function setUp()
    {
        parent::setUp();

        $this->ranger = $this->app->make(RangeQueryObject::class);
    }


}