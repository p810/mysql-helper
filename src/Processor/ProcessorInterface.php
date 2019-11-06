<?php

namespace p810\MySQL\Processor;

interface ProcessorInterface
{
    /**
     * Gets the callback handler for the given command
     * 
     * @param string $command
     * @return callable
     */
    public function getHandler(?string $command): callable;

    /**
     * Sets the callback handler for a given command
     * 
     * @param callable $handler
     * @param string $command
     * @return void
     */
    public function setHandler(callable $handler, string $command = '*'): void;
}
