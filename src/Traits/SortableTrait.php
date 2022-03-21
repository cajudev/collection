<?php

namespace Cajudev\Traits;

use Cajudev\Interfaces\Sortable;

trait SortableTrait
{
    public function sort(): static
    {
        $content = $this->content;
        sort($content);
        return new static($content);
    }

    public function rsort(): static
    {
        $content = $this->content;
        rsort($content);
        return new static($content);
    }

    public function asort(): static
    {
        $content = $this->content;
        asort($content);
        return new static($content);
    }

    public function arsort(): static
    {
        $content = $this->content;
        arsort($content);
        return new static($content);
    }

    public function ksort(): static
    {
        $content = $this->content;
        ksort($content);
        return new static($content);
    }

    public function krsort(): static
    {
        $content = $this->content;
        krsort($content);
        return new static($content);
    }

    public function usort(callable $callback): static
    {
        $content = $this->content;
        usort($content, $callback);
        return new static($content);
    }
}
