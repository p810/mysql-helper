<?php

namespace p810\MySQL\Mapper;

use p810\MySQL\ConnectionInterface;

class DefaultAdapter implements AdapterInterface
{
    /**
     * @var \p810\MySQL\ConnectionInterface
     */
    protected $database;

    /**
     * Injects a database connection
     * 
     * @param \p810\MySQL\ConnectionInterface $connection
     */
    function __construct(ConnectionInterface $connection)
    {
        $this->database = $connection;
    }

    /**
     * {@inheritdoc}
     */
    public function get(?string $source = null): object
    {
        $query = $this->database->select();

        if ($source) {
            $query->from($source);
        }

        return $query;
    }
}