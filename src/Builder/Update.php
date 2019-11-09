<?php

namespace p810\MySQL\Builder;

class Update extends AbstractBuilder
{
    use Grammar\Set;
    use Grammar\Where;
    use Grammar\Table;

    /**
     * @inheritdoc
     */
    protected $components = [
        'table',
        'set',
        'where'
    ];

    /**
     * Specifies the table to update data in
     * 
     * @param string $table The table to update
     * @return \p810\MySQL\Builder\BuilderInterface
     */
    public function update(string $table): BuilderInterface
    {
        return $this->table($table);
    }

    /**
     * {@inheritdoc}
     */
    public function getCommand(): ?string
    {
        return 'update';
    }
}
