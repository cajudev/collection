<?php

namespace Cajudev;

use Cajudev\Interfaces\Set;
use Cajudev\Interfaces\Mixed;
use Cajudev\Interfaces\Backup;
use Cajudev\Interfaces\Sortable;

class Arrays implements \ArrayAccess, \Iterator, \Countable, Sortable, Mixed, Set, Backup
{
    use \Cajudev\Traits\SetTrait;
    use \Cajudev\Traits\MixedTrait;
    use \Cajudev\Traits\BackupTrait;
    use \Cajudev\Traits\SortableTrait;
    use \Cajudev\Traits\IteratorTrait;
    use \Cajudev\Traits\CountableTrait;
    use \Cajudev\Traits\ArrayAccessTrait;

    private const BREAK    = 'break';
    private const CONTINUE = 'continue';

    private $content;
    private $length;

    public function __construct(array $content = [])
    {
        $this->content = $content;
        $this->count();
    }

    /**
     * Set the content of the array by reference
     *
     * @param  array $array
     *
     * @return self
     */
    public function setByReference(array &$array): self
    {
        $this->content =& $array;
        $this->count();
        return $this;
    }

    /**
     * Insert the values on the beginning of the array
     *
     * @param  mixed $values
     *
     * @return self
     */
    public function unshift(...$values): self
    {
        array_unshift($this->content, ...$values);
        $this->increment(count($values));
        return $this;
    }

    /**
     * Insert the values on the final of the array
     *
     * @param  mixed $values
     *
     * @return self
     */
    public function push(...$values): self
    {
        array_push($this->content, ...$values);
        $this->increment(count($values));
        return $this;
    }

    /**
     * Perform a simplified for loop
     *
     * @param  int     $i
     * @param  int     $add
     * @param  Closure $function
     *
     * @return void
     */
    public function for(int $i, int $add, \Closure $function)
    {
        $keys   = array_keys($this->content);
        $count  = count($this->content);

        for ($i; ($add >= 0 ? $i < $count : $i >= 0); $i += $add) {
            $return = $function($keys[$i], $this->content[$keys[$i]]);
            switch ($return) {
                case self::BREAK: break 2;
                case self::CONTINUE; continue 2;
            }
        }
    }

    /**
     * Perform a foreach loop
     *
     * @param  Closure $function
     *
     * @return void
     */
    public function each(\Closure $function)
    {
        foreach ($this->content as $key => $value) {
            $return = $function($key, $value);
            switch ($return) {
                case self::BREAK: break 2;
                case self::CONTINUE; continue 2;
            }
        }
    }

    /**
     * Sum all values in the array
     *
     * @return mixed
     */
    public function sum()
    {
        return array_sum($this->content);
    }

    /**
     * Check if the value exist in the array
     *
     * @return mixed
     */
    public function contains($value): bool
    {
        return in_array($value, $this->content);
    }

    /**
     * Applies the callback to all elements
     *
     * @param  callable $handle
     *
     * @return self
     */
    public function map(callable $handle): self
    {
        $keys = array_keys($this->content);
        $this->content = array_column(array_map($handle, $keys, $this->content), 1, 0);
        return $this;
    }

    /**
     * Filter the array using a callable function
     *
     * @param  callable $handle
     *
     * @return self
     */
    public function filter(callable $handle): self
    {
        $this->content = array_filter($this->content, $handle, ARRAY_FILTER_USE_BOTH);
        $this->count();
        return $this;
    }

