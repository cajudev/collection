<?php

namespace Cajudev\Traits;

use Cajudev\Arrays;

trait ArrayAccessTrait
{
    /**
     * Set a value in array
     *
     * @param  mixed $key
     * @param  mixed $value
     *
     * @return void
     */
    public function offsetSet($key, $value)
    {
        if (Arrays::isArrays($value)) {
            $value = $value->get();
        }

        if ($key === null) {
            $this->content[] = $value;
            $this->increment();
            return;
        }
        
        if (preg_match(static::KEY_NOTATION, $key)) {
            $this->increment(!array_key_exists($key, $this->content));
            $this->content[$key] = $value;
            return;
        }
        
        if (!preg_match_all(static::DOT_NOTATION, $key, $keys)) {
            throw new \InvalidArgumentException("Wrong Pattern {$key} is not a valid key");
        }
            
        $this->increment(!array_key_exists($keys['key'][0], $this->content));
        $ret =& $this->content;
        while (count($keys['key']) > 0) {
            $ret =& $ret[array_shift($keys['key'])];
        }
        $ret = $value;
    }

    /**
     * Check if a key is set
     *
     * @param  mixed $key
     *
     * @return bool
     */
    public function offsetExists($key): bool
    {
        if (preg_match(static::KEY_NOTATION, $key)) {
            return isset($this->content[$key]);
        } 
        
        if (!preg_match_all(static::DOT_NOTATION, $key, $keys)) {
            throw new \InvalidArgumentException("Wrong Pattern {$key} is not a valid key");
        }

        $ret =& $this->content;
        while (count($keys['key']) > 0) {
            if (!isset($keys['key'][0])) {
                return false;
            }
            $ret =& $ret[array_shift($keys['key'])];
        }
        return isset($ret);
    }

    /**
     * Unset a value in array
     *
     * @param  mixed $key
     *
     * @return void
     */
    public function offsetUnset($key)
    {
        if (preg_match(static::KEY_NOTATION, $key)) {
            $this->decrement();
            unset($this->content[$key]);
            return true;
        }
        
        if (!preg_match_all(static::DOT_NOTATION, $key, $keys)) {
            throw new \InvalidArgumentException("Wrong Pattern {$key} is not a valid key");
        }
        
        $ret =& $this->content;
        while (count($keys['key']) > 1) {
            if (!isset($ret[$keys['key'][0]])) {
                return false;
            }
            $ret =& $ret[array_shift($keys['key'])];
        }
        unset($ret[$keys['key'][0]]);
    }

    /**
     * Get a value from the array
     *
     * @param  mixed $key
     *
     * @return mixed
     */
    public function &offsetGet($key)
    {
        $ret =& $this->offsetGetValue($key);
        if (!(is_null($ret) || is_array($ret))) {
            return $ret;
        }
        $return = (new static())->setByReference($ret);
        return $return;
    }

    /**
     * Auxiliary function of offsetGet
     */
    private function &offsetGetValue($key) {
        if (preg_match(static::KEY_NOTATION, $key)) {
            $this->increment(!array_key_exists($key, $this->content));
            return $this->content[$key];
        }
        
        if (preg_match_all(static::DOT_NOTATION, $key, $keys)) {
            $this->increment(!array_key_exists($keys['key'][0], $this->content));
            $ret =& $this->content;
            while (count($keys['key']) > 0) {
                $ret =& $ret[array_shift($keys['key'])];
            }
            return $ret;
        }
        
        if (!preg_match(static::INTERVAL_NOTATION, $key, $result)) {
            throw new \InvalidArgumentException("Wrong Pattern {$key} is not a valid key");
        }

        $start = $result['start'];
        $end   = $result['end'];
        $ret   = $start < $end 
               ? array_slice($this->content, $start, $end - $start + 1)
               : array_reverse(array_slice($this->content, $end, $start - $end + 1));

        return $ret;
    }
}