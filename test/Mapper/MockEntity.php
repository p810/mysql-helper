<?php

namespace p810\MySQL\Test\Mapper;

class MockEntity
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
}
