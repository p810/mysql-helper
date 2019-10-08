<?php

namespace p810\MySQL\Test\Mapper;

use OutOfBoundsException;
use p810\MySQL\Mapper\EntityInterface;

use function array_key_exists;

class MockEntity implements EntityInterface
{
    /**
     * @var string
     */
    public $message;

    /**
     * Creates a new mock entity with the given message
     * 
     * @param string $message
     */
    function __construct(string $message)
    {
        $this->message = $message;
    }

    /**
     * {@inheritdoc}
     * @throws \OutOfBoundsException if the given data does not include a "message" key
     */
    public static function from(array $state): EntityInterface
    {
        if (! array_key_exists('message', $state)) {
            throw new OutOfBoundsException();
        }
        
        return new self($state['message']);
    }

    /**
     * {@inheritdoc}
     */
    public function toArray(): array
    {
        return (array) $this;
    }
}
