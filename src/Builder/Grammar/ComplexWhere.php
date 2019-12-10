<?php

namespace p810\MySQL\Builder\Grammar;

use p810\MySQL\Builder\AbstractBuilder;

class ComplexWhere extends AbstractBuilder
{
    use Where;

    /**
     * {@inheritdoc}
     */
    final public function build(): string
    {
        $wheres = $this->getParameter('where');

        return Expression::listToString($wheres);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function getCommand(): ?string
    {
        return null;
    }
}
