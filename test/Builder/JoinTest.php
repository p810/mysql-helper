<?php

namespace p810\MySQL\Test\Builder;

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

    public function test_join_predicate_queue()
    {
        $query = $this->getMockQueryBuilder();

        $query->using('user_id')
              ->innerJoin('bans');
        
        $this->assertEquals('inner join bans using (user_id)', $query->build());
    }
}
