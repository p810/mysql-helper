<?php

namespace p810\MySQL\Builder;

class Update extends Builder {
    use \p810\MySQL\Query\From;
    use \p810\MySQL\Query\Where;

    public function build(): string {
        $query = sprintf('UPDATE %s SET %s', $this->getTableName(), $this->getValues());

        $where = $this->getWhere();
        if ($where !== null) {
            $query .= ' ' . $where;
        }

        return $query;
    }

    public function set(array $values): self {
        $set = '';
        foreach ($values as $column => $value) {
            $set .= "$column = $value";
        }

        $this->fragments['values'] = $set;

        return $this;
    }

    protected function getValues(): string {
        return $this->fragments['values'];
    }
}