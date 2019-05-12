<?php

namespace Cajudev\Traits;

trait CountableTrait
{
    private $length;

    /**
     * Count all elements of the array
     *
     * @return int
     */
    public function count(int $mode = COUNT_NORMAL): int
    {
        $this->length = count($this->content, $mode);
        return $this->length;
    }
}