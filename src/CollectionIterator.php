<?php

namespace Cajudev;

use Cajudev\Collection;

class CollectionIterator implements \Iterator
{
    private $content;

    public function __construct(array $content)
    {
        $this->content = $content;
    }

    public function rewind()
    {
        reset($this->content);
    }

    public function current()
    {
        return current($this->content);
    }

    public function key()
    {
        return key($this->content);
    }

    public function next()
    {
        next($this->content);
    }

    public function previous()
    {
        prev($this->content);
    }

    public function valid()
    {
        return !is_null(key($this->content));
    }
}
