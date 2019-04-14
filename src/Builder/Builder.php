<?php

namespace p810\MySQL\Builder;

use function ucfirst;
use function implode;
use function sprintf;
use function is_array;
use function array_map;
use function array_walk;
use function array_reduce;

abstract class Builder
{
    /**
     * @var string[]
     */
    protected $components;

    /**
     * @param array|string|int $value
     */
    public function bind($value): string
    {
        if (is_array($value)) {
            return array_map(function ($value) {
                return $this->bind($value);
            }, $value);
        }

        $this->input[] = $value;

        return '?';
    }

    public function build(): string
    {
        $parts = array_reduce($this->components, function ($value, $component) {
            $method = 'compile' . ucfirst($component);
            $result = $this->$method();

            if ($result) {
                $value[] = $result;
            }

            return $value;
        }, []);

        return implode(' ', $parts);
    }

    protected function parentheses(array $list): string
    {
        return sprintf('(%s)', $this->toCommaList($list));
    }

    protected function toCommaList(array $list): string
    {
        return implode(', ', $list);
    }

    protected function prefixedColumnList(array $columns): array
    {
        array_walk($columns, function (&$column, $table) {
            $column = "$table.$column";
        });

        return $columns;
    }
}