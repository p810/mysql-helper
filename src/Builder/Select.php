<?php

namespace p810\MySQL\Builder;

class Select extends Builder implements BuilderInterface
{
    use Grammar\Where;

    /**
     * @inheritdoc
     */
    public function build(): string
    {
        $query = 'SELECT ' . $this->getData('columns') . ' FROM ' . $this->getData('table');

        if ($this->clauses) {
            $this->setWhere();
            $query .= ' ' . $this->getData('where');
        }

        return $query;
    }
}