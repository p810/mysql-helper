<?php

namespace p810\MySQL\Builder;

use p810\MySQL\Builder\Grammar\Expression;

class Replace extends Insert
{
    use Grammar\Set;

    /**
     * @inheritdoc
     */
    const COMMAND = 'replace';

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
        return self::COMMAND;
    }

    /**
     * Checks whether this query is using an assignment or value list and returns a string accordingly
     * 
     * @return null|string
     */
    public function compileValues(): ?string
    {
        if ($this->setValues && $this->setValues[0] instanceof Expression) {
            return $this->compileSet();
        }

        return parent::compileValues();
    }
}
