<?php

namespace p810\MySQL\Builder\Grammar;

use InvalidArgumentException;

use function substr;
use function sprintf;
use function in_array;
use function array_walk;
use function p810\MySQL\spaces;
use function p810\MySQL\parentheses;

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

    /**
     * @param string $left       Lefthand side of the expression
     * @param mixed  $right      Righthand side of the expression
     * @param string $comparison Middle of the expression
     * @param string $logical    A logical operator used to concatenate expressions
     * @return void
     */
    function __construct(string $left, $right, string $comparison = '=', string $logical = 'and')
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

    /**
     * Returns the expression as a string
     * 
     * @return string
     */
    function __toString(): string
    {
        return $this->compile();
    }

    /**
     * Returns the expression as a string
     * 
     * @param bool $withLogicalOperator Whether to include the logical operator at the end
     * @return string
     */
    public function compile(bool $withLogicalOperator = false): string
    {
        return sprintf('%s%s %s %s',
            $withLogicalOperator
                ? "$this->logicalOperator "
                : '',
            $this->left,
            $this->comparisonOperator,
            $this->getRighthandArgument()
        );
    }

    /**
     * Returns the righthand side of the expression
     * 
     * @return string
     */
    protected function getRighthandArgument(): string
    {
        if (is_array($this->right)) {
            return parentheses($this->right);
        }

        return $this->right;
    }

    /**
     * Compiles a list of expressions to a string
     * 
     * The array may contain strings or instances of \p810\MySQL\Builder\Grammar\Expression
     * 
     * @param array $expressions A list of expressions
     * @return string
     */
    public static function listToString(array $expressions): string
    {
        $clauses = [];
        
        array_walk($expressions, function ($value, $key) use (&$clauses) {
            $useLogicalOperator = $key === 0 ? false : true;
            
            if ($value instanceof Expression) {
                $value = $value->compile($useLogicalOperator);
            }
            
            $clauses[] = $value;
        });

        return spaces($clauses);
    }
}