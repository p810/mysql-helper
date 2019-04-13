<?php

namespace p810\MySQL\Test;

use p810\MySQL\Builder\Token;
use p810\MySQL\Builder\Select;
use p810\MySQL\Builder\Builder;
use PHPUnit\Framework\TestCase;

class BuilderTest extends TestCase
{
    /**
     * @var \stdClass<\p810\MySQL\Builder\Builder>
     */
    protected $builder;

    public function setUp()
    {
        $this->builder = new class extends Builder
        {
            protected $order = ['select', 'from', 'where', 'limit'];

            /**
             * @var string[]
             */
            protected $tokens;

            public function select($columns): self
            {
                if (is_array($columns)) {
                    $columns = implode(', ', $columns);
                }

                return $this->append(Token::SELECT, $columns);
            }

            public function from(string $table): self
            {
                return $this->append(Token::FROM, $table);
            }

            public function where(string $column, $value, string $operator = '='): self
            {
                return $this->append(Token::WHERE, $column, $operator, $value);
            }

            public function limit(int $limit): self
            {
                return $this->append(Token::LIMIT, $limit);
            }
        };
    }

    public function test_builder_construction_order()
    {
        $query = new Select;

        $query->from('users')
              ->select(['username', 'password'])
              ->where('username', 'Payton')
              ->or()
              ->where('username', 'Anna');
        
        $this->assertEquals("select username, password from users where username = Payton or username = Anna", $query->build());
    }
}