<?php

namespace p810\MySQL\Builder;

use UnexpectedValueException;

use function uksort;
use function implode;
use function array_map;
use function array_search;

abstract class Builder
{
    /**
     * @var string[]
     */
    protected $order;

    /**
     * @var \p810\MySQL\Token[]
     */
    protected $tokens;

    /**
     * @var string[]
     */
    public $userInput = [];

    public function build(): string
    {
        usort($this->tokens, [$this, 'compareTokens']);

        return implode(' ', $this->tokens);
    }

    public function bind($value): string
    {
        if (is_array($value)) {
            return array_map(function ($value) {
                return $this->bind($value);
            }, $value);
        }

        $this->userInput[] = $value;

        return '?';
    }

    public function append(string $token, ...$arguments): self
    {
        $this->tokens[$token] = new Token($token, ...$arguments);

        return $this;
    }

    protected function compareTokens(Token $current, Token $previous): int
    {
        $current  = array_search($current->type, $this->order);
        $previous = array_search($previous->type, $this->order);

        return $current <=> $previous;
    }
}