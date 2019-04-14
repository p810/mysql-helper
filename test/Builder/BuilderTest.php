<?php

namespace p810\MySQL\Test;

use p810\MySQL\Builder\Token;
use p810\MySQL\Builder\Select;
use p810\MySQL\Builder\Builder;
use PHPUnit\Framework\TestCase;

class BuilderTest extends TestCase
{
    public function test_select_builder()
    {
        $query = new Select;

        $query->from('users')
              ->select(['username', 'password'])
              ->where('username', 'Payton')
              ->whereOr('username', 'Anna');
        
        $this->assertEquals("select username, password from users where username = ? or username = ?", $query->build());
    }
}