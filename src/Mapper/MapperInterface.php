<?php

namespace p810\MySQL\Mapper;

interface MapperInterface
{
    /**
     * Injects an instance of \p810\MySQL\Mapper\AdapterInterface for the mapper to source from
     * 
     * @param \p810\MySQL\Mapper\AdapterInterface $adapter
     */
    function __construct(AdapterInterface $adapter);

    /**
     * Returns a new instance of the entity this mapper represents from the given data
     * 
     * @param array $state The data to build the entity from, e.g. a row from MySQL
     * @return null|object
     */
    public function from(array $data): ?object;

    /**
     * Returns a new instance of the entity this mapper represents, if the adapter has data where
     * the entity's unique identifier is equal to the given $id
     * 
     * Some mappers may not need this functionality and may ignore it
     * 
     * @param int $id
     * @return null|object
     */
    public function findById(int $id): ?object;

    /**
     * Updates the row with the given ID with the given entity for data
     * 
     * Some mappers may not need this functionality and may ignore it
     * 
     * @param int    $id     The ID of the record to update
     * @param object $entity The entity from which data should be pulled
     * @return bool
     */
    public function updateById(int $id, object $entity): bool;

    /**
     * Deletes the row with the given ID
     * 
     * Some mappers may not need this functionality and may ignore it
     * 
     * @param int $id The ID of the record to delete
     * @return bool
     */
    public function deleteById(int $id): bool;

    /**
     * Inserts a new row with data from the given entity
     * 
     * @param object $entity The entity from which data should be pulled
     * @return bool
     */
    public function create(object $entity): bool;
}
