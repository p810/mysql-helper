<?php

namespace p810\MySQL\Builder;

class Update extends Builder {
    use \p810\MySQL\Query\From;
    use \p810\MySQL\Query\Where;

    public function build(): string {
        $query = sprintf('UPDATE %s SET %s', $this->getTable(), $this->getValues());

        $where = $this->getWhere();
        if ($where !== null) {
            $query .= ' ' . $where;
        }

        return $query;
    }

    public function set(array $values): self {
        foreach ($values as $column => $value) {
            $this->bind($value);

            $values[$column] = '?';
        }

        $this->fragments['values'] = $values;

        return $this;
    }

    public function getValues(): string {
        $string = '';
        foreach ($this->fragments['values'] as $column => $questionMark) {
            $string .= "$column = ?";

            end($this->fragments['values']);
            if (key($this->fragments['values']) !== $column) {
                $string .= ', ';
            }
        }

        return $string;
    }
}