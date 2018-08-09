<?php

use PHPUnit\Framework\TestCase;

use p810\MySQL\Model;
use p810\MySQL\Connection;
use p810\MySQL\Builder\Select;

class ModelTest extends TestCase {
    public function testGetTable(): Model {
        $connection = new Connection('root', 'root', 'test', 'database');

        $model = new class ($connection) extends Model {
            protected $table = 'test';
        };

        $this->assertEquals('test', $model->getTable());

        return $model;
    }

    /**
     * @depends testGetTable
     */
    public function testQueryBuilderDefaultValues(Model $model) {
        $query = $model->where([
            'foo' => 'bar'
        ])->and('bar', 'bam');

        $this->assertEquals('SELECT * FROM test WHERE foo = ? AND bar = ?', $query->build());
    }
}
