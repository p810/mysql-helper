<?php

namespace p810\MySQL\Builder;

use p810\MySQL\Exception\MissingArgumentException;

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
    protected $columns = '*';

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

    protected function compileSelect(): string
    {
        return "select $this->columns";
    }

    public function from(string $table): self
    {
        $this->table = $table;
        
        return $this;
    }

    protected function compileFrom(): string
    {
        if (! $this->table) {
            throw new MissingArgumentException('Attempted to run a select query without a table');
        }

        return "from $this->table";
    }

    public function limit(int $limit): self
    {
        $this->limit = $limit;

        return $this;
    }

    protected function compileLimit(): ?string
    {
        if (! $this->limit) {
            return null;
        }

        return "limit $this->limit";
    }
}