<?php

namespace p810\MySQL\Builder;

use function is_array;
use function p810\MySQL\commas;

class Select extends AbstractBuilder
{
    use Grammar\Join;
    use Grammar\Table;
    use Grammar\Where;
    use Grammar\Limit;
    use Grammar\OrderBy;
    use Grammar\Priority;

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
    protected $columns = '*';

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
     * An alias for `\p810\MySQL\Builder\Select::select()`
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

    /**
     * {@inheritdoc}
     */
    public function getCommand(): ?string
    {
        return 'select';
    }
}
