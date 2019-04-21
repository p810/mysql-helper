<?php

namespace p810\MySQL\Test;

use p810\MySQL\Builder\Select;
use p810\MySQL\Builder\Insert;
use p810\MySQL\Builder\Update;
use p810\MySQL\Builder\Delete;
use p810\MySQL\Builder\Replace;
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

    public function test_select_builder_with_nested_where()
    {
        $query = new Select;

        $query->from('users')
              ->select('username')
              ->where('user_id', 1)
              ->whereNested(function ($query) {
                  return $query->where('user_id', 2)->orWhere('user_id', 3);
              })
              ->where('user_id', 4);
        
        $this->assertEquals('select username from users where user_id = ? and (user_id = ? or user_id = ?) and user_id = ?', $query->build());
    }

    public function test_select_builder_with_nested_where_as_first_clause()
    {
        $query = new Select;

        $query->from('users')
              ->select('username')
              ->whereNested(function ($query) {
                  return $query->where('user_id', 1)->orWhere('user_id', 2);
              })
              ->orWhere('user_id', 3);
        
        $this->assertEquals('select username from users where (user_id = ? or user_id = ?) or user_id = ?', $query->build());
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
              ->values(
                  ['Payton', 'password'],
                  ['Anna', 'abc123']
              );
        
        $this->assertEquals('insert into users (username, password) values (?, ?), (?, ?)', $query->build());
    }

    public function test_update_builder_with_single_set()
    {
        $query = new Update;

        $query->update('users')
              ->set('username', 'Carl')
              ->where('username', 'Payton');
        
        $this->assertEquals('update users set username = ? where username = ?', $query->build());
    }

    public function test_update_builder_with_multiple_set()
    {
        $query = new Update;

        $query->update('users')
              ->set([
                  'username' => 'Carl',
                  'password' => 'hatestherain'
              ])
              ->where('username', 'Payton');
        
        $this->assertEquals('update users set username = ?, password = ? where username = ?', $query->build());
    }

    public function test_delete_builder()
    {
        $query = new Delete;

        $query->from('users')
              ->whereNot('username', 'Carl');
        
        $this->assertEquals('delete from users where username != ?', $query->build());
    }

    public function test_replace_builder()
    {
        $query = new Replace;

        $query->into('users')
              ->set('username', 'Carl')
              ->set('password', 'abc123');
        
        $this->assertEquals('replace into users set username = ?, password = ?', $query->build());
    }
}