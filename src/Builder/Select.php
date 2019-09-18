<?php

namespace p810\MySQL\Builder;

use PDO;
use PDOStatement;

use function is_array;
use function is_string;
use function p810\MySQL\commas;

class Select extends Builder
{
    use Grammar\From;
    use Grammar\Join;
    use Grammar\Where;
    use Grammar\Limit;
    use Grammar\OrderBy;
    use Grammar\Priority;

    /**
     * @inheritdoc
     */
    const COMMAND = 'select';

    /**
     * @inheritdoc
     */
    protected $components = [
        'select',
        'priority',
        'from',
        'join',
        'where',
        'order',
        'limit'
    ];

    /**
     * @var string
     */
    protected $columns;

    /**
     * Specifies which columns to return in the result set
     * 
     * @param array|string $columns
     * @return self
     */
    public function select($columns = '*'): self
    {
        if (is_array($columns)) {
            $columns = commas($columns);
        }

        $this->columns = $columns;

        return $this;
    }

    /**
     * An alias for \p810\MySQL\Builder\Select::select()
     * 
     * @param array|string $columns
     * @return self
     */
    public function columns($columns = '*'): self
    {
        return $this->select($columns);
    }

    /**
     * Compiles the select clause
     * 
     * @return null|string
     */
    protected function compileSelect(): ?string
    {
        if (! $this->columns) {
            return null;
        }

        return "select $this->columns";
    }
}
