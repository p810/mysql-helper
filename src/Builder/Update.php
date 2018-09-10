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

    public function set(...$arguments): self {
        switch (count($arguments)) {
            case 1:
                if (! is_array($arguments)) {
                    throw new \UnexpectedValueException('Update::set() requires either a list of column => value pairs, or two arguments');
                }

                $arguments = $arguments[0];

                foreach ($arguments as $column => $value) {
                    $this->bind($value);
                    $arguments[$column] = '?';
                }
            break;

            case 2:
                [$column, $value] = $arguments;

                $this->bind($value);

                $arguments = ["$column" => '?'];
            break;
        }

        if (isset($this->fragments['values'])) {
            $this->fragments['values'] += $arguments;
        } else {
            $this->fragments['values'] = $arguments;
        }

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