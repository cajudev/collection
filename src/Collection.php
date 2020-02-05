<?php

namespace Cajudev;

use Cajudev\ObjectParser;
use Cajudev\CollectionIterator;

use Cajudev\Interfaces\Set;
use Cajudev\Interfaces\Mixed;
use Cajudev\Interfaces\Sortable;
use Cajudev\Interfaces\CollectionInterface;

class Collection implements CollectionInterface, \ArrayAccess, \IteratorAggregate, \Countable, Sortable, Set
{
    use \Cajudev\Traits\SetTrait;
    use \Cajudev\Traits\SortableTrait;
    use \Cajudev\Traits\ArrayAccessTrait;

    const KEY_NOTATION      = '/^[^.:]+$/';
    const DOT_NOTATION      = '/(?<=\.|^)(?<key>[^.:]+)(?=\.|$)/';
    const INTERVAL_NOTATION = '/^(?<start>\d+):(?<end>\d+)$/';

    protected $content;
    protected $length;

    public function __construct($content = [])
    {
        $this->content = $this->construct($content);
        $this->count();
    }
  
    private function construct($content): array
    {
        if ($content instanceof static) {
            return $content->get();
        }
  
        if (is_object($content)) {
            $parser = new ObjectParser($content);
            return $parser->parse();
        }
  
        if (is_array($content)) {
            return static::sanitize($content);
        }
  
        throw new \InvalidArgumentException('Invalid Type: Argument must be an array or object.');
    }

    public function setByReference(array &$array = null): CollectionInterface
    {
        $array = $array ?? [];
        $this->content =& $array;
        $this->count();
        return $this;
    }

    public function count(int $mode = COUNT_NORMAL): int
    {
        if ($mode === COUNT_RECURSIVE) {
            return count($this->content, $mode);
        }

        $this->length = count($this->content);
        return $this->length;
    }

    public function getIterator(): \Iterator
    {
        return new CollectionIterator($this->content);
    }

    public function unshift(...$values): CollectionInterface
    {
        array_unshift($this->content, ...static::sanitize($values));
        $this->increment(count($values));
        return $this;
    }

    public function push(...$values): CollectionInterface
    {
        array_push($this->content, ...static::sanitize($values));
        $this->increment(count($values));
        return $this;
    }

    public function for(int $i, int $add, callable $callback, bool $array_as_collection = false)
    {
        $keys   = array_keys($this->content);
        $count  = count($this->content);

        for ($i; ($add >= 0 ? $i < $count : $i >= 0); $i += $add) {
            $value = static::parse($this->content[$keys[$i]], $array_as_collection);
            $callback($keys[$i], $value);
        }
    }

    public function each(callable $callback, bool $array_as_collection = false)
    {
        foreach ($this->content as $key => $value) {
            $value = static::parse($value, $array_as_collection);
            $callback($key, $value);
        }
    }

    public function walk(callable $callback, $type = \RecursiveIteratorIterator::LEAVES_ONLY, bool $array_as_collection = false)
    {
        $iterator = new \RecursiveArrayIterator($this->content);
        foreach (new \RecursiveIteratorIterator($iterator, $type) as $key => $value) {
            $value = static::parse($value, $array_as_collection);
            $callback($key, $value);
        }
    }

    public function sum(): int
    {
        return array_sum($this->content);
    }

    public function contains($value): bool
    {
        return in_array(static::parse($value), $this->content);
    }

    public function map(callable $callback, bool $array_as_collection = false): CollectionInterface
    {
        $return = [];
        foreach ($this->content as $key => $value) {
            $value  = static::parse($value, $array_as_collection);
            $result = $callback($key, $value);
            $value  = reset($result);
            $return[key($result)] = is_array($value) ? static::sanitize($value) : static::parse($value);
        }
        return $this->return($return);
    }

    public function filter(callable $callback, bool $array_as_collection = false): CollectionInterface
    {
        $return = [];
        foreach ($this->content as $key => $value) {
            $value = static::parse($value, $array_as_collection);
            if ($callback($key, $value)) {
                $return[$key] = $value;
            }
        }
        return $this->return($return);
    }

    public function reduce(callable $callback, bool $array_as_collection = false)
    {
        $content  = $this->content;
        $previous = array_shift($content);
        while ($next = array_shift($content)) {
            $previous = static::parse($previous, $array_as_collection);
            $next     = static::parse($next, $array_as_collection);
            $previous = $callback($previous, $next);
        }
        return $this->return($previous);
    }

