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
     * @var array
     */
    protected $input = [];

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
     * Returns the query as a string
     * 
     * @return string
     */
    function __toString(): string
    {
        return $this->build();
    }
}
