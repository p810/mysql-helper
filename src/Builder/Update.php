<?php

namespace p810\MySQL\Builder;

use PDOStatement;
use p810\MySQL\Builder\Grammar\Expression;

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
    protected $values;

    /**
     * @var string
     */
    protected $table;

    /**
     * Specifies the table to update data in
     * 
     * @param string $table The table to update
     * @return self
     */
    public function update(string $table): self
    {
        $this->table = $table;

        return $this;
    }

    /**
     * An alias for \p810\MySQL\Builder\Update::update()
     * 
     * @param string $table The table to update
     * @return self
     */
    public function table(string $table): self
    {
        return $this->update($table);
    }

    /**
     * Compiles the update from clause
     * 
     * @return null|string
     */
    protected function compileUpdate(): ?string
    {
        if (! $this->table) {
            return null;
        }

        return "update $this->table";
    }

    /**
     * Specifies which columns to update and what their values should be
     * 
     * This method may take either two arguments, a column and a value, or an associative
     * array mapping columns to values
     * 
     * @param array $arguments The columns and values to update
     * @return self
     */
    public function set(...$arguments): self
    {
        if (is_array($arguments[0])) {
            foreach ($arguments[0] as $column => $value) {
                $this->set($column, $value);
            }
        } else {
            [$column, $value] = $arguments;
            
            $this->values[] = new Expression($column, $this->bind($value));
        }

        return $this;
    }

    /**
     * Compiles an assignment list for the "set (...)" clause
     * 
     * @return null|string
     */
    protected function compileSet(): ?string
    {
        if (! $this->values) {
            return null;
        }

        return 'set ' . commas($this->values);
    }
}