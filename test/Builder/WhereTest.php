<?php

namespace p810\MySQL\Test\Builder;

use PHPUnit\Framework\TestCase;
use p810\MySQL\Builder\Grammar\Where;
use p810\MySQL\Builder\AbstractBuilder;
use p810\MySQL\Builder\BuilderInterface;

class WhereTest extends TestCase
{
    /**
     * @todo Find a better way of storing/handling mocks, probably as part of my eventual effort to fix up the test
     * suite overall
     */
    protected function getMockQueryBuilder(): BuilderInterface
    {
        return new class extends AbstractBuilder
        {
            use Where;

            /** {@inheritdoc} */
            protected $components = [
                'where'
            ];

            /** {@inheritdoc} */
            public function getCommand(): ?string
            {
                return null;
            }
        };
    }

    public function test_where_equals(): BuilderInterface
    {
        $query = $this->getMockQueryBuilder();

        $query->where('foo', 'bar');

        $this->assertEquals('where foo = ?', $query->build());

        return $query;
    }

    /**
     * @depends test_where_equals
     */
    public function test_where_with_two_conditions(BuilderInterface $query): BuilderInterface
    {
        $query->whereNotEquals('bam', 'baz');

        $this->assertEquals('where foo = ? and bam != ?', $query->build());

        return $query;
    }

    /**
     * @depends test_where_with_two_conditions
     */
    public function test_where_with_logical_or(BuilderInterface $query): BuilderInterface
    {
        $query->orWhere('fem', 'fam');

        $this->assertEquals('where foo = ? and bam != ? or fem = ?', $query->build());

        return $query;
    }

    /**
     * @depends test_where_with_logical_or
     */
    public function test_where_math_comparisons(BuilderInterface $query): BuilderInterface
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
    public function test_where_in_and_not_in(BuilderInterface $query): BuilderInterface
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
    public function test_where_like(BuilderInterface $query): BuilderInterface
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
    public function test_where_nested(BuilderInterface $query)
    {
        $query->whereNested(function (BuilderInterface $q) {
            return $q->where('boo', 'far');
        });

        $this->assertEquals(
            'where foo = ? and bam != ? or fem = ? and a < ? or a <= ? and b > ? or b >= ? and id in (?, ?, ?, ?) and id not in (?, ?) and quux like ? and (boo = ?)',
            $query->build()
        );
    }
}
