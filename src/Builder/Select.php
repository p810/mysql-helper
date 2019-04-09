<?php

namespace p810\MySQL\Builder;

use function sprintf;

class Select extends Builder implements BuilderInterface
{
    /**
     * @inheritdoc
     */
    public function build(): string
    {
        return sprintf('SELECT %s FROM %s',
            $this->getData('columns'),
            $this->getData('table')
        );
    }
}