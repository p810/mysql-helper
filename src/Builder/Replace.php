<?php

namespace p810\MySQL\Builder;

class Replace extends Insert
{
    use Grammar\Set;

    /**
     * @inheritdoc
     */
    protected $components = [
        'replace',
        'priority',
        'into',
        'columns',
        'values'
    ];

    /**
     * Returns the query command
     * 
     * @return string
     */
    protected function compileReplace(): string
    {
        return 'replace';
    }

    /**
     * Checks whether this query is using an assignment or value list and returns a string accordingly
     * 
     * @return null|string
     */
    public function compileValues(): ?string
    {
        if ($this->getParameter('set')) {
            return $this->compileSet();
        }

        return parent::compileValues();
    }

    /**
     * {@inheritdoc}
     */
    public function getCommand(): ?string
    {
        return 'replace';
    }
}
