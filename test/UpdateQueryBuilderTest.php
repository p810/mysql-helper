<?php

use p810\MySQL\Query;
use p810\MySQL\Builder\Update;
use p810\MySQL\Builder\Builder;
use PHPUnit\Framework\TestCase;

class UpdateQueryBuilderTest extends TestCase {
    public function testQueryReturnsUpdateBuilder(): Update {
        $query = Query::update('test_table');

        $this->assertInstanceOf(Update::class, $query);

        return $query;
    }

    /**
     * @depends testQueryReturnsUpdateBuilder
     */
    public function testValuesAreSet(Update $query): Update {
        $query->set([
            'foo' => 'hello',
            'bar' => 'world'
        ]);

        $query->set('quux', '!');

        $this->assertEquals('foo = ?, bar = ?, quux = ?', $query->getValues());

        return $query;
    }

    /**
     * @depends testValuesAreSet
     */
    public function testClausesAreSet(Update $query): Update {
        $query->where('message', '!=', 'hello world');

        $this->assertEquals('WHERE message != ?', $query->getWhere());

        return $query;
    }

    /**
     * @depends testClausesAreSet
     */
    public function testQueryString(Update $query) {
        $this->assertEquals(
            'UPDATE test_table SET foo = ?, bar = ?, quux = ? WHERE message != ?',
            $query->build()
        );
    }
}