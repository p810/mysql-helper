<?php

namespace p810\MySQL\Mapper;

use LogicException;

use function array_map;

abstract class DefaultMapper implements MapperInterface
{
    /**
     * A key (column) that can be used to uniquely identify an entity in MySQL
     * 
     * @var string
     */
    public $key;

    /**
     * The name of the table where rows of this entity are stored
     * 
     * @var string
     */
    public $table;

    /**
     * The class name of the entity this mapper represents
     * 
     * @var string
     */
    protected $entity;

    /**
     * {@inheritdoc}
     */
    function __construct(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;

        if (! $this->table || ! $this->entity) {
            throw new LogicException('A child of \p810\MySQL\Mapper\DefaultMapper must specify the table and entity it represents');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function create(EntityInterface $entity): bool
    {
        $created = $this->adapter->create($this->table, $entity->toArray())->execute();

        return $created === 1;
    }

    /**
     * {@inheritdoc}
     */
    public function query(string $query, array $input = [])
    {
        return $this->adapter->query($query, $input);
    }

    /**
     * {@inheritdoc}
     */
    public function get(?callable $cb = null, $columns = '*'): ?array
    {
        $query = $this->adapter->get($this->table)->columns($columns);

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
     * {@inheritdoc}
     */
    public function findById(int $id): ?EntityInterface
    {
        if ($this->key) {
            $result = $this->adapter->get($this->table)->where($this->key, $id)->execute();

            if ($result) {
                return $this->getEntityFrom($result[0]);
            }
        }

        return null;
    }

    /**
     * Updates the row with the given ID with the given entity for data
     * 
     * @param int $id The ID of the record to update
     * @param \p810\MySQL\Mapper\EntityInterface $entity The entity from which data should be pulled
     * @return bool
     */
    public function updateById(int $id, EntityInterface $entity): bool
    {
        if ($this->key) {
            $query = $this->adapter->save($this->table, $entity->toArray());
            $query->where($this->key, $id);
            
            $rowCount = $query->execute();

            return $rowCount !== false && $rowCount >= 1;
        }

        return false;
    }

    /**
     * Deletes the row with the given ID
     * 
     * @param int $id The ID of the record to delete
     * @return bool
     */
    public function deleteById(int $id): bool
    {
        if ($this->key) {
            $rowCount = $this->adapter->delete($this->table)->where($this->key, $id);

            return $rowCount !== false && $rowCount >= 1;
        }

        return false;
    }

    /**
     * Dynamically instantiates an object of \p810\MySQL\Mapper\EntityInterface with the given data
     * 
     * @param array $state An associative array mapping properties to values for the new entity
     * @return \p810\MySQL\Mapper\EntityInterface
     */
    protected function getEntityFrom(array $state): EntityInterface
    {
        return ($this->entity)::from($state);
    }
}