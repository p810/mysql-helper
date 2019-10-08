<?php

namespace p810\MySQL\Test\Builder;

use p810\MySQL\Builder\Select;
use p810\MySQL\Builder\Builder;
use PHPUnit\Framework\TestCase;

class WhereTest extends TestCase
{
    public function test_where_equals(): Select
    {
        $query = new Select();

        $query->where('foo', 'bar');

        $this->assertEquals('where foo = ?', $query->build());

        return $query;
    }

    /**
     * @depends test_where_equals
     */
    public function test_where_with_two_conditions(Select $query): Select
    {
        $query->whereNotEquals('bam', 'baz');

        $this->assertEquals('where foo = ? and bam != ?', $query->build());

        return $query;
    }

    /**
     * @depends test_where_with_two_conditions
     */
    public function test_where_with_logical_or(Select $query): Select
    {
        $query->orWhere('fem', 'fam');

        $this->assertEquals('where foo = ? and bam != ? or fem = ?', $query->build());

        return $query;
    }

    /**
     * @depends test_where_with_logical_or
     */
    public function test_where_math_comparisons(Select $query): Select
    {
        $query->whereLess('a', 1)
              ->orWhereLessOrEqual('a', 0)
              ->whereGreater('b', 1)
              ->orWhereGreaterOrEqual('b', 0);
        
        $this->assertEquals(
            'where foo = ? and bam != ? or fem = ? and a < ? or a <= ? and b > ? or b >= ?',
            $query->build()
        );

        return $query;
    }

    /**
     * @depends test_where_math_comparisons
     */
    public function test_where_in_and_not_in(Select $query): Select
    {
        $query->whereIn('id', [1, 2, 3, 4])
              ->whereNotIn('id', [5, 6]);

        $this->assertEquals(
            'where foo = ? and bam != ? or fem = ? and a < ? or a <= ? and b > ? or b >= ? and id in (?, ?, ?, ?) and id not in (?, ?)',
            $query->build()
        );

        return $query;
    }

    /**
     * @depends test_where_in_and_not_in
     */
    public function test_where_like(Select $query): Select
    {
        $query->whereLike('quux', 'foobar');

        $this->assertEquals(
            'where foo = ? and bam != ? or fem = ? and a < ? or a <= ? and b > ? or b >= ? and id in (?, ?, ?, ?) and id not in (?, ?) and quux like ?',
            $query->build()  
        );

        return $query;
    }

    /**
     * @depends test_where_like
     */
    public function test_where_nested(Select $query): Select
    {
        $query->whereNested(function (Builder $q) {
            return $q->where('boo', 'far');
        });

        $this->assertEquals(
            'where foo = ? and bam != ? or fem = ? and a < ? or a <= ? and b > ? or b >= ? and id in (?, ?, ?, ?) and id not in (?, ?) and quux like ? and (boo = ?)',
            $query->build()
        );

        return $query;
    }

    /**
     * @depends test_where_nested
     */
    public function test_logical_operator_chaining_with_methods(Select $query)
    {
        $query->or()->where('string_is', 'comically_long');

        $this->assertEquals(
            'where foo = ? and bam != ? or fem = ? and a < ? or a <= ? and b > ? or b >= ? and id in (?, ?, ?, ?) and id not in (?, ?) and quux like ? and (boo = ?) or string_is = ?',
            $query->build()
        );
    }
}
