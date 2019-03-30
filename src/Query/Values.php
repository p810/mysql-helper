<?php

namespace p810\MySQL\Query;

use InvalidArgumentException;

use function key;
use function end;

trait Values
{
    /**
     * @throws \InvalidArgumentException 
     */
    public function setValues(...$arguments): self
    {
        switch (count($arguments)) {
            case 1:
                if (! is_array($arguments)) {
                    throw new InvalidArgumentException('Update::set() requires either a list of column => value pairs, or two arguments');
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

    public function set(...$arguments): self
    {
        return $this->setValues(...$arguments);
    }

    public function values(...$arguments): self
    {
        return $this->setValues(...$arguments);
    }

    public function getValues(): string
    {        
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