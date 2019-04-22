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
    public function get(string $source): object
    {
        return $this->database->select()->from($source);
    }

    /**
     * {@inheritdoc}
     */
    public function create(string $source, array $data): object
    {
        return $this->database->insert($data)->into($source);
    }

    /**
     * {@inheritdoc}
     */
    public function save(string $source, ?array $data = null): object
    {
        $query = $this->database->update($source);

        if ($data) {
            $query->set($data);
        }

        return $query;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(string $source): object
    {
        return $this->database->delete($source);
    }

    /**
     * {@inheritdoc}
     */
    public function query(string $query, array $input = [])
    {
        return $this->database->raw($query, $input);
    }
}