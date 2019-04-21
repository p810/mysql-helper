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
     * Returns a new \p810\MySQL\Mapper\EntityInterface if the given ID has a corresponding
     * entry in the data source used by the adapter
     * 
     * @param int $id
     * @return null|object
     */
    public function findById(int $id): ?object;
}
