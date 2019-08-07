<?php

namespace Cajudev;

use Cajudev\Collection;

class CollectionIterator implements \Iterator
{
    private $content;

    /**
     * __construct
     *
     * @param  mixed $content
     *
     * @return void
     */
    public function __construct(array $content) {
        $this->content = $content;
    }

    /**
     * Set the internal pointer of an array to its first element
     *
     * @return void
     */
    public function rewind() {
        reset($this->content);
    }

    /**
     * Return the current element in an array
     *
     * @return mixed
     */
    public function current() {
        return current($this->content);
    }

    /**
     * Fetch a key from an array
     *
     * @return mixed
     */
    public function key() {
        return key($this->content);
    }

    /**
     * Advance the internal array pointer of an array
     *
     * @return void
     */
    public function next() {
        next($this->content);
    }

    /**
     * Return the internal array pointer of an array
     *
     * @return void
     */
    public function previous() {
        prev($this->content);
    }

    /**
     * Check if the current position of the array is valid
     *
     * @return bool
     */
    public function valid() {
        return !is_null(key($this->content));
    }
}
