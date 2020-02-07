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
        $result = $callback->exec($this, 'lorem', 'ipsum');
        $this->assertEquals(['lorem' => 'ipsum'], $result);
    }

    public function test_should_execute_the_callback_using_class_as_closure()
    {
        $callback = new Callback(function ($key, $value) {
            return [$key => $value];
        });
        $result = $callback($this, 'lorem', 'ipsum');
        $this->assertEquals(['lorem' => 'ipsum'], $result);
    }

    public function test_should_execute_the_callback_using_callable_array()
    {
        $callback = new Callback([$this, 'assertTrue']);
        $result = $callback->exec(true, true, true);
    }

    public function test_should_receive_only_value_as_argument()
    {
        $callback = new Callback(function ($value) {
            return $value;
        });
        $result = $callback->exec($this, 'lorem', 'ipsum');
        $this->assertEquals('ipsum', $result);
    }

    public function test_should_receive_key_and_value_as_argument()
    {
        $callback = new Callback(function ($key, $value) {
            return [$key, $value];
        });
        $result = $callback->exec($this, 'lorem', 'ipsum');
        $this->assertEquals(['lorem', 'ipsum'], $result);
    }

    public function test_should_receive_self_reference_key_and_value_as_argument()
    {
        $callback = new Callback(function ($self, $key, $value) {
            return [$self, $key, $value];
        });
        $result = $callback->exec($this, 'lorem', 'ipsum');
        $this->assertEquals([$this, 'lorem', 'ipsum'], $result);
    }

    public function test_should_throws_exception_when_callback_has_more_than_allowed_arguments()
    {
        $this->expectException(\InvalidArgumentException::class);
        $callback = new Callback(function ($self, $key, $value, $invalid) {
        });
        $callback->exec($this, 'lorem', 'ipsum');
    }
}
