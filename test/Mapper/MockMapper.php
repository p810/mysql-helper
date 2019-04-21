<?php

namespace p810\MySQL\Test\Mapper;

use p810\MySQL\Mapper\MapperInterface;
use p810\MySQL\Mapper\AdapterInterface;

class MockMapper implements MapperInterface
{
    /**
     * @var \p810\MySQL\Mapper\AdapterInterface
     */
    protected $adapter;

    /**
     * {@inheritdoc}
     */
    function __construct(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * {@inheritdoc}
     */
    public function findById(int $id): ?object
    {
        $row = $this->adapter->get()->where('id', $id);

        if (! $row) {
            return null;
        }

        return $this->from($row);
    }

    /**
     * An example of a custom mapper method that searches for data by a value
     * 
     * @param string $message
     * @return null|\p810\MySQL\Test\Mapper\MockEntity
     */
    public function findByMessage(string $message): ?MockEntity
    {
        $row = $this->adapter->get()->where('message', $message);

        if (! $row) {
            return null;
        }

        return $this->from($row);
    }

    /**
     * {@inheritdoc}
     */ 
    public function from(array $state): object
    {
        return new MockEntity($state['message']);
    }
}