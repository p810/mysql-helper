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
     * Specifies which columns to return in the result set
     * 
     * @param array|string $columns
     * @return \p810\MySQL\Builder\BuilderInterface
     */
    public function select($columns = '*'): BuilderInterface
    {
        if (is_array($columns)) {
            $columns = commas($columns);
        }

        $this->setParameter('columns', $columns);

        return $this;
    }

    /**
     * An alias for `\p810\MySQL\Builder\Select::select()`
     * 
     * @param array|string $columns
     * @return \p810\MySQL\Builder\BuilderInterface
     */
    public function columns($columns = '*'): BuilderInterface
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
        $columns = $this->getParameter('columns');

        if (! $columns) {
            return null;
        }

        return "select $columns";
    }

    /**
     * {@inheritdoc}
     */
    public function getCommand(): ?string
    {
        return 'select';
    }
}
