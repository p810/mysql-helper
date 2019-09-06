<?php

namespace p810\MySQL\Builder;

use PDOStatement;

class Delete extends Builder
{
    use Grammar\Where;
    use Grammar\Ignore;
    use Grammar\Priority;

    /**
     * @inheritdoc
     */
    const COMMAND = 'delete';

    /**
     * @inheritdoc
     */
    protected $components = [
        'priority',
        'ignore',
        'from',
        'where'
    ];

    /**
     * @var string
     */
    protected $table;

    /**
     * Specifies the table to remove data from
     * 
     * @param string $table The table to remove data from
     * @return self
     */
    public function from(string $table): self
    {
        $this->table = $table;

        return $this;
    }

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
     * Compiles the delete from clause
     * 
     * @return null|string
     */
    protected function compileFrom(): ?string
    {
        if (! $this->table) {
            return null;
        }

        return "delete from $this->table";
    }
}
