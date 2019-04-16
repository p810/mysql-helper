<?php

namespace p810\MySQL\Builder\Grammar;

use InvalidArgumentException;

use function substr;
use function sprintf;
use function in_array;

class Expression
{
    /**
     * @var string
     */
    public $left;

    /**
     * @var mixed
     */
    public $right;

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

    function __construct(string $left, $right, string $comparison, string $logical)
    {
        if (! in_array($comparison, self::COMPARISON_OPERATORS) ||
            ! in_array($logical, self::LOGICAL_OPERATORS))
        {
            throw new InvalidArgumentException('Clause was instantiated with an invalid operator');
        }

        $this->left = $left;
        $this->right = $right;
        $this->logicalOperator = $logical;
        $this->comparisonOperator = $comparison;
    }

    public function compile(bool $withLogicalOperator = false): string
    {
        return sprintf('%s %s %s%s',
            $this->left,
            $this->comparisonOperator,
            $this->getRighthandArgument(),
            $withLogicalOperator
                ? " $this->logicalOperator"
                : ''
        );
    }

    protected function getRighthandArgument(): string
    {
        if (is_array($this->right)) {
            return '(' . implode(', ', $this->right) . ')';
        }

        return $this->right;
    }

    /**
     * @var \p810\MySQL\Builder\Grammar\Expression[] $expressions
     */
    public static function listToString(array $expressions): string
    {
        $compiled = '';
        $lastIndex = count($expressions) - 1;
    
        foreach ($expressions as $expression) {
            $compiled .= $expression->compile();
    
            if (key($expressions) < $lastIndex) {
                $next = next($expressions);
                $compiled .= " $next->logicalOperator ";
            }
        }
    
        return $compiled;
    }
}