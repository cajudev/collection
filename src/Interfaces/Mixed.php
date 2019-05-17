<?php

namespace Cajudev\Interfaces;

interface Mixed
{
    public function isInt(string $key): bool;
    public function isBool(string $key): bool;
    public function isFloat(string $key): bool;
    public function isNumeric(string $key): bool;
    public function isString(string $key): bool;
    public function isArray(string $key): bool;
}