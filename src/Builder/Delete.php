<?php

namespace p810\MySQL\Builder;

class Delete extends AbstractBuilder
{
    use Grammar\Table;
    use Grammar\Where;
    use Grammar\Limit;
    use Grammar\Ignore;
    use Grammar\OrderBy;
    use Grammar\Priority;

    /**
     * @inheritdoc
     */
    protected $components = [
        'delete',
        'priority',
        'ignore',
        'from',
        'where',
        'order',
        'limit'
    ];

    /**
     * An alias for `\p810\MySQL\Builder\Delete::from()`
     * 
     * @param string $table The table to remove data from
     * @return \p810\MySQL\Builder\BuilderInterface
     */
    public function delete(string $table): BuilderInterface
    {
        return $this->from($table);
    }

    /**
     * Returns the `DELETE` keyword
     * 
     * @return string
     */
    protected function compileDelete(): string
    {
        return 'delete';
    }

    /**
     * {@inheritdoc}
     */
    public function getCommand(): ?string
    {
        return 'delete';
    }
}