    /**
     * Reduce the array to a single value
     *
     * @param  callable $handle
     *
     * @return self
     */
    public function reduce(callable $handle)
    {
        $content = $this->content;
        $initial = array_shift($content);
        $result  = array_reduce($content, $handle, $initial);
        if (is_array($result)) {
            $result = new static($result);
        }
        return $result;
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
     * Determine wheter a variable is empty
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
     * Determine wheter a variable is not empty
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
     * Unset a given variable
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
     * Remove the first element from an array
     *
     * @return self
     */
    public function shift(): self
    {
        array_shift($this->content);
        $this->decrement();
        return $this;
    }

    /**
     * Remove the last element from an array
     *
     * @return self
     */
    public function pop(): self
    {
        array_pop($this->content);
        $this->decrement();
        return $this;
    }

    /**
     * Join array elements into string
     *
     * @return string
     */
    public function join(string $glue)
    {
        return implode($glue, $this->content);
    }

    /**
     * Exchange all keys with their associated values
     *
     * @return self
     */
    public function flip(): self
    {
        $this->content = array_flip($this->content);
        return $this;
    }

    /**
     * Return a object with all the keys of the array
     *
     * @return self
     */
    public function keys(): self
    {
        return new static(array_keys($this->content));
    }

    /**
     * Return a object with all the values of the array
     *
     * @return self
     */
    public function values(): self
    {
        return new static(array_values($this->content));
    }

    /**
     * Return the values from a single column
     *
     * @param  mixed $key
     * @param  mixed $index
     *
     * @return self
     */
    public function column($key, $index = null): ?self
    {
        return new static(array_column($this->content, $key, $index));
    }
    
    /**
     * Split the array into chunks
     *
     * @param  int $size
     * @param  bool $preserve_keys
     *
     * @return self
     */
    public function chunk(int $size, bool $preserve_keys = false): self
    {
        $this->content = array_chunk($this->content, $size, $preserve_keys);
        $this->count();
        return $this;
    }

    /**
     * Remove duplicated values
     *
     * @return self
     */
    public function unique(): self
    {
        $this->content = array_unique($this->content);
        $this->count();
        return $this;
    }

    /**
     * Merge all sublevels of the array into one
     *
     * @return self
     */
    public function merge(): self
    {
        $this->content = $this->reduce('array_merge')->get();
        $this->count();
        return $this;
    }

    /**
     * Reverse the order of the array
     *
     * @return self
     */
    public function reverse($preserve_keys = null): self
    {
        $this->content = array_reverse($this->content, $preserve_keys);
        return $this;
    }

    /**
     * Return a key from a value in array if it exists
     *
     * @return self
     */
    public function search($value, bool $strict = null): int
    {
        return array_search($value, $this->content, $strict);
    }

    /**
     * Return the last element of the array
     *
     * @return void
     */
    public function last()
    {
        $return = $this->end();
        $this->rewind();
        return $return;
    }

    /**
     * Change the case of all keys in the array to lower case
     *
     * @return self
     */
    public function lower(): self
    {
        $this->content = array_change_key_case($this->content, CASE_LOWER);
        return $this;
    }

    /**
     * Change the case of all keys in the array to upper case
     *
     * @return self
     */
    public function upper(): self
    {
        $this->content = array_change_key_case($this->content, CASE_UPPER);
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
        if (!$keys) {
            return $this->content;
        }
        return $this[implode('.', $keys)] ?? null;
    }

    /**
     * Insert a value in a associated key
     *
     * @param  mixed $keys
     *
     * @return mixed
     */
    public function set($value, ...$keys)
    {
        if (!$keys) {
            $this[] = $value;
        } else {
            $this[implode('.', $keys)] = $value;
        }
        return $this;
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

    public function __get($property) {
        if ($property == 'length') {
            return $this->length;
        }
    }

    public function __toString()
    {
        return json_encode($this->get(), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    /**
     * Verify whether a element is an Arrays object
     *
     * @param  mixed $array
     *
     * @return bool
    */
    public static function isArrays($object): bool
    {
        return $object instanceof static;
    }

    /**
     * Transform all properties of a object into an associative array
     *
     * @param  $object
     *
     * @return self
     */
    public static function fromObject($object): ?self
    {
        if (!is_object($object)) {
            return null;
        }

        $vars = new static((array) $object);
        return $vars->map(function($key, $value) {
            return [preg_replace('/.*\0(.*)/', '\1', $key), $value];
        });
    }

    /**
     * Combine two arrays, using the first for keys and the second for values
     *
     * @param  mixed $keys
     * @param  mixed $values
     *
     * @return self
     */
    public static function combine($keys, $values): self
    {
        if ($keys instanceof static) {
            $keys = $keys->get();
        }

        if ($values instanceof static) {
            $values = $values->get();
        }
        
        return new static(array_combine($keys, $values));
    }
}
