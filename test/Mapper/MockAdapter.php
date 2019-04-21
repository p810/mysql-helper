<?php

namespace p810\MySQL\Test\Mapper;

use p810\MySQL\Mapper\EntityInterface;
use p810\MySQL\Mapper\AdapterInterface;

use function array_key_exists;

class MockAdapter implements AdapterInterface
{
    /**
     * @var object
     */
    private $builder;

    /**
     * Populates the adapter with dummy data
     */
    function __construct()
    {
        $this->builder = new class ($this->rows)
        {
            /**
             * @var array
             */
            protected $rows;

            /**
             * Populates the mock query builder with dummy data
             */
            function __construct() {
                $this->rows = [
                    ['id' => 1, 'message' => 'I am the first row'],
                    ['id' => 2, 'message' => 'I am the second row']
                ];
            }

            /**
             * Mocks a query builder's "where" functionality
             * 
             * @param string $column The key to search for
             * @param mixed  $value  A value associated with the key
             * @return null|array
             */
            public function where(string $column, $value): ?array {
                foreach ($this->rows as $row) {
                    if (array_key_exists($column, $row) && $row[$column] === $value) {
                        return $row;
                    }
                }

                return null;
            }
        };
    }

    /**
     * {@inheritdoc}
     */
    public function get(?string $source = null): object
    {
        return $this->builder;
    }
}