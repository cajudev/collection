<?php

namespace Cajudev\Interfaces;

interface Sortable
{
    public function sort();
    public function rsort();
    public function asort();
    public function arsort();
    public function ksort();
    public function krsort();
}