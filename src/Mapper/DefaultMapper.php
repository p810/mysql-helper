<?php

namespace p810\MySQL\Mapper;

use PDO;
use PDOStatement;
use LogicException;
use p810\MySQL\Query;
use p810\MySQL\ConnectionInterface;

use function array_map;

class DefaultMapper implements MapperInterface
{
    /**
     * @var string
     * @psalm-suppress PropertyNotSetInConstructor
     */
    public $table;

    /**
     * @var string
     * @psalm-suppress PropertyNotSetInConstructor
     */
    public $key;

    /**
     * @var string
     * @psalm-suppress PropertyNotSetInConstructor
     */
    protected $entity;

    /**
     * @var \p810\MySQL\ConnectionInterface
     */
    protected $adapter;

    /**
     * {@inheritdoc}
     */
    function __construct(ConnectionInterface $connection)
    {
        $this->adapter = $connection;

        /** @psalm-suppress UninitializedProperty */
        if (! $this->table || ! $this->entity) {
            throw new LogicException(
                'Children of \p810\MySQL\Mapper\DefaultMapper must define their $table and $entity to use certain functionality'
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function create(EntityInterface $entity, ?callable $cb = null): bool
    {
        $query = $this->adapter->insert($entity->toArray())->into($this->table);

        if ($cb) {
            $query = $cb($query, $entity);
        }

        return $query->execute() != false;
    }

    /**
     * {@inheritdoc}
     */
    public function read(?callable $cb = null): ?array
    {
        $query = $this->adapter->select('*')->from($this->table);

        if ($cb) {
            $query = $cb($query);
        }

        $result = $query->execute();

        if ($result) {
            return array_map(function ($row) {
                return $this->getEntityFrom($row);
            }, $result);
        }

        return null;
    }

    /**
     * Queries for a row with the given ID and returns an entity if successful
     * 
     * @param int $id
     * @return null|\p810\MySQL\Mapper\EntityInterface
     * @throws \LogicException if the mapper's $key property is not defined
     */
    public function findById(int $id): ?EntityInterface
    {
        $this->requireKeyToBeSetFor('findById');

        return $this->first(function (Query $q) use ($id) {
            return $q->where($this->key, $id);
        });
    }

    /**
     * Retrieves the first result from a call to `\p810\MySQL\Mapper\DefaultMapper::read()`
     * 
     * @param null|callable $cb An optional callback used to modify the query
     * @return null|\p810\MySQL\Mapper\EntityInterface
     */
    public function first(?callable $cb = null): ?EntityInterface
    {
        $results = $this->read($cb);

        return $results[0] ?? null;
    }

    /**
     * {@inheritdoc}
     */
    public function update(EntityInterface $entity, ?callable $cb = null): bool
    {
        $query = $this->adapter->update($this->table)->setMany($entity->toArray());

        if ($cb) {
            $query = $cb($query, $entity);
        }

        return $query->execute() != false;
    }

    /**
     * Updates an entity's row by a certain ID
     * 
     * @param int $id The ID of the row being updated
     * @param \p810\MySQL\Mapper\EntityInterface The entity from which to source data for the update
     * @return bool
     */
    public function updateById(int $id, EntityInterface $entity): bool
    {
        $this->requireKeyToBeSetFor('updateById');

        return $this->update($entity, function (Query $q, EntityInterface $entity) use ($id) {
            return $q->where($this->key, $id);
        });
    }

    /**
     * {@inheritdoc}
     */
    public function delete(EntityInterface $entity, ?callable $cb = null): bool
    {
        $query = $this->adapter->delete($this->table);

        if ($cb) {
            $query = $cb($query, $entity);
        }

        return $query->execute() != false;
    }

    /**
     * Deletes an entity's row by a certain ID
     * 
     * @param int $id The ID of the row being deleted
     * @return bool
     */
    public function deleteById(int $id): bool
    {
        $this->requireKeyToBeSetFor('deleteById');

        $query = $this->adapter->delete($this->table)->where($this->key, $id);

        return $query->execute() != false;
    }

    /**
     * {@inheritdoc}
     */
    public function query(string $query, array $input = [])
    {
        return $this->adapter->query($query, $input);
    }

    /**
     * Fetches the ID of the last inserted row, if applicable
     * 
     * @todo Investigate whether it's possible to use LAST_INSERT_ID() over this query (currently it's returning 0 each
     *       time due to some limitation with the driver)
     * @return null|int
     */
    public function lastInsertId(): ?int
    {
        $this->requireKeyToBeSetFor('lastInsertId');

        $query = $this->adapter
            ->select($this->key)
            ->from($this->table)
            ->orderBy($this->key)
            ->limit(1);
        
        return $query->execute(function (?PDOStatement $statement): ?int {
            if (! $statement) {
                return null;
            }

            $rows = $statement->fetch(PDO::FETCH_ASSOC);

            return (int) $rows[$this->key];
        }, true);
    }

    /**
     * Returns a new instance of the entity represented by the mapper
     * 
     * @param array $state The data used to construct the entity
     * @return \p810\MySQL\Mapper\EntityInterface 
     */
    protected function getEntityFrom(array $state): EntityInterface
    {
        return ($this->entity)::from($state);
    }

    /**
     * Raises a \LogicException for the given $method if the property $key is not set on the mapper
     * 
     * @param string $method The method that requires the $key property be set
     * @return void
     * @throws \LogicException if the mapper has not specified a value for its $key
     */
    protected function requireKeyToBeSetFor(string $method = null): void
    {
        if (! $this->key) {
            throw new LogicException(
                "\p810\MySQL\Mapper\DefaultMapper::$method() failed: A key is required and must be specified in Mapper::\$key"
            );
        }
    }
}
