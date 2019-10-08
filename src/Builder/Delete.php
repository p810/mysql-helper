<?php

namespace p810\MySQL\Builder;

class Delete extends Builder
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
    const COMMAND = 'delete';

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
     * An alias for \p810\MySQL\Builder\Delete::from()
     * 
     * @param string $table The table to remove data from
     * @return self
     */
    public function delete(string $table): self
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
        return self::COMMAND;
    }
}
