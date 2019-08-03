<?php

namespace Cajudev;

use Cajudev\Collection;
use Cajudev\Interfaces\Parser;

class ObjectParser implements Parser
{
    private $object;

    public function __construct($object) {
        if (!is_object($object)) {
            throw new \InvalidArgumentException("Invalid argument {$object}(" . gettype($object) . ')');
        }
        $this->object = $object;
    }

    /**
     * Transform all properties of a object into an associative array
     *
     * @return array
     */
    public function parse(): array
    {
        if ($this->object instanceof Collection) {
            return $this->object->get();
        }

        $return = [];
        foreach ((array)$this->object as $key => $value) {
            $return[preg_replace('/.*\0(.*)/', '\1', $key)] = $value;
        }
        return $return;
    }
}
