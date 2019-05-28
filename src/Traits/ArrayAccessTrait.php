<?php

namespace Cajudev\Traits;

use Cajudev\Strings;
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
        } elseif (preg_match('/^\w+$/', $key)) {
            $this->incrementIfKeyNotExists($key);
            $this->content[$key] = $value;
        } elseif (preg_match_all('/(?<=\.|^)(?<key>\w+)(?=\.|$)/', $key, $keys)) {
            $this->incrementIfKeyNotExists($keys['key'][0]);
            $ret =& $this->content;
            while (count($keys['key']) > 0) {
                $ret =& $ret[array_shift($keys['key'])];
            }
            $ret = $value;
        } else {
            throw new \InvalidArgumentException('Wrong Pattern');
        }
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
        if (preg_match('/^\w+$/', $key)) {
            return isset($this->content[$key]);
        } elseif (preg_match_all('/(?<=\.|^)(?<key>\w+)(?=\.|$)/', $key, $keys)) {
            $ret =& $this->content;
            while (count($keys['key']) > 0) {
                $key = array_shift($keys['key']);
                if (!isset($ret[$key])) {
                    return false;
                }
                $ret =& $ret[$key];
            }
            return isset($ret);
        } else {
            throw new \InvalidArgumentException('Wrong Pattern');
        }
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
        if (preg_match('/^\w+$/', $key)) {
            $this->decrement();
            unset($this->content[$key]);
        } elseif (preg_match_all('/(?<=\.|^)(?<key>\w+)(?=\.|$)/', $key, $keys)) {
            $ret =& $this->content;
            while (count($keys['key']) > 1) {
                $key = array_shift($keys['key']);
                if (!isset($ret[$key])) {
                    return false;
                }
                $ret =& $ret[$key];
            }
            unset($ret[$keys['key'][0]]);
        } else {
            throw new \InvalidArgumentException('Wrong Pattern');
        }
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
        if (preg_match('/^\w+$/', $key)) {
            $this->incrementIfKeyNotExists($key);
            $ret =& $this->content[$key];
        } elseif (preg_match_all('/(?<=\.|^)(?<key>\w+)(?=\.|$)/', $key, $keys)) {
            $this->incrementIfKeyNotExists($keys['key'][0]);
            $ret =& $this->content;
            while (count($keys['key']) > 0) {
                $ret =& $ret[array_shift($keys['key'])];
            }
        } elseif (preg_match('/^(?<start>\w+):(?<end>\w+)$/', $key, $result)) {
            $start = $result['start'];
            $end   = $result['end'];
            $ret   = $start < $end 
                   ? array_slice($this->content, $start, $end - $start + 1)
                   : array_reverse(array_slice($this->content, $end, $start - $end + 1));
        } else {
            throw new \InvalidArgumentException('Wrong Pattern');
        }

        if (is_array($ret)) {
            $return = (new static())->setByReference($ret);
            return $return;
        }

        return $ret;
    }

    public function incrementIfKeyNotExists($key) {
        if (!array_key_exists($key, $this->content)) {
            $this->increment();
        };
    }
}