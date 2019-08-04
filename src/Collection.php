<?php

namespace Cajudev;

use Cajudev\ObjectParser;
use Cajudev\CollectionIterator;

use Cajudev\Interfaces\Set;
use Cajudev\Interfaces\Mixed;
use Cajudev\Interfaces\Sortable;

class Collection implements \ArrayAccess, \IteratorAggregate, \Countable, Sortable, Set
{
    use \Cajudev\Traits\SetTrait;
    use \Cajudev\Traits\SortableTrait;
    use \Cajudev\Traits\ArrayAccessTrait;

    const KEY_NOTATION      = '/^[^.:]+$/';
    const DOT_NOTATION      = '/(?<=\.|^)(?<key>[^.:]+)(?=\.|$)/';
    const INTERVAL_NOTATION = '/^(?<start>\d+):(?<end>\d+)$/';

    protected $content;
    protected $length;

    /**
     * @param  mixed $content
     *
     * @return void
     */
    public function __construct($content = [])
    {
        $this->content = is_array($content) ? $content : (new ObjectParser($content))->parse();
        $this->count();
    }

    /**
     * Set the content of the array by reference
     *
     * @param  array $array
     *
     * @return self
     */
    public function setByReference(array &$array = null): self
    {
        $array = $array ?? [];
        $this->content =& $array;
        $this->count();
        return $this;
    }

    /**
     * Count all elements of the collection
     * 
     * @param int $mode
     *
     * @return int
     */
    public function count(int $mode = COUNT_NORMAL): int
    {
        if ($mode === COUNT_RECURSIVE) {
            return count($this->content, $mode);
        }

        $this->length = count($this->content);
        return $this->length;
    }

    /**
     * Return the object iterator
     *
     * @return CollectionIterator
     */
    public function getIterator()
    {
        return new CollectionIterator($this);
    }

    /**
     * Insert the values on the beginning of the collection
     *
     * @param  mixed $values
     *
     * @return self
     */
    public function unshift(...$values): self
    {
        array_unshift($this->content, ...static::checkAll($values));
        $this->increment(count($values));
        return $this;
    }

    /**
     * Insert the values on the final of the collection
     *
     * @param  mixed $values
     *
     * @return self
     */
    public function push(...$values): self
    {
        array_push($this->content, ...static::checkAll($values));
        $this->increment(count($values));
        return $this;
    }

    /**
     * Perform a simplified for loop
     *
     * @param  int     $i
     * @param  int     $add
     * @param  callable $callback
     *
     * @return void
     */
    public function for(int $i, int $add, callable $callback)
    {
        $keys   = array_keys($this->content);
        $count  = count($this->content);

        for ($i; ($add >= 0 ? $i < $count : $i >= 0); $i += $add) {
            $callback($keys[$i], $this->content[$keys[$i]]);
        }
    }

    /**
     * Perform a foreach loop
     *
     * @param  callable $callback
     *
     * @return void
     */
    public function each(callable $callback)
    {
        foreach ($this->content as $key => $value) {
            $callback($key, $value);
        }
    }

    /**
     * Apply a callback in all elements of the collection
     *
     * @param  callable $callback
     *
     * @return void
     */
    public function walk(callable $callback, $mode = \RecursiveIteratorIterator::LEAVES_ONLY)
    {
        $iterator = new \RecursiveArrayIterator($this->content);
        foreach (new \RecursiveIteratorIterator($iterator, $mode) as $key => $value) {
            $callback($key, $value);
        }
    }

    /**
     * Sum all values in the collection
     *
     * @return mixed
     */
    public function sum()
    {
        return array_sum($this->content);
    }

    /**
     * Check if the value exist in the collection
     * 
     * @param mixed $value
     *
     * @return bool
     */
    public function contains($value): bool
    {
        return in_array(static::check($value), $this->content);
    }

    /**
     * Applies the callback to all elements
     *
     * @param  callable $callback
     *
     * @return self
     */
    public function map(callable $callback): self
    {
        $keys   = array_keys($this->content);
        $result = array_map($callback, $keys, $this->content);
        return $this->return(array_reduce($result, function($a, $b) {
            return (array) $a + (array) $b;
        }));
    }

    /**
     * Filter the collection using a callable function
     *
     * @param  callable $callback
     *
     * @return self
     */
    public function filter(callable $callback): self
    {
        $filter = [];
        // array_filter is not used as for the order of the arguments differs from others methods
        foreach ($this->content as $key => $value) {
            if ($callback($key, $value)) {
                $filter[$key] = $value;
            }
        }
        return $this->return($filter);
    }

    /**
     * Reduce the collection to a single value
     *
     * @param  callable $callback
     *
     * @return self
     */
    public function reduce(callable $callback)
    {
        $content = $this->content;
        $initial = array_shift($content);
        $result  = array_reduce($content, $callback, $initial);
        return $this->return($result);
    }

    /**
     * Determine if a key is set and it's value is not null
     *
     * @param  mixed $key
     *
     * @return bool
     */
    public function isset($key): bool
    {
        return isset($this[$key]);
    }

    /**
     * Determine if a key is not set
     *
     * @param  mixed $key
     *
     * @return bool
     */
    public function noset($key): bool
    {
        return !isset($this[$key]);
    }

    /**
     * Determine wheter a position in collection is empty
     *
     * @param  mixed $key
     *
     * @return bool
     */
    public function empty($key): bool
    {
        return empty($this[$key]);
    }

    /**
     * Determine wheter a position in collection is not empty
     *
     * @param  mixed $key
     *
     * @return bool
     */
    public function filled($key): bool
    {
        return !empty($this[$key]);
    }

    /**
     * Unset a given position of the collection
     *
     * @param  mixed $key
     *
     * @return void
     */
    public function unset($key)
    {
        unset($this[$key]);
    }

    /**
     * Remove the first element from the collection, and return the removed value
     *
     * @return mixed
     */
    public function shift()
    {
        $value = array_shift($this->content);
        $this->decrement();
        return $this->return($value);
    }

    /**
     * Remove the last element from the collection, and return the removed value
     *
     * @return mixed
     */
    public function pop()
    {
        $value = array_pop($this->content);
        $this->decrement();
        return $this->return($value);
    }

    /**
     * Join collection's elements into string
     *
     * @return string
     */
    public function join(string $glue)
    {
        return implode($glue, $this->content);
    }

    /**
     * Get a random element of the array
     *
     * @param int $num
     */
    public function random(int $num = 1)
    {
        return $this->return($this[array_rand($this->content, $num)]);
    }

    /**
     * Shuffle the array
     * 
     * @return self
     */
    public function shuffle(): self
    {
        $content = $this->content;
        shuffle($content);
        return $this->return($content);
    }

    /**
     * Exchange all keys with their associated values
     *
     * @return self
     */
    public function flip(): self
    {
        return $this->return(array_flip($this->content));
    }

    /**
     * Return a object with all the keys of the collection
     *
     * @return self
     */
    public function keys(): self
    {
        return $this->return(array_keys($this->content));
    }

    /**
     * Return a object with all the values of the collection
     *
     * @return self
     */
    public function values(): self
    {
        return $this->return(array_values($this->content));
    }

    /**
     * Return the values from a single column
     *
     * @param  mixed $key
     * @param  mixed $index
     *
     * @return self
     */
    public function column($key, $index = null)
    {
        return $this->return(array_column($this->content, $key, $index));
    }
    
    /**
     * Split the collection into parts
     *
     * @param  int $size
     * @param  bool $preserve_keys
     *
     * @return self
     */
    public function chunk(int $size, bool $preserve_keys = false): self
    {
        return $this->return(array_chunk($this->content, $size, $preserve_keys));
    }

    /**
     * Remove duplicated values
     *
     * @return self
     */
    public function unique(int $flags = SORT_STRING): self
    {
        return $this->return(array_unique($this->content, $flags));
    }

    /**
     * Return the first non null value
     *
     * @return mixed
     */
    public function coalesce()
    {
        foreach ($this->content as $value) {
            if (!is_null($value)) {
                return $this->return($value);
            }
        }
        return null;
    }

    /**
     * Merge all sublevels of the collection into one
     *
     * @return self
     */
    public function merge(): self
    {
        return $this->reduce('array_merge');
    }

    /**
     * Reverse the order of the collection
     *
     * @return self
     */
    public function reverse($preserve_keys = null): self
    {
        return $this->return(array_reverse($this->content, $preserve_keys));
    }

    /**
     * Return a key from a value in collection if it exists
     */
    public function search($value, bool $strict = null)
    {
        return array_search(static::check($value), $this->content, $strict);
    }

    /**
     * Return the first element of the collection
     *
     * @return void
     */
    public function first()
    {
        reset($this->content);
        return $this->return(current($this->content));
    }

    /**
     * Return the last element of the collection
     *
     * @return void
     */
    public function last()
    {
        $value = end($this->content);
        reset($this->content);
        return $this->return($value);
    }

    /**
     * Change the case of all keys in the collection to lower case
     *
     * @return self
     */
    public function lower(): self
    {
        $lower = function(&$array) use (&$lower) {
            $array = array_change_key_case($array, CASE_LOWER);
            foreach ($array as $key => $value)
                if (is_array($value)) $lower($array[$key]);
        };
        $lower($this->content);
        return $this;
    }

    /**
     * Change the case of all keys in the collection to upper case
     *
     * @return self
     */
    public function upper(): self
    {
        $upper = function(&$array) use (&$upper) {
            $array = array_change_key_case($array, CASE_UPPER);
            foreach ($array as $key => $value)
                if (is_array($value)) $upper($array[$key]);
        };
        $upper($this->content);
        return $this;
    }

    /**
     * Get the value associated a given key or keys
     *
     * @param  mixed $keys
     *
     * @return mixed
     */
    public function get(...$keys)
    {
        $count = count($keys);

        if ($count === 0) {
            return $this->content;
        }

        if ($count === 1) {
            return $this[$keys[0]] ?? null;
        }

        $return = new static();
        foreach ($keys as $key) {
            $return[$key] = $this[$key] ?? null;
        }
        return $return;
    }

    /**
     * Insert a value in a associated key
     *
     * @param  string $key
     * $param  mixed $value
     *
     * @return mixed
     */
    public function set(string $key, $value)
    {
        $this[$key] = $value;
        return $this;
    }

    /**
     * Return a new object if value is an object, else return the value
     *
     * @return mixed
     */
    protected function return($content) {
        return is_array($content) ? new static($content) : $content;
    }

    /**
     * Increment the length property
     *
     * @return void
     */
    private function increment(int $value = 1) {
        $this->length += $value;
    }

    /**
     * Decrement the length property
     *
     * @return void
     */
    private function decrement(int $value = 1) {
        $this->length -= $value;
    }

    // ================================================================= //
    // ======================= MAGIC METHODS =========================== //
    // ================================================================= //

    public function __get($property) {
        if ($property === 'length') {
            return $this->length;
        }
        return $this[$property] ?? null;
    }

    public function __set($property, $value) {
        if ($property === 'length') {
            throw new \InvalidArgumentException('length property is readonly');
        }
        $this[$property] = $value;
    }

    public function __isset($property) {
        return isset($this->content[$property]);
    }

    public function __unset($property) {
        unset($this->content[$property]);
    }

    public function __toString()
    {
        return json_encode($this->content, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    // ================================================================= //
    // ======================= STATIC METHODS =========================== //
    // ================================================================= //

    /**
     * Verify whether a element is an Collection object
     *
     * @param  mixed $object
     *
     * @return bool
    */
    public static function isCollection($object): bool
    {
        return $object instanceof static;
    }

    /**
     * Combine two collections, using the first for keys and the second for values
     *
     * @param  mixed $keys
     * @param  mixed $values
     *
     * @return self
     */
    public static function combine($keys, $values): self
    {
        return new static(array_combine(static::check($keys), static::check($values)));
    }

     /**
     * Create a collection, containing a range of elements
     *
     * @param  mixed $start
     * @param  mixed $end
     * @param  int   $step
     *
     * @return self
     */
    public static function range($start, $end, $step = 1): self
    {
        return new static(range($start, $end, $step));
    }

    /**
     * Check if the value is instance of self and extract the content
     *
     * @return mixed
     */
    private static function check($value) {
        return $value instanceof static ? $value->get() : $value;
    }

    /**
     * Check each value of a list, and if it is instance of self and extract the content
     *
     * @return mixed
     */
    private static function checkAll(array $values) {
        return array_map(function($value) {
            return static::check($value);
        }, $values);
    }
}
