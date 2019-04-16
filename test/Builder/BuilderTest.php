<?php

namespace p810\MySQL\Test;

use p810\MySQL\Builder\Select;
use p810\MySQL\Builder\Insert;
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
              ->orWhere('username', 'Anna')
              ->orderBy('username')
              ->limit(2);
        
        $this->assertEquals("select username, password from users where username = ? or username = ? order by username desc limit 2", $query->build());
    }

    public function test_select_builder_with_join()
    {
        $query = new Select;

        $query->select([
            'users' => 'username',
            'bans' => 'is_banned'
        ]);

        $query->from('users')
              ->innerJoin('bans')
              ->using('user_id');
        
        $this->assertEquals('select users.username, bans.is_banned from users inner join bans using (user_id)', $query->build());
    }

    public function test_select_builder_with_multiple_joins()
    {
        $query = new Select;

        $query->select('username')
              ->from('users')
              ->innerJoin('userdata')
              ->on('users.user_id', 'userdata.user_id')
              ->innerJoin('permissions')
              ->on('users.user_id', 'permissions.user_id')
              ->on('userdata.foo_id', 'permissions.foo_id');
        
        $this->assertEquals('select username from users inner join userdata on users.user_id = userdata.user_id inner join permissions on users.user_id = permissions.user_id and userdata.foo_id = permissions.foo_id', $query->build());
    }

    public function test_insert_builder_single_row()
    {
        $query = new Insert;

        $query->into('users')
              ->values(['Payton', 'password']);
        
        $this->assertEquals('insert into users values (?, ?)', $query->build());
    }

    public function test_insert_builder_with_multiple_rows()
    {
        $query = new Insert;

        $query->into('users')
              ->columns(['username', 'password'])
              ->values([
                  ['Payton', 'password'],
                  ['Anna', 'abc123']
              ]);
        
        $this->assertEquals('insert into users (username, password) values (?, ?), (?, ?)', $query->build());
    }
}