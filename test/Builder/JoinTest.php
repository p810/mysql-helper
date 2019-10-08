<?php

namespace p810\MySQL\Test\Builder;

use p810\MySQL\Builder\Select;
use PHPUnit\Framework\TestCase;

class JoinTest extends TestCase
{
    public function test_single_join_using()
    {
        $query = new Select();

        $query->innerJoin('bans')
              ->using('user_id');
        
        $this->assertEquals('inner join bans using (user_id)', $query->build());
    }

    public function test_single_join_on()
    {
        $query = new Select();

        $query->innerJoin('bans')
              ->on('users.user_id', 'bans.issuer_id');
        
        $this->assertEquals('inner join bans on users.user_id = bans.issuer_id', $query->build());
    }

    public function test_multiple_join_on()
    {
        $query = new Select();

        $query->innerJoin('bans')
              ->on('users.user_id', 'bans.issuer_id')
              ->orOn('users.user_id', 'bans.user_id');
        
        $this->assertEquals('inner join bans on users.user_id = bans.issuer_id or users.user_id = bans.user_id', $query->build());
    }

    public function test_join_predicate_queue()
    {
        $query = new Select();

        $query->using('user_id')
              ->innerJoin('bans');
        
        $this->assertEquals('inner join bans using (user_id)', $query->build());
    }
}
