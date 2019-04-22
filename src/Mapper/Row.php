<?php

namespace p810\MySQL\Mapper;

use BadMethodCallException;

use function is_scalar;
use function array_merge;
use function array_filter;
use function property_exists;

class Row implements EntityInterface
{
    /**
     * @var \p810\MySQL\Mapper\MapperInterface
     */
    protected $mapper;

    /**
     * @var \p810\MySQL\Mapper\EntityInterface
     */
    protected $entity;

    /**
     * Injects dependencies necessary for this Row to be built
     * 
     * @param \p810\MySQL\Mapper\MapperInterface $mapper
     * @param \p810\MySQL\Mapper\EntityInterface $entity
     */
    function __construct(MapperInterface $mapper, EntityInterface $entity)
    {
        $this->mapper = $mapper;
        $this->entity = $entity;
    }

    /**
     * Searches either this object or the injected entity for the requested property
     * 
     * @param string $property
     * @return null|mixed
     */
    function __get(string $property)
    {
        return $this->$property ?? $this->entity->$property;
    }

    /**
     * Allows properties to be overridden on the injected entity
     * 
     * @param string $property
     * @param mixed  $value
     * @return void
     */
    function __set(string $property, $value)
    {
        if (property_exists($this->entity, $property)) {
            $this->entity->$property = $value;
        } else {
            $this->$property = $value;
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function from(array $state): EntityInterface
    {
        throw new BadMethodCallException;
    }

    /**
     * {@inheritdoc}
     */
    public function toArray(): array
    {
        return array_filter($this->getCombinedProperties(), function ($value) {
            return is_scalar($value);
        });
    }

    /**
     * Saves any changes made to the entity
     * 
     * @return bool
     */
    public function save(): bool
    {
        if ($this->hasUniqueKey()) {
            return $this->mapper->updateById($this->getUniqueKey(), $this->entity);
        }

        return false;
    }

    /**
     * Deletes the row this object represents
     * 
     * @return bool
     */
    public function delete(): bool
    {
        if ($this->hasUniqueKey()) {
            return $this->mapper->deleteById($this->getUniqueKey());
        }

        return false;
    }

    /**
     * Returns the properties of the entity this row represents and any
     * properties defined specifically on the object
     * 
     * @return array
     */
    protected function getCombinedProperties(): array
    {
        return array_merge((array) $this, $this->entity->toArray());
    }

    /**
     * Returns a boolean indicating whether the object has a primary or unique key
     * 
     * @return bool
     */
    protected function hasUniqueKey(): bool
    {
        if ($this->mapper->key) {
            return property_exists($this, $this->mapper->key);
        }

        return false;
    }

    /**
     * Returns the row's unique key if applicable
     * 
     * @return null|mixed
     */
    protected function getUniqueKey()
    {
        if ($this->hasUniqueKey()) {
            return $this->{$this->mapper->key};
        }

        return null;
    }
}