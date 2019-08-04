<?php

use Cajudev\Collection;
use Cajudev\CollectionIterator;
use PHPUnit\Framework\TestCase;

class CollectionIteratorTest extends TestCase
{
    public function setUp() {
        $this->iterator = new CollectionIterator(new Collection(['lorem' => 'ipsum', 'dolor' => 'sit', 'amet' => 'consectetur']));
    }

    public function test_valid_should_return_true()
    {
        self::assertTrue($this->iterator->valid());
    }

    public function test_current_should_return_current_element()
    {
        self::assertEquals('ipsum', $this->iterator->current());
    }

    public function test_key_should_return_current_key_of_element()
    {
        self::assertEquals('lorem', $this->iterator->key());
    }

    public function test_next_should_advance_internal_point()
    {
        $this->iterator->next();
        self::assertEquals('sit', $this->iterator->current());
    }

    public function test_rewind_should_reset_the_collection()
    {
        $this->iterator->next();
        $this->iterator->next();
        $this->iterator->rewind();
        self::assertEquals('ipsum', $this->iterator->current());
    }

    public function test_previous_should_return_internal_point()
    {
        $this->iterator->next();
        $this->iterator->previous();
        self::assertEquals('ipsum', $this->iterator->current());
    }

    public function test_valid_should_return_false()
    {
        $this->iterator->next();
        $this->iterator->next();
        $this->iterator->next();
        self::assertFalse($this->iterator->valid());
    }
}