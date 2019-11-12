<?php

namespace p810\MySQL\Test\Builder;

use p810\MySQL\Builder\Select;
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

    public function test_where_equals()
    {
        $query = $this->getMockQueryBuilder()
            ->where('foo', 'bar');

        $this->assertEquals('where foo = ?', $query->build());
    }

    public function test_where_with_two_conditions()
    {
        $query = $this->getMockQueryBuilder()
            ->where('foo', 'bar')
            ->whereNotEquals('bam', 'baz');

        $this->assertEquals('where foo = ? and bam != ?', $query->build());
    }

    public function test_where_with_logical_or()
    {
        $query = $this->getMockQueryBuilder()
            ->where('foo', 'bar')
            ->orWhere('fem', 'fam');

        $this->assertEquals('where foo = ? or fem = ?', $query->build());
    }

    public function test_where_math_comparisons()
    {
        $query = $this->getMockQueryBuilder()
            ->whereLess('a', 1)
            ->orWhereLessOrEqual('a', 0)
            ->whereGreater('b', 1)
            ->orWhereGreaterOrEqual('b', 0);
        
        $this->assertEquals('where a < ? or a <= ? and b > ? or b >= ?', $query->build());
    }

    public function test_where_in_and_not_in()
    {
        $query = $this->getMockQueryBuilder()
            ->whereIn('id', [1, 2, 3, 4])
            ->whereNotIn('id', [5, 6]);

        $this->assertEquals('where id in (?, ?, ?, ?) and id not in (?, ?)', $query->build());
    }

    public function test_where_like()
    {
        $query = $this->getMockQueryBuilder()
            ->whereLike('quux', 'foobar')
            ->orWhereNotLike('bam', 'foo');

        $this->assertEquals('where quux like ? or bam not like ?', $query->build());
    }

    public function test_where_nested()
    {
        $query = $this->getMockQueryBuilder();
        
        $query->whereNested(function (BuilderInterface $q) {
            return $q->where('boo', 'far');
        });

        $this->assertEquals('where (boo = ?)', $query->build());
    }

    public function test_where_subquery()
    {
        $query = $this->getMockQueryBuilder();

        $query->whereNested(function (BuilderInterface $q) {
            $subquery = (new Select())->from('users');

            return $q->where('foo', $subquery);
        });

        $this->assertEquals('where (foo = ?)', $query->build());
    }
}
