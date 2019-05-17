<?php

namespace Cajudev\Traits;

trait MixedTrait
{

    /** 
     * Verifify if the value is a integer
     * 
     * @return bool
     */
    public function isInt(string $key): bool
    {
        return is_int($this->content[$key] ?? null);
    }

    /** 
     * Verifify if the value is a boolean
     * 
     * @return bool
     */
    public function isBool(string $key): bool
    {
        return is_bool($this->content[$key] ?? null);
    }

    /** 
     * Verifify if the value is a float
     * 
     * @return bool
     */
    public function isFloat(string $key): bool
    {
        return is_float($this->content[$key] ?? null);
    }

    /** 
     * Verifify if the value is a number
     * 
     * @return bool
     */
    public function isNumeric(string $key): bool
    {
        return is_numeric($this->content[$key] ?? null);
    }

    /** 
     * Verifify if the value is a string
     * 
     * @return bool
     */
    public function isString(string $key): bool
    {
        return is_string($this->content[$key] ?? null);
    }

    /** 
     * Verifify if the value is an array
     * 
     * @return bool
     */
    public function isArray(string $key): bool
    {
        return is_array($this->content[$key] ?? null);
    }

}
