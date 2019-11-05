<?php

namespace p810\MySQL\Builder;

interface BuilderInterface
{
    /**
     * @param string|array $value
     * @return string|array
     */
    public function bind($value);

    /**
     *
     */
    public function build(): string;
}