    public function find(callable $callback, bool $array_as_collection = false)
    {
        foreach ($this->content as $key => $value) {
            $value = static::parse($value, $array_as_collection);
            if ($callback($key, $value)) {
                return $this->return($value);
            }
        }
        return false;
    }

    public function isset($key): bool
    {
        return isset($this[$key]);
    }

    public function noset($key): bool
    {
        return !isset($this[$key]);
    }

    public function empty($key): bool
    {
        return empty($this[$key]);
    }

    public function filled($key): bool
    {
        return !empty($this[$key]);
    }

    public function unset($key)
    {
        unset($this[$key]);
    }

    public function shift()
    {
        $this->decrement();
        return $this->return(array_shift($this->content));
    }

    public function pop()
    {
        $this->decrement();
        return $this->return(array_pop($this->content));
    }

    public function join(string $glue)
    {
        return implode($glue, $this->content);
    }

    public function random(int $num = 1)
    {
        return $this->return($this[array_rand($this->content, $num)]);
    }

    public function shuffle(): CollectionInterface
    {
        $content = $this->content;
        shuffle($content);
        return $this->return($content);
    }

    public function flip(): CollectionInterface
    {
        return $this->return(array_flip($this->content));
    }

    public function keys(): CollectionInterface
    {
        return $this->return(array_keys($this->content));
    }

    public function values(): CollectionInterface
    {
        return $this->return(array_values($this->content));
    }

    public function column($key, $index = null)
    {
        return $this->return(array_column($this->content, $key, $index));
    }
    
    public function chunk(int $size, bool $preserve_keys = false): CollectionInterface
    {
        return $this->return(array_chunk($this->content, $size, $preserve_keys));
    }

    public function unique(int $flags = SORT_STRING): CollectionInterface
    {
        return $this->return(array_unique($this->content, $flags));
    }

    public function coalesce()
    {
        foreach ($this->content as $value) {
            if (!is_null($value)) {
                return $this->return($value);
            }
        }
        return null;
    }

    public function merge(): CollectionInterface
    {
        return $this->reduce('array_merge');
    }

    public function reverse($preserve_keys = null): CollectionInterface
    {
        return $this->return(array_reverse($this->content, $preserve_keys));
    }

    public function search($value, bool $strict = null)
    {
        return array_search(static::parse($value), $this->content, $strict);
    }

    public function first()
    {
        return $this->return(reset($this->content));
    }

    public function last()
    {
        return $this->return(end($this->content));
    }

    public function lower(): CollectionInterface
    {
        $lower = function (&$array) use (&$lower) {
            $array = array_change_key_case($array, CASE_LOWER);
            foreach ($array as $key => $value) {
                if (is_array($value)) {
                    $lower($array[$key]);
                }
            }
        };
        $lower($this->content);
        return $this;
    }

    public function upper(): CollectionInterface
    {
        $upper = function (&$array) use (&$upper) {
            $array = array_change_key_case($array, CASE_UPPER);
            foreach ($array as $key => $value) {
                if (is_array($value)) {
                    $upper($array[$key]);
                }
            }
        };
        $upper($this->content);
        return $this;
    }

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

    public function set(string $key, $value)
    {
        $this[$key] = $value;
        return $this;
    }

    protected function return($content)
    {
        return static::parse($content, true);
    }

    private function increment(int $value = 1)
    {
        $this->length += $value;
    }

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

    public static function isCollection($object): bool
    {
        return $object instanceof static;
    }

    public static function combine($keys, $values): CollectionInterface
    {
        return new static(array_combine(static::parse($keys), static::parse($values)));
    }

    public static function range($start, $end, $step = 1): CollectionInterface
    {
        return new static(range($start, $end, $step));
    }

    // ================================================================= //
    // ======================= HELPER METHODS ========================== //
    // ================================================================= //

    private static function sanitize(array $content): array
    {
        foreach ($content as $key => $value) {
            $content[$key] = is_array($value) ? static::sanitize($value) : static::parse($value);
        }
        return $content;
    }

    private static function parse($value, bool $array_as_collection = false)
    {
        if ($array_as_collection) {
            return is_array($value) ? new static($value) : $value;
        }
        return $value instanceof static ? $value->get() : $value;
    }
}
