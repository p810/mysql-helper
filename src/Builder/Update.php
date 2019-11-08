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
     * Specifies the table to update data in
     * 
     * @param string $table The table to update
     * @return \p810\MySQL\Builder\BuilderInterface
     */
    public function update(string $table): BuilderInterface
    {
        return $this->setParameter('table', $table);
    }

    /**
     * An alias for `\p810\MySQL\Builder\Update::update()`
     * 
     * @param string $table The table to update
     * @return \p810\MySQL\Builder\BuilderInterface
     */
    public function table(string $table): BuilderInterface
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
        $table = $this->getParameter('table');

        if (! $table) {
            return null;
        }

        return "update $table";
    }

    /**
     * {@inheritdoc}
     */
    public function getCommand(): ?string
    {
        return 'update';
    }
}
