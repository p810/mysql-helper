<?php

namespace p810\MySQL\Builder;

use InvalidArgumentException;

use function array_key_exists;

/**
 * Represents a distinct part of a query string.
 */
class Token
{
    /**
     * @var string
     */
    protected $value;

    /**
     * @var string
     */
    public $type;

    /**
     * @var array<string,string>
     */
    const TOKEN_FORMATS = [
        'select' => 'select %s',
        'from'   => 'from %s',
        'as'     => 'as %s',
        'where'  => 'where %s %s %s',
        'order'  => 'order by %s %s',
        'limit'  => 'limit %d',
        'insert' => 'insert into %s (%s)',
        'values' => 'values (%s)',
        'update' => 'update %s',
        'set'    => 'set %s',
        'delete' => 'delete from %s'
    ];

    /**
     * @var string
     */
    const SELECT   = 'select';
    const FROM     = 'from';
    const AS       = 'as';
    const WHERE    = 'where';
    const ORDER_BY = 'order';
    const LIMIT    = 'limit';
    const INSERT   = 'insert';
    const VALUES   = 'values';
    const UPDATE   = 'update';
    const SET      = 'set';
    const DELETE   = 'delete';

    /**
     * @throws \InvalidArgumentException if the $token does not have a format
     */
    function __construct(Builder $builder, string $token, ...$arguments)
    {
        if (! array_key_exists($token, self::TOKEN_FORMATS)) {
            throw new InvalidArgumentException('Attempting to create an instance of \p810\MySQL\Builder\Token with an undefined token type');
        }
        
        $this->type  = $token;
        $this->value = sprintf(self::TOKEN_FORMATS[$token], ...$arguments);
    }

    function __toString(): string
    {
        return $this->value;
    }
}