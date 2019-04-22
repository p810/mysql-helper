<?php

namespace p810\MySQL\Mapper;

interface AdapterInterface
{
    /**
     * Returns an object that can be used to fluently fetch data
     * 
     * @param string $source An optional source (e.g. table) to fetch data from
     * @return object
     */
    public function get(string $source): object;

    /**
     * Returns an object that can be used to fluently create records in the data store
     * 
     * @param string $source Where to save the data e.g. a table name
     * @param array  $data   Data to create the record with
     * @return object
     */
    public function create(string $source, array $data): object;

    /**
     * Returns an object that can be used to fluently update records in the data store
     * 
     * @param string     $source Where to save the data e.g. a table name
     * @param array|null $data   Optional data to update the record with
     * @return object
     */
    public function save(string $source, ?array $data = null): object;

    /**
     * Returns an object that can be used to fluently delete a record(s) from the data store
     * 
     * @param string $source An optional source, e.g. a table name, to remove data from
     * @return object
     */
    public function delete(string $source): object;
}
