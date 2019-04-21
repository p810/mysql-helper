<?php

namespace p810\MySQL\Mapper;

interface AdapterInterface
{
    /**
     * Returns an object that can be used to fluently fetch data
     * 
     * @param string|null $source An optional source (e.g. table) to fetch data from
     * @return object
     */
    public function get(?string $source = null): object;
}
