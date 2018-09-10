<?php

namespace p810\MySQL\Builder;

class Insert extends Builder {
    use \p810\MySQL\Query\From;

    public function build(): string {
        return sprintf(
            'INSERT INTO %s (%s) VALUES (%s)',
            $this->getTable(), $this->getColumns(), $this->getValues()
        );
    }

    public function values(array $values): self {
        return $this->setValues($values);
    }

    public function setValues(array $values): self {
        foreach ($values as $index => $value) {
            $this->bind($value);
            $values[$index] = '?';
        }
        
        $this->fragments['values'] = $values;

        return $this;
    }

    public function getValues(): string {
        return implode(', ', $this->fragments['values']);
    }
}