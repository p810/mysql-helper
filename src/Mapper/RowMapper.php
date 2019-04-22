<?php

namespace p810\MySQL\Mapper;

use function array_key_exists;

class RowMapper extends DefaultMapper
{
    /**
     * {@inheritdoc}
     */
    protected function getEntityFrom(array $state): EntityInterface
    {
        $entity = parent::getEntityFrom($state);

        $row = new Row($this, $entity);

        if ($this->key && array_key_exists($this->key, $state)) {
            $row->{$this->key} = $state[$this->key];
        }

        return $row;
    }
}
