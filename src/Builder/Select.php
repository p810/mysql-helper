<?php

namespace p810\MySQL\Builder;

class Select extends Builder
{
    use Grammar\Join;
    use Grammar\Where;

    /**
     * @inheritdoc
     */
    protected $components = [
        'select',
        'from',
        'join',
        'where'
    ];

    public function select($columns = '*'): self
    {
        if (is_array($columns)) {
            $isAssoc = is_string(key($columns));

            if ($isAssoc) {
                $columns = $this->prefixedColumnList($columns);
            }

            $columns = $this->toCommaList($columns);
        }

        $this->columns = $columns;

        return $this;
    }

    public function from(string $table): self
    {
        $this->table = $table;
        
        return $this;
    }
}