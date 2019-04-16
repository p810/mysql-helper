<?php

namespace p810\MySQL\Builder;

use function is_array;
use function p810\MySQL\commas;

class Update extends Builder
{
    use Grammar\Where;

    /**
     * @inheritdoc
     */
    protected $components = [
        'update',
        'set',
        'where'
    ];

    /**
     * @var array
     */
    public $values;

    /**
     * @var string
     */
    public $table;

    public function update(string $table): self
    {
        $this->table = $table;

        return $this;
    }

    public function table(string $table): self
    {
        return $this->update($table);
    }

    protected function compileUpdate(): string
    {
        return "update $this->table";
    }

    public function set(...$arguments): self
    {
        if (is_array($arguments[0])) {
            foreach ($arguments[0] as $column => $value) {
                $this->set($column, $value);
            }
        } else {
            [$column, $value] = $arguments;
            
            $this->values[$column] = $this->bind($value);
        }

        return $this;
    }

    protected function compileSet(): string
    {
        $strings = [];

        foreach ($this->values as $column => $value) {
            $strings[] = $column . ' = ' . $value;
        }

        return 'set ' . commas($strings);
    }
}