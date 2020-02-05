<?php

namespace Cajudev;

class Callback
{
    private $callback;

    public function __construct(callable $callback)
    {
        $this->callback = $callback;
    }

    public function __invoke(...$args)
    {
        return $this->exec(...$args);
    }

    public function exec($key, $value)
    {
        $callback = $this->callback;
        return $this->args() > 1 ? $callback($key, $value) : $callback($value);
    }

    public function args(): int
    {
        return (is_array($this->callback)
            ? new \ReflectionMethod($this->callback[0], $this->callback[1])
            : new \ReflectionFunction($this->callback))->getNumberOfParameters();
    }
}
