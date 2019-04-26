<?php

namespace p810\MySQL;

abstract class Processor
{
    /**
     * @var array<string,callable>
     */
    protected $callbacks;

    /**
     * @param string $command
     * @return mixed
     */
    public function getHandler(string $command)
    {
        $handler = $this->callbacks[$command] ?? $this->callbacks['*'];

        return $handler;
    }

    /**
     * @param callable $handler
     * @param string   $command
     * @return void
     */
    public function setHandler(callable $handler, string $command = '*'): void
    {
        $this->callbacks[$command] = $handler;
    }
}
