<?php

use PHPUnit\Framework\TestCase;

use Cajudev\Callback;

class CallbackTest extends TestCase
{
    public function test_should_return_the_the_arguments_of_the_callback()
    {
        $callback = new Callback(function ($key, $value) {
            return [$key => $value];
        });
        $args = $callback->args();
        $this->assertEquals('key', $args->get(0)->name);
        $this->assertEquals('value', $args->get(1)->name);
    }

    public function test_should_execute_the_callback_using_closure()
    {
        $callback = new Callback(function ($key, $value) {
            return [$key => $value];
        });
        $result = $callback->exec('lorem', 'ipsum');
        $this->assertEquals(['lorem' => 'ipsum'], $result);
    }

    public function test_should_execute_the_callback_using_class_as_closure()
    {
        $callback = new Callback(function ($key, $value) {
            return [$key => $value];
        });
        $result = $callback('lorem', 'ipsum');
        $this->assertEquals(['lorem' => 'ipsum'], $result);
    }

    public function test_should_execute_the_callback_using_callable_array()
    {
        $callback = new Callback([$this, 'assertTrue']);
        $result = $callback->exec(true, true);
    }
}
