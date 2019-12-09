<?php

namespace p810\MySQL\Builder;

use function ucfirst;
use function is_array;
use function array_map;
use function p810\MySQL\keywordToString;
use function p810\MySQL\parentheses;
use function p810\MySQL\spaces;
use function array_push;

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
     * @var null|string
     */
    protected $table;

    /**
     * {@inheritdoc}
     */
    public function bind($value)
    {
        if (is_array($value)) {
            return array_map([$this, 'bind'], $value);
        }

        $this->input[] = keywordToString($value);

        return '?';
    }

    /**
     * {@inheritdoc}
     */
    public function prepare($value)
    {
        if (is_array($value)) {
            return array_map([$this, 'prepare'], $value);
        }
        
        if ($value instanceof BuilderInterface) {
            $input = $value->getInput();

            if ($input) {
                array_push($this->input, ...$input);
            }

            $value = parentheses($value->build());
        }

        return $value;
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
     * {@inheritdoc}
     */
    public function alias(string $alias, ?string $table = null): BuilderInterface
    {
        $aliases = $this->getParameter('as') ?? [];

        $table = $table ?? $this->getCurrentTable();

        $aliases[$table] = $alias;

        return $this->setParameter('as', $aliases);
    }

    /**
     * An alias for `\p810\MySQL\Builder\AbstractBuilder::alias()`
     * 
     * @param string $alias
     * @param null|string $table
     * @return \p810\MySQL\Builder\BuilderInterface
     */
    public function as(string $alias, ?string $table = null): BuilderInterface
    {
        return $this->alias($alias, $table);
    }

    /**
     * Returns the alias for a given table, or null if it doesn't have one
     * 
     * @param string $table A table name
     * @return null|string
     */
    protected function getTableAlias(string $table): ?string
    {
        $aliases = $this->getParameter('as') ?? [];

        return $aliases[$table] ?? null;
    }

    /**
     * Returns the most recent table name used in the query, or null if one hasn't been set
     * 
     * @return null|string
     */
    protected function getCurrentTable(): ?string
    {
        return $this->table;
    }

    /**
     * Sets the query's latest table name
     * 
     * @param string $table A table name
     * @return void
     */
    protected function setCurrentTable(string $table): void
    {
        $this->table = $table;
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
