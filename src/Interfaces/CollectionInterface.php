<?php

namespace Cajudev\Interfaces;

/**
 * Interface CollectionInterface
 * @package Cajudev\Interfaces
 */
interface CollectionInterface
{
    /**
     * Get a element of the collection
     *
     * @param mixed ...$keys
     * @return mixed
     */
    public function get(...$keys);

    /**
     * Associate a element into collection
     *
     * @param string $key
     * @param $value
     * @return mixed
     */
    public function set(string $key, $value);

    /**
     * Insert elements on the beginning of the collection
     *
     * @param mixed ...$values
     * @return static
     */
    public function unshift(...$values): static;

    /**
     * Insert elements on final of the collection
     *
     * @param mixed ...$values
     * @return static
     */
    public function push(...$values): static;

    /**
     * Count the number of elements of the collection
     *
     * @param int $mode
     * @return int
     */
    public function count(int $mode = COUNT_NORMAL): int;

    /**
     * Perform a incremental loop
     *
     * @param int $i
     * @param int $add
     * @param callable $callback
     * @param bool $array_as_collection
     * @return mixed
     */
    public function for(int $i, int $add, callable $callback, bool $array_as_collection = false);

    /**
     * Perform a foreach loop
     *
     * @param callable $callback
     * @param bool $array_as_collection
     * @return mixed
     */
    public function each(callable $callback, bool $array_as_collection = false);

    /**
     * Apply a callable recursively in all elements of the collection
     *
     * @param callable $callback
     * @param int $type
     * @param bool $array_as_collection
     * @return mixed
     */
    public function walk(callable $callback, $type = \RecursiveIteratorIterator::LEAVES_ONLY, bool $array_as_collection = false);

    /**
     * Sum the elements of the collection
     *
     * @return int
     */
    public function sum(): int;

    /**
     * Check if a element exists in the colletion
     *
     * @param $value
     * @return bool
     */
    public function contains($value): bool;

    /**
     * Apply a callback into elements of the collection
     *
     * @param callable $callback
     * @param bool $array_as_collection
     * @return static
     */
    public function map(callable $callback, bool $array_as_collection = false): static;

    /**
     * Filter the elements of the collection
     *
     * @param callable $callback
     * @param bool $array_as_collection
     * @return static
     */
    public function filter(callable $callback, bool $array_as_collection = false): static;

    /**
     * Reduce the collection to single value
     *
     * @param callable $callback
     * @param bool $array_as_collection
     * @return mixed
     */
    public function reduce(callable $callback, bool $array_as_collection = false);

    /**
     * Find a element of collection using a callback
     *
     * @param callable $callback
     * @param bool $array_as_collection
     * @return mixed
     */
    public function find(callable $callback, bool $array_as_collection = false);

    /**
     * Check if the key exists and its value is not null
     *
     * @param $key
     * @return bool
     */
    public function isset($key): bool;

    /**
     * Check if the key not exists or its value is null
     *
     * @param $key
     * @return bool
     */
    public function noset($key): bool;

    /**
     * Check is a value of given key is empty
     *
     * @param $key
     * @return bool
     */
    public function empty($key): bool;

    /**
     * Check if a value of given key is not empty
     *
     * @param $key
     * @return bool
     */
    public function filled($key): bool;

    /**
     * Remove a element of the collection
     *
     * @param $key
     * @return mixed
     */
    public function unset($key);

    /**
     * Remove and return the first element of the collection
     *
     * @return mixed
     */
    public function shift();

    /**
     * Remove and return the last element of the collection
     *
     * @return mixed
     */
    public function pop();

    /**
     * Transform a collection into a string
     *
     * @param string $glue
     * @return mixed
     */
    public function join(string $glue);

    /**
     * Get a random element of the collection
     *
     * @param int $num
     * @return mixed
     */
    public function random(int $num = 1);

    /**
     * Shuffle the elements of the collection
     *
     * @return static
     */
    public function shuffle(): static;

    /**
     * Invert the key and values of the collection
     *
     * @return static
     */
    public function flip(): static;

    /**
     * Return a new collection containing only the keys
     *
     * @return static
     */
    public function keys(): static;

    /**
     * Return a new collection reseting the keys
     *
     * @return static
     */
    public function values(): static;

    /**
     * Return a new collection containing only elements of a given column
     *
     * @param $key
     * @param null $index
     * @return mixed
     */
    public function column($key, $index = null);

    /**
     * Split the collection into parts
     *
     * @param int $size
     * @param bool $preserve_keys
     * @return static
     */
    public function chunk(int $size, bool $preserve_keys = false): static;

    /**
     * Remove duplicated elements
     *
     * @param int $flags
     * @return static
     */
    public function unique(int $flags = SORT_STRING): static;

    /**
     * Return the first non null value of the collection
     *
     * @return mixed
     */
    public function coalesce();

    /**
     * Merge the elements of the collection
     *
     * @return static
     */
    public function merge(): static;

    /**
     * Return the inverse of the collection
     *
     * @param bool $preserve_keys
     * @return static
     */
    public function reverse(bool $preserve_keys = false): static;

    /**
     * Find a value of the collection and return its key
     *
     * @param $value
     * @param bool|null $strict
     * @return mixed
     */
    public function search($value, bool $strict = null);

    /**
     * Return the first element of the collection
     *
     * @return mixed
     */
    public function first();

    /**
     * Return the last element of the collection
     *
     * @return mixed
     */
    public function last();

    /**
     * Transform all the keys of the collection to lower case
     *
     * @return static
     */
    public function lower(): static;

    /**
     * Transform all the keys of the collection to upper case
     *
     * @return static
     */
    public function upper(): static;

    /**
     * Combine two arrays/collection into one
     *
     * @param $keys
     * @param $values
     * @return static
     */
    public static function combine($keys, $values): static;

    /**
     * Generate a new collection by a given range
     *
     * @param $start
     * @param $end
     * @param int $step
     * @return static
     */
    public static function range($start, $end, $step = 1): static;
}
