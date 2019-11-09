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
        'columns',
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
        return $this->setParameter('columns', $columns);
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
     * Compiles the column(s) string for the query
     * 
     * @return null|string
     */
    protected function compileColumns(): ?string
    {
        $columns = $this->getParameter('columns');

        if (is_array($columns)) {
            $columns = commas($columns);
        }

        return $columns;
    }

    /**
     * {@inheritdoc}
     */
    public function getCommand(): ?string
    {
        return 'select';
    }
}
