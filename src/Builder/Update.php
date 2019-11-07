<?php

namespace p810\MySQL\Builder;

class Update extends AbstractBuilder
{
    use Grammar\Set;
    use Grammar\Where;

    /**
     * @inheritdoc
     */
    protected $components = [
        'update',
        'set',
        'where'
    ];

    /**
     * @var null|string
     */
    protected $table;

    /**
     * Specifies the table to update data in
     * 
     * @param string $table The table to update
     * @return self
     */
    public function update(string $table): self
    {
        $this->table = $table;

        return $this;
    }

    /**
     * An alias for `\p810\MySQL\Builder\Update::update()`
     * 
     * @param string $table The table to update
     * @return self
     */
    public function table(string $table): self
    {
        return $this->update($table);
    }

    /**
     * Compiles the update from clause
     * 
     * @return null|string
     */
    protected function compileUpdate(): ?string
    {
        if (! $this->table) {
            return null;
        }

        return "update $this->table";
    }

    /**
     * {@inheritdoc}
     */
    public function getCommand(): ?string
    {
        return 'update';
    }
}
