<?php

namespace p810\MySQL\Builder;

class Delete extends Builder
{
    use Grammar\Where;

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
    public $table;

    public function from(string $table): self
    {
        $this->table = $table;

        return $this;
    }

    public function delete(string $table): self
    {
        return $this->from($table);
    }

    protected function compileFrom(): string
    {
        return "delete from $this->table";
    }
}