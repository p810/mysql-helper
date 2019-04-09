<?php

namespace p810\MySQL\Builder;

class Select extends Builder implements BuilderInterface
{
    use Grammar\Where;
    use Grammar\OrderBy;

    /**
     * @inheritdoc
     */
    public function build(): string
    {
        $query = 'SELECT ' . $this->getData('columns') . ' FROM ' . $this->getData('table');

        if ($this->hasWhereClauses()) {
            $query .= ' ' . $this->getWhere();
        }

        if ($this->hasOrderByClauses()) {
            $query .= ' ' . $this->getOrderBy();
        }

        return $query;
    }
}