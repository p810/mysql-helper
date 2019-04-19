<?php

namespace p810\MySQL\Builder;

use PDOStatement;

class Delete extends Builder
{
    use Grammar\Where;

    /**
     * @inheritdoc
     */
    public $type = 'delete';

    /**
     * @inheritdoc
     */
    protected $components = [
        'from',
        'where'
    ];

    /**
     * @var string
     */
    protected $table;

    /**
     * @inheritdoc
     */
    public function process(PDOStatement $statement)
    {
        return $statement->rowCount();
    }

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
     * @return string
     */
    protected function compileFrom(): string
    {
        return "delete from $this->table";
    }
}