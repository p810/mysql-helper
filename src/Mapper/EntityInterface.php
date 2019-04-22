<?php

namespace p810\MySQL\Mapper;

interface EntityInterface
{
    /**
     * Returns a new instance of the entity from the given data
     * 
     * @param array $state An associative array of data to construct the entity with
     * @return \p810\MySQL\Mapper\EntityInterface
     */
    public static function from(array $state): EntityInterface;

    /**
     * Returns an associative array of the entity's data
     * 
     * @return array
     */
    public function toArray(): array;
}