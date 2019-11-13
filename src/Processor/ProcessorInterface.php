<?php

namespace p810\MySQL\Processor;

interface ProcessorInterface
{
    /**
     * Gets the callback handler for the given command
     * 
     * @param null|string $command A SQL command (e.g. `SELECT`)
     * @return callable
     */
    public function getHandler(?string $command): callable;

    /**
     * Sets the callback handler for a given command
     * 
     * @param callable $handler A callback that's invoked when the given command(s) are processed
     * @param string $command Which command this callback should be invoked to process. An asterisk may be given to
     *                        specify that this callback should handle all commands that don't have a specific handler.
     * @return void
     */
    public function setHandler(callable $handler, string $command = '*'): void;
}
