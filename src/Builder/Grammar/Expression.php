<?php

namespace p810\MySQL\Builder\Grammar;

use function sprintf;
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
     * @param string $left Left hand side of the expression
     * @param mixed  $right Right hand side of the expression
     * @param string $comparison Middle of the expression
     * @param string $logical A logical operator used to concatenate expressions
     * @return void
     */
    function __construct(string $left, $right, string $comparison = '=', string $logical = 'and')
    {
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
     * @param bool $withLogicalOperator Whether this expression is being appended to one before it
     * @return string
     */
    public function compile(bool $withLogicalOperator = false): string
    {
        $logicalOperator = $withLogicalOperator ? "$this->logicalOperator " : '';

        return sprintf('%s%s %s %s',
            $logicalOperator,
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

        foreach ($expressions as $key => $value) {
            $useLogicalOperator = $key >= 1;

            if ($value instanceof self) {
                $value = $value->compile($useLogicalOperator);
            }

            $clauses[] = $value;
        }

        return spaces($clauses);
    }
}
