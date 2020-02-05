<?php

namespace Cajudev;

use Cajudev\Collection;
use Cajudev\Interfaces\Parser;

class ObjectParser implements Parser
{
    private $object;

    public function __construct($object)
    {
        $this->object = $object;
    }
    
    public function parse(): array
    {
        $return = [];
        foreach ((array)$this->object as $key => $value) {
            $return[preg_replace('/.*\0(.*)/', '\1', $key)] = $value;
        }
        return $return;
    }
}
