<?php

namespace p810\MySQL\Builder;

use function ucfirst;
use function is_array;
use function array_map;
use function p810\MySQL\spaces;

abstract class AbstractBuilder implements BuilderInterface
{
    /**
     * @var string[]
     */
    protected $components = [];

    /**
     * @var mixed[]
     */
    protected $input = [];

    /**
     * @var array<string,mixed>
     */
    protected $parameters = [];

    /**
     * {@inheritdoc}
     */
    public function bind($value)
    {
        if (is_array($value)) {
            return array_map(function ($value) {
                return $this->bind($value);
            }, $value);
        }

        $this->input[] = $value;

        return '?';
    }

    /**
     * {@inheritdoc}
     */
    public function build(): string
    {
        $parts = [];

        $command = $this->getCommand();

        if ($command) {
            $parts[] = $command;
        }

        foreach ($this->components as $component) {
            $method = 'compile' . ucfirst($component);

            if ($compiledPart = $this->$method()) {
                $parts[] = $compiledPart;
            }
        }

        return spaces($parts);
    }

    /**
     * {@inheritdoc}
     */
    public function getInput(): array
    {
        return $this->input;
    }

    /**
     * {@inheritdoc}
     */
    public function getParameter(string $param)
    {
        if (! array_key_exists($param, $this->parameters)) {
            return null;
        }

        return $this->parameters[$param];
    }

    /**
     * {@inheritdoc}
     */
    public function setParameter(string $param, $value): BuilderInterface
    {
        $this->parameters[$param] = $value;

        return $this;
    }

    /**
     * Returns the query as a string
     * 
     * @return string
     */
    function __toString(): string
    {
        return $this->build();
    }
}
