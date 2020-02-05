<?php

namespace Cajudev\Traits;

use Cajudev\Interfaces\Sortable;

trait SortableTrait
{
    public function sort(): Sortable
    {
        $content = $this->content;
        sort($content);
        return new static($content);
    }

    public function rsort(): Sortable
    {
        $content = $this->content;
        rsort($content);
        return new static($content);
    }

    public function asort(): Sortable
    {
        $content = $this->content;
        asort($content);
        return new static($content);
    }

    public function arsort(): Sortable
    {
        $content = $this->content;
        arsort($content);
        return new static($content);
    }
    
    public function ksort(): Sortable
    {
        $content = $this->content;
        ksort($content);
        return new static($content);
    }

    public function krsort(): Sortable
    {
        $content = $this->content;
        krsort($content);
        return new static($content);
    }

    public function usort(callable $callback): Sortable
    {
        $content = $this->content;
        usort($content, $callback);
        return new static($content);
    }
}
