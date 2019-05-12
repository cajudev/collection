<?php

namespace Cajudev\Traits;

use Cajudev\Arrays;

trait IteratorTrait
{
    /**
     * Set the internal pointer of the array to the first element
     *
     * @return void
     */
    public function rewind()
    {
        reset($this->content);
    }

    /**
     * Return the current element in the array
     *
     * @return mixed
     */
    public function current()
    {
        return current($this->content);
    }

    /**
     * Fetch a key from the array
     *
     * @return void
     */
    public function key()
    {
        return key($this->content);
    }

    /**
     * Advance the internal pointer of the array
     *
     * @return void
     */
    public function next()
    {
        return next($this->content);
    }

    /**
     * Set the internal pointer of the array to the last element
     *
     * @return void
     */
    public function end()
    {
        return end($this->content);
    }

    /**
     * Verify whether a key of the array is valid
     *
     * @return void
     */
    public function valid()
    {
        $key = key($this->content);
        return $key !== null && $key !== false;
    }
}