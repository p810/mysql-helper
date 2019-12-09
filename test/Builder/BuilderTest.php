<?php

namespace p810\MySQL\Test;

use p810\MySQL\Builder\Select;
use p810\MySQL\Builder\Insert;
use p810\MySQL\Builder\Update;
use p810\MySQL\Builder\Delete;
use p810\MySQL\Builder\Replace;
use PHPUnit\Framework\TestCase;

class BuilderTest extends TestCase
{
    public function test_select_builder()
    {
        $query = new Select();

        $query->from('users')
              ->select(['username', 'password'])
              ->where('username', 'Payton')
              ->orWhere('username', 'Anna')
              ->orderBy('username')
              ->limit(2);
        
        $this->assertEquals("select username, password from users where username = ? or username = ? order by username desc limit 2", $query->build());
    }

    public function test_select_builder_with_multiple_tables()
    {
        $query = new Select();
        $temp = new Select();

        $temp->select()->from('posts');

        $query->select()->fromMany([
            'a' => 'users',
            'b' => $temp
        ]);

        $this->assertEquals('select * from users as a, (select * from posts) as b', $query->build());
    }

    public function test_select_builder_with_alias()
    {
        $query = new Select();

        $query->select()
            ->from('users', 'a')
            ->from('bans', 'b')
            ->where('a.user_id', 'b.user_id');
        
        $this->assertEquals('select * from users as a, bans as b where a.user_id = ?', $query->build());
    }

    public function test_select_builder_with_alias_after_initial_call()
    {
        $query = new Select();

        $query->select()
            ->from('books')
            ->as('a')
            ->from('topics', 'b')
            ->where('a.topic_id', 'b.topic_id');
        
        $this->assertEquals('select * from books as a, topics as b where a.topic_id = ?', $query->build());
    }

    public function test_select_builder_with_temporary_table()
    {
        $query = new Select();
        $temporary = new Select();

        $temporary->select('issuer_id')->from('bans')->where('user_id', 1);

        $query->select()
            ->from($temporary)
            ->as('a')
            ->innerJoin('users', 'b')
            ->on('a.issuer_id', 'b.user_id');

        $this->assertEquals(
            'select * from (select issuer_id from bans where user_id = ?) as a inner join users as b on a.issuer_id = b.user_id',
            $query->build()
        );
    }

    public function test_insert_builder_single_row()
    {
        $query = new Insert();

        $query->into('users')
              ->values(['Payton', 'password'])
              ->highPriority()
              ->ignore()
              ->onDuplicateKeyUpdate('foo', 'bar');
        
        $this->assertEquals('insert high_priority ignore into users values (?, ?) on duplicate key update foo = ?', $query->build());
    }

    public function test_insert_builder_with_multiple_rows()
    {
        $query = new Insert();

        $query->into('users')
              ->columns(['username', 'password'])
              ->lowPriority()
              ->values(
                  ['Payton', 'password'],
                  ['Anna', 'abc123']
              );
        
        $this->assertEquals('insert low_priority into users (username, password) values (?, ?), (?, ?)', $query->build());
    }

    public function test_update_builder_with_single_set()
    {
        $query = new Update();

        $query->update('users')
              ->set('username', 'Carl')
              ->where('username', 'Payton');
        
        $this->assertEquals('update users set username = ? where username = ?', $query->build());
    }

    public function test_update_builder_with_multiple_set()
    {
        $query = new Update();

        $query->update('users')
              ->setMany([
                  'username' => 'Carl',
                  'password' => 'hatestherain'
              ])
              ->where('username', 'Payton');
        
        $this->assertEquals('update users set username = ?, password = ? where username = ?', $query->build());
    }

    public function test_delete_builder()
    {
        $query = new Delete();

        $query->from('users')
              ->whereNot('username', 'Carl');
        
        $this->assertEquals('delete from users where username != ?', $query->build());
    }

    public function test_replace_builder_with_assignment_list()
    {
        $query = new Replace();

        $query->into('users')
              ->set('username', 'Carl')
              ->set('password', 'abc123');
        
        $this->assertEquals('replace into users set username = ?, password = ?', $query->build());
    }

    public function test_replace_builder_with_value_list()
    {
        $query = new Replace();

        $query->into('users')
              ->columns(['username', 'password'])
              ->values(['Carl', 'abc123']);
        
        $this->assertEquals('replace into users (username, password) values (?, ?)', $query->build());
    }
}
