<?php

namespace p810\MySQL\Builder\Grammar;

use InvalidArgumentException;

use function substr;
use function sprintf;
use function in_array;

class Clause
{
    /**
     * @var string
     */
    public $column;

    /**
     * @var mixed
     */
    public $value;

    /**
     * @var string
     */
    public $logicalOperator;

    /**
     * @var string
     */
    public $comparisonOperator;

    /**
     * @var string[]
     */
    const COMPARISON_OPERATORS = ['=', '!=', '<', '>', '<=', '>=', 'in', 'not in', 'like'];

    /**
     * @var string[]
     */
    const LOGICAL_OPERATORS = ['and', 'or', 'between'];

    function __construct(string $column, $value, string $comparison, string $logical)
    {
        if (! in_array($comparison, self::COMPARISON_OPERATORS) ||
            ! in_array($logical, self::LOGICAL_OPERATORS))
        {
            throw new InvalidArgumentException('Clause was instantiated with an invalid operator');
        }

        $this->value = $value;
        $this->column = $column;
        $this->logicalOperator = $logical;
        $this->comparisonOperator = $comparison;
    }

    public function compile(bool $withLogicalOperator = false): string
    {
        return sprintf('%s %s %s%s',
            $this->column,
            $this->comparisonOperator,
            $this->getValue(),
            $withLogicalOperator
                ? " $this->logicalOperator"
                : null
        );
    }

    protected function getValue(): string
    {
        if (is_array($this->value)) {
            return '(' . implode(', ', $this->value) . ')';
        }

        return $this->value;
    }
}