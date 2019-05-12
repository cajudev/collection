<?php

namespace Cajudev\Traits;

use Cajudev\Arrays;

trait SortableTrait
{
    /**
     * Sort the array
     *
     * @return self
     */
    public function sort(): self
    {
        sort($this->content);
        return $this;
    }

    /**
     * Sort the array in reverse order
     *
     * @return self
     */
    public function rsort(): self
    {
        rsort($this->content);
        return $this;
    }

    /**
     * Sort the array and maintain index association
     *
     * @return self
     */
    public function asort(): self
    {
        asort($this->content);
        return $this;
    }

    /**
     * Sort the array in reverse order and maintain index association
     *
     * @return self
     */
    public function arsort(): self
    {
        arsort($this->content);
        return $this;
    }
    
    /**
     * Sort the array by key
     *
     * @return self
     */
    public function ksort(): self
    {
        ksort($this->content);
        return $this;
    }

    /**
     * Sort the array by key in reverse order
     *
     * @return self
     */
    public function krsort(): self
    {
        krsort($this->content);
        return $this;
    }
}