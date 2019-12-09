<?php

namespace p810\MySQL\Test\Builder;

use BadMethodCallException;
use PHPUnit\Framework\TestCase;
use p810\MySQL\Builder\Grammar\Join;
use p810\MySQL\Builder\AbstractBuilder;
use p810\MySQL\Builder\BuilderInterface;

class JoinTest extends TestCase
{
    protected function getMockQueryBuilder(): BuilderInterface
    {
        return new class extends AbstractBuilder
        {
            use Join;

            /** {@inheritdoc} */
            protected $components = [
                'join'
            ];

            /** {@inheritdoc} */
            public function getCommand(): ?string
            {
                return null;
            }
        };
    }

    public function test_single_join_using()
    {
        $query = $this->getMockQueryBuilder();

        $query->innerJoin('bans')
              ->using('user_id');
        
        $this->assertEquals('inner join bans using (user_id)', $query->build());
    }

    public function test_single_join_on()
    {
        $query = $this->getMockQueryBuilder();

        $query->innerJoin('bans')
              ->on('users.user_id', 'bans.issuer_id');
        
        $this->assertEquals('inner join bans on users.user_id = bans.issuer_id', $query->build());
    }

    public function test_multiple_join_on()
    {
        $query = $this->getMockQueryBuilder();

        $query->innerJoin('bans')
              ->on('users.user_id', 'bans.issuer_id')
              ->orOn('users.user_id', 'bans.user_id');
        
        $this->assertEquals('inner join bans on users.user_id = bans.issuer_id or users.user_id = bans.user_id', $query->build());
    }

    public function test_single_aliased_join_on()
    {
        $query = $this->getMockQueryBuilder()
            ->innerJoin('bans', 'b')
            ->on('b.user_id', 'a.user_id');

        $this->assertEquals('inner join bans as b on b.user_id = a.user_id', $query->build());
    }

    public function test_multiple_aliased_join_on_after_initial_call()
    {
        $query = $this->getMockQueryBuilder()
            ->innerJoin('bans')
            ->on('b.user_id', 'a.user_id')
            ->as('b')
            ->orOn('b.issuer_id', 'c.user_id');

        $this->assertEquals('inner join bans as b on b.user_id = a.user_id or b.issuer_id = c.user_id', $query->build());
    }

    public function test_join_on_not_equals()
    {
        $query = $this->getMockQueryBuilder()
            ->leftJoin('bans')
            ->onNotEquals('users.user_id', 'bans.issuer_id')
            ->orOnNotEquals('users.user_id', 'bans.user_id');
        
        $this->assertEquals('left join bans on users.user_id != bans.issuer_id or users.user_id != bans.user_id', $query->build());
    }

    public function test_join_on_like()
    {
        $query = $this->getMockQueryBuilder()
            ->rightJoin('bans')
            ->onLike('users.user_id', 'bans.issuer_id')
            ->orOnLike('users.user_id', 'bans.user_id');
        
        $this->assertEquals('right join bans on users.user_id like bans.issuer_id or users.user_id like bans.user_id', $query->build());
    }

    public function test_join_on_not_like()
    {
        $query = $this->getMockQueryBuilder()
            ->leftOuterJoin('bans')
            ->onNotLike('users.user_id', 'bans.issuer_id')
            ->orOnNotLike('users.user_id', 'bans.user_id');
        
        $this->assertEquals('left outer join bans on users.user_id not like bans.issuer_id or users.user_id not like bans.user_id', $query->build());
    }

    public function test_append_predicate_throws_exception_before_setter()
    {
        $query = $this->getMockQueryBuilder();

        $this->expectException(BadMethodCallException::class);

        $query->on('users.user_id', 'bans.user_id')->rightOuterJoin('bans');
    }
}
