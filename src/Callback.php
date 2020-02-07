<?php

namespace Cajudev;

use Cajudev\Interfaces\CollectionInterface;

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

    public function exec($self, $key, $value)
    {
        $callback = $this->callback;
        switch ($this->args()->length) {
            case 1: return $callback($value);
            case 2: return $callback($key, $value);
            case 3: return $callback($self, $key, $value);
            default: throw new \InvalidArgumentException('Arguments number incorrect');
        }
    }

    public function args(): CollectionInterface
    {
        return new Collection((is_array($this->callback)
            ? new \ReflectionMethod($this->callback[0], $this->callback[1])
            : new \ReflectionFunction($this->callback))->getParameters());
    }
}
