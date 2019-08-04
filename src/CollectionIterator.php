<?php

namespace Cajudev;

use Cajudev\Collection;

class CollectionIterator implements \Iterator
{
    private $class;
    private $content;

    /**
     * __construct
     *
     * @param  mixed $content
     *
     * @return void
     */
    public function __construct(Collection $collection) {
        $this->reflection = new \ReflectionClass($collection);
        $this->content    = $collection->get();
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
        return $this->return(current($this->content));
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

    /**
     * Transform the return into z Collection, if it is an array
     *
     * @return mixed
     */
    private function return($content) {
        $class = $this->reflection->getName();
        return is_array($content) ? new $class($content) : $content;
    }
}
