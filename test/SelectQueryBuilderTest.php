<?php

use p810\MySQL\Query;
use p810\MySQL\Builder\Select;
use p810\MySQL\Builder\Builder;
use PHPUnit\Framework\TestCase;

class SelectQueryBuilderTest extends TestCase {
    public function testQueryReturnsBuilder(): Select {
        $query = Query::select('*');

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
    public function testClausesAreAppended(Select $query): Select {
        $query
            ->where('foo', 'bar')
            ->or('quux', 'test', '!=')
            ->where([
                'bam' => 'borp',
                'wae' => ['caw', '!=', 'OR']
            ]);
        
        $this->assertEquals(
            "WHERE foo = ? OR quux != ? AND bam = ? OR wae != ?",
            $query->getWhere()
        );

        return $query;
    }

    /**
     * @depends testClausesAreAppended
     */
    public function testQueryBuildsQueryString(Select $query) {
        $this->assertEquals(
            'SELECT * FROM test_table WHERE foo = ? OR quux != ? AND bam = ? OR wae != ?',
            $query->build()
        );

        $this->assertEquals(['bar', 'test', 'borp', 'caw'], $query->getBindings());
    }
}