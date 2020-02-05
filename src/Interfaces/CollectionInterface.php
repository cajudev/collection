<?php

namespace Cajudev\Interfaces;

interface CollectionInterface
{
    public function count(int $mode = COUNT_NORMAL): int;

    public function unshift(...$values): CollectionInterface;
    public function push(...$values): CollectionInterface;

    public function for(int $i, int $add, callable $callback, bool $arrayAsCollection = false);
    public function each(callable $callback, bool $arrayAsCollection = false);
    public function walk(callable $callback, $type = \RecursiveIteratorIterator::LEAVES_ONLY, bool $arrayAsCollection = false);
   
    public function sum(): int;
    public function contains($value): bool;

    public function map(callable $callback, bool $arrayAsCollection = false): CollectionInterface;
    public function filter(callable $callback, bool $arrayAsCollection = false): CollectionInterface;
    public function reduce(callable $callback, bool $arrayAsCollection = false);
    public function find(callable $callback, bool $arrayAsCollection = false);

    public function isset($key): bool;
    public function noset($key): bool;
    public function empty($key): bool;
    public function filled($key): bool;
    public function unset($key);

    public function shift();
    public function pop();

    public function join(string $glue);
    public function random(int $num = 1);
    public function shuffle(): CollectionInterface;
    public function flip(): CollectionInterface;

    public function keys(): CollectionInterface;
    public function values(): CollectionInterface;
    public function column($key, $index = null);

    public function chunk(int $size, bool $preserve_keys = false): CollectionInterface;
    public function unique(int $flags = SORT_STRING): CollectionInterface;

    public function coalesce();

    public function merge(): CollectionInterface;
    public function reverse($preserve_keys = null): CollectionInterface;
    public function search($value, bool $strict = null);

    public function first();
    public function last();

    public function lower(): CollectionInterface;
    public function upper(): CollectionInterface;

    public function get(...$keys);
    public function set(string $key, $value);

    public static function combine($keys, $values): CollectionInterface;
    public static function range($start, $end, $step = 1): CollectionInterface;
}
