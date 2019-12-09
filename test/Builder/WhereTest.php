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

    public function test_where_between()
    {
        $query = $this->getMockQueryBuilder()
            ->whereBetween(2, 1, 3)
            ->orWhereBetween('b', 'a', 'c');
        
        $this->assertEquals('where 2 between ? or b between ?', $query->build());
    }

    public function test_where_between_with_subquery()
    {
        $select = (new Select())
            ->columns('*')
            ->from('numbers')
            ->where('number', 2);
        $query = $this->getMockQueryBuilder()->whereBetween($select, 1, 3);

        $this->assertEquals('where (select * from numbers where number = ?) between ?', $query->build());
    }

    public function test_where_not_between()
    {
        $query = $this->getMockQueryBuilder()
            ->whereNotBetween(2, 1, 3)
            ->orWhereNotBetween('b', 'a', 'c');
        
        $this->assertEquals('where 2 not between ? or b not between ?', $query->build());
    }

    public function test_where_not_between_with_subquery()
    {
        $select = (new Select())
            ->columns('*')
            ->from('numbers')
            ->where('number', 2);
        $query = $this->getMockQueryBuilder()->whereNotBetween($select, 3, 5);

        $this->assertEquals('where (select * from numbers where number = ?) not between ?', $query->build());
    }

    public function test_where_is()
    {
        $query = $this->getMockQueryBuilder()
            ->whereIs('1', true)
            ->orWhereIs('true', 'true')
            ->whereIsFalse('0')
            ->orWhereIsFalse('false')
            ->whereIsUnknown('foo')
            ->orWhereIsUnknown('baz')
            ->whereIsNull('bar')
            ->orWhereIsNull('bam');

        $this->assertEquals(
            'where ? is true or ? is true and ? is false or ? is false and ? is unknown or ? is unknown and ? is null or ? is null',
            $query->build()
        );
    }

    public function test_where_not_is()
    {
        $query = $this->getMockQueryBuilder()
            ->whereIsNot('0', true)
            ->orWhereIsNot('false', 'true')
            ->whereIsNotFalse('1')
            ->orWhereIsNotFalse(true)
            ->whereIsNotUnknown('1')
            ->orWhereIsNotUnknown(false)
            ->whereIsNotNull(true)
            ->orWhereIsNotNull('false');

        $this->assertEquals(
            'where ? is not true or ? is not true and ? is not false or ? is not false and ? is not unknown or ? is not unknown and ? is not null or ? is not null',
            $query->build()
        );
    }

    public function test_where_null_safe()
    {
        $query = $this->getMockQueryBuilder()
            ->whereNullSafe('foo', null)
            ->orWhereNullSafe('bar', '1')
            ->whereNullSafeEquals('bam', '2')
            ->orWhereNullSafeEquals('baz', '3');

        $this->assertEquals('where foo <=> ? or bar <=> ? and bam <=> ? or baz <=> ?', $query->build());
    }
}
