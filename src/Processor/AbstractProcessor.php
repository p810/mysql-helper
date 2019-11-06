<?php

namespace p810\MySQL\Processor;

abstract class AbstractProcessor implements ProcessorInterface
{
    /**
     * @var array<string,callable>
     * @psalm-suppress PropertyNotSetInConstructor
     */
    protected $callbacks;

    /**
     * {@inheritdoc}
     */
    public function getHandler(?string $command): callable
    {
        $command = $command ?: '*';

        $handler = $this->callbacks[$command] ?? $this->callbacks['*'];

        return $handler;
    }

    /**
     * {@inheritdoc}
     */
    public function setHandler(callable $handler, string $command = '*'): void
    {
        $this->callbacks[$command] = $handler;
    }
}
