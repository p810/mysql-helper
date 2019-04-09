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
            $this->setWhere();
            $query .= ' ' . $this->getData('where');
        }

        if ($this->hasOrderByClauses()) {
            $this->setOrderBy();
            $query .= ' ' . $this->getData('orderBy');
        }

        return $query;
    }
}