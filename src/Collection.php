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

    const COLLECTION_TO_ARRAY = 0;
    const ARRAY_TO_COLLECTION = 1;

    protected $content;
    protected $length;

    /**
     * @param  mixed $content
     *
     * @return void
     */
    public function __construct($content = [])
    {
        $this->content = $this->construct($content);
        $this->count();
    }
  
    /**
     * Parse the __construct argument received
     *
     * @param  mixed $content
     *
     * @return array
     */
    private function construct($content): array {
        if ($content instanceof static) {
            return $content->get();
        }
  
        if (is_object($content)) {
            $parser = new ObjectParser($content);
            return $parser->parse();
        }
  
        if (is_array($content)) {
            return static::check($content);
        }
  
        throw new \InvalidArgumentException('Invalid Type: Argument must be an array or object.');
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
        return new CollectionIterator($this->content);
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
        array_unshift($this->content, ...static::check($values));
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
        array_push($this->content, ...static::check($values));
        $this->increment(count($values));
        return $this;
    }

    /**
     * Perform a simplified for loop
     *
     * @param  int      $i
     * @param  int      $add
     * @param  callable $callback
     * @param  int      $mode
     *
     * @return void
     */
    public function for(int $i, int $add, callable $callback, int $mode = self::COLLECTION_TO_ARRAY)
    {
        $keys   = array_keys($this->content);
        $count  = count($this->content);

        for ($i; ($add >= 0 ? $i < $count : $i >= 0); $i += $add) {
            $value = static::parse($this->content[$keys[$i]], $mode);
            $callback($keys[$i], $value);
        }
    }

    /**
     * Perform a foreach loop
     *
     * @param  callable $callback
     * @param  int      $mode
     *
     * @return void
     */
    public function each(callable $callback, int $mode = self::COLLECTION_TO_ARRAY)
    {
        foreach ($this->content as $key => $value) {
            $value = static::parse($value, $mode);
            $callback($key, $value);
        }
    }

    /**
     * Apply a callback in all elements of the collection
     *
     * @param  callable $callback
     * @param  int      $type
     * @param  int      $mode
     *
     * @return void
     */
    public function walk(callable $callback, $type = \RecursiveIteratorIterator::LEAVES_ONLY, int $mode = self::COLLECTION_TO_ARRAY)
    {
        $iterator = new \RecursiveArrayIterator($this->content);
        foreach (new \RecursiveIteratorIterator($iterator, $type) as $key => $value) {
            $value = static::parse($value, $mode);
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
        return in_array(static::parse($value), $this->content);
    }

    /**
     * Applies the callback to all elements
     *
     * @param  callable $callback
     * @param  int      $mode
     *
     * @return self
     */
    public function map(callable $callback, int $mode = self::COLLECTION_TO_ARRAY): self
    {
        $return = [];
        foreach ($this->content as $key => $value) {
            $value  = static::parse($value, $mode);
            $result = $callback($key, $value);
            $value  = reset($result);
            $return[key($result)] = is_array($value) ? static::check($value) : static::parse($value);
        }
        return $this->return($return);
    }

    /**
     * Filter the collection using a callable function
     *
     * @param  callable $callback
     * @param  int      $mode
     *
     * @return self
     */
    public function filter(callable $callback, int $mode = self::COLLECTION_TO_ARRAY): self
    {
        $return = [];
        foreach ($this->content as $key => $value) {
            $value = static::parse($value, $mode);
            if ($callback($key, $value)) {
                $return[$key] = $value;
            }
        }
        return $this->return($return);
    }

    /**
     * Reduce the collection to a single value
     *
     * @param  callable $callback
     * @param  int      $mode
     *
     * @return self
     */
    public function reduce(callable $callback, int $mode = self::COLLECTION_TO_ARRAY)
    {
        $content  = $this->content;
        $previous = array_shift($content);
        while ($next = array_shift($content)) {
            $previous = static::parse($previous, $mode);
            $next     = static::parse($next, $mode);
            $previous = $callback($previous, $next);
        }
        return $this->return($previous);
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
        $this->decrement();
        return $this->return(array_shift($this->content));
    }

    /**
     * Remove the last element from the collection, and return the removed value
     *
     * @return mixed
     */
    public function pop()
    {
        $this->decrement();
        return $this->return(array_pop($this->content));
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
        return array_search(static::parse($value), $this->content, $strict);
    }

    /**
     * Return the first element of the collection
     *
     * @return void
     */
    public function first()
    {
        return $this->return(reset($this->content));
    }

    /**
     * Return the last element of the collection
     *
     * @return void
     */
    public function last()
    {
        return $this->return(end($this->content));
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
    protected function return($content)
    {
        return static::parse($content, self::ARRAY_TO_COLLECTION);
    }

    /**
     * Increment the length property
     *
     * @return void
     */
    private function increment(int $value = 1)
    {
        $this->length += $value;
    }

    /**
     * Decrement the length property
     *
     * @return void
     */
    private function decrement(int $value = 1)
    {
        $this->length -= $value;
    }

    // ================================================================= //
    // ======================= MAGIC METHODS =========================== //
    // ================================================================= //

    public function __get($property)
    {
        if ($property === 'length') {
            return $this->length;
        }
        return $this[$property] ?? null;
    }

    public function __set($property, $value)
    {
        if ($property === 'length') {
            throw new \InvalidArgumentException('length property is readonly');
        }
        $this[$property] = $value;
    }

    public function __isset($property)
    {
        return isset($this->content[$property]);
    }

    public function __unset($property)
    {
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
        return new static(array_combine(static::parse($keys), static::parse($values)));
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
    * Check all elements in an array and when it is a Collection, get its internal value
    *
    * @param  array $content
    *
    * @return array
    */
    private static function check(array $content): array
    {
        foreach ($content as $key => $value) {
            $content[$key] = is_array($value) ? static::check($value) : static::parse($value);
        }
        return $content;
    }

    /**
    * Parse a value according with the received mode
    *
    * @param  mixed $value
    *
    * @return mixed
    */
   private static function parse($value, int $mode = self::COLLECTION_TO_ARRAY)
   {

        if ($mode === self::ARRAY_TO_COLLECTION) {
            return is_array($value) ? new static($value) : $value;
        }

        if ($mode === self::COLLECTION_TO_ARRAY) {
            return $value instanceof static ? $value->get() : $value;
        }

       return $value;
   }
}
