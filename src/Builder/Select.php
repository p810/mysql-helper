<?php

namespace p810\MySQL\Builder;

use PDO;
use PDOStatement;

use function is_array;
use function is_string;
use function p810\MySQL\commas;

class Select extends Builder
{
    use Grammar\Join;
    use Grammar\Where;
    use Grammar\OrderBy;

    /**
     * @inheritdoc
     */
    const COMMAND = 'select';

    /**
     * @inheritdoc
     */
    protected $components = [
        'select',
        'from',
        'join',
        'where',
        'order',
        'limit'
    ];

    /**
     * @var int|null
     */
    protected $limit;

    /**
     * @var string
     */
    protected $table;

    /**
     * @var string
     */
    protected $columns;

    /**
     * Specifies which columns to return in the result set
     * 
     * @param array|string $columns Either a string or array; if an array, it can
     *                              be numerically indexed for a list of columns,
     *                              or associative to specify table prefixes
     * @return self
     */
    public function select($columns = '*'): self
    {
        if (is_array($columns)) {
            $isAssoc = is_string(key($columns));

            if ($isAssoc) {
                $columns = $this->prefixColumns($columns);
            }

            $columns = commas($columns);
        }

        $this->columns = $columns;

        return $this;
    }

    /**
     * An alias for \p810\MySQL\Builder\Select::select()
     * 
     * @param array|string $columns Either a string or array; if an array, it can
     *                              be numerically indexed for a list of columns,
     *                              or associative to specify table prefixes
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
     * Specifies which table to pull data from
     * 
     * @param string $table The table to get data from
     * @return self
     */
    public function from(string $table): self
    {
        $this->table = $table;
        
        return $this;
    }

    /**
     * Compiles the from clause
     * 
     * @return null|string
     */
    protected function compileFrom(): ?string
    {
        if (! $this->table) {
            return null;
        }

        return "from $this->table";
    }

    /**
     * Specifies a limit of rows to return in the result set
     * 
     * @param int $limit The maximum number of rows to return
     * @return self
     */
    public function limit(int $limit): self
    {
        $this->limit = $limit;

        return $this;
    }

    /**
     * Compiles the limit clause
     * 
     * @return null|string
     */
    protected function compileLimit(): ?string
    {
        if (! $this->limit) {
            return null;
        }

        return "limit $this->limit";
    }

    /**
     * Prefixes column names with their corresponding tables, e.g. for a
     * query that joins data from foreign tables
     * 
     * @param array $columns An associative array of tables => columns
     * @return string[]
     */
    protected function prefixColumns(array $columns): array
    {
        array_walk($columns, function (&$column, $table) {
            $column = "$table.$column";
        });

        return $columns;
    }
}
