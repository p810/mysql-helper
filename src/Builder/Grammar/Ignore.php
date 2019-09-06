<?php

namespace p810\MySQL\Builder\Grammar;

trait Ignore
{
    /**
     * @var bool
     */
    protected $ignore = false;

    public function ignore(): self
    {
        $this->ignore = true;

        return $this;
    }

    protected function compileIgnore(): ?string
    {
        return $this->ignore ? 'ignore' : null;
    }
}
