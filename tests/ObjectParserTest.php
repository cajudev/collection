<?php

use Cajudev\ObjectParser;
use PHPUnit\Framework\TestCase;

class ObjectParserTest extends TestCase
{
    public function test_parse_should_transform_object_into_array()
    {
        $object = new class() {
            public    $lorem = 'ipsum';
            protected $dolor = 'sit';
            private   $amet  = 'consectetur';
        };

        $expect = [
            'lorem' => 'ipsum',
            'dolor' => 'sit',
            'amet'  => 'consectetur',
        ];

        $parser = new ObjectParser($object);

        self::assertEquals($expect, $parser->parse());
    }
}