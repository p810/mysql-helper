<?php

use p810\MySQL\Query;
use p810\MySQL\Builder\Select;
use p810\MySQL\Builder\Builder;
use PHPUnit\Framework\TestCase;

class SelectQueryBuilderTest extends TestCase {
    public function testQueryReturnsBuilder(): Select {
        $query = (new Query)->select('*');

        $this->assertInstanceOf(Select::class, $query);

        return $query;
    }

    /**
     * @depends testQueryReturnsBuilder
     */
    public function testQueryValuesAreCorrect(Select $query): Select {
        $this->assertEquals('*', $query->getColumns());

        $query->from('test_table');

        $this->assertEquals('test_table', $query->getTable());

        return $query;
    }

    /**
     * @depends testQueryValuesAreCorrect
     */
    public function testQueryBuildsQueryString(Select $query) {
        $this->assertEquals('SELECT * FROM test_table', $query->build());
    }
}