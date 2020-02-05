<?php

namespace Cajudev\Traits;

use Cajudev\Collection;

trait SortableTrait
{
    public function sort(): self
    {
        $content = $this->content;
        sort($content);
        return new static($content);
    }

    public function rsort(): self
    {
        $content = $this->content;
        rsort($content);
        return new static($content);
    }

    public function asort(): self
    {
        $content = $this->content;
        asort($content);
        return new static($content);
    }

    public function arsort(): self
    {
        $content = $this->content;
        arsort($content);
        return new static($content);
    }
    
    public function ksort(): self
    {
        $content = $this->content;
        ksort($content);
        return new static($content);
    }

    public function krsort(): self
    {
        $content = $this->content;
        krsort($content);
        return new static($content);
    }

    public function usort(callable $callback): self
    {
        $content = $this->content;
        usort($content, $callback);
        return new static($content);
    }
}
