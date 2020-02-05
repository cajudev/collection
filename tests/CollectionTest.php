<?php

use PHPUnit\Framework\TestCase;
use Cajudev\Collection;

/**
 * Tests follows the sintax: test_methodname_should_action[_when_condition]
 */
class CollectionTest extends TestCase
{
    public function test_construct_should_update_internal_data_when_received_array()
    {
        $regularArray = ['lorem', 'ipsum', 'dolor', 'sit', 'amet,', 'consectetur', 'adipiscing', 'elit'];
        $collection = new Collection($regularArray);
        $this->assertEquals($regularArray, $collection->get());
    }

    public function test_construct_should_parse_attributes_when_received_object()
    {
        $object = new class {
            private $private = 'lorem';
            public $public = 'ipsum';
            protected $protected = 'dolor';
        };
        $collection = new Collection($object);
        $expect     = new Collection(['private' => 'lorem', 'public' => 'ipsum', 'protected' => 'dolor']);
        $this->assertEquals($expect, $collection);
    }

    public function test_construct_should_turn_into_regular_array_when_received_collection()
    {
        $collection1 = new Collection([1, 2, 3]);
        $collection2 = new Collection($collection1);
        $this->assertSame([1, 2, 3], $collection2->get());
    }

    public function test_construct_should_turn_into_regular_array_when_received_multidimensional_collection()
    {
        $collection = new Collection([[new Collection(), [[new Collection()]]]]);
        $this->assertSame([[[], [[[]]]]], $collection->get());
    }

    public function test_construct_should_throws_exception_when_received_another_type_of_argument()
    {
        $this->expectException(\InvalidArgumentException::class);
        $collection = new Collection('lorem');
    }

    public function test_set_should_associate_key_and_value()
    {
        $collection = new Collection();
        $collection->set('lorem', 'ipsum');
        $this->assertSame(['lorem' => 'ipsum'], $collection->get());
    }

    public function test_set_should_insert_array_when_received_another_collection()
    {
        $collection = new Collection();
        $collection->set('lorem', new Collection([1, 2, 3]));
        $array = $collection->get();
        $this->assertEquals([1, 2, 3], $array['lorem']);
    }

    public function test_set_should_associate_multidimensional_when_using_dot_notation()
    {
        $collection = new Collection();
        $collection->set('lorem.ipsum', 'dolor');
        $this->assertSame(['lorem' => ['ipsum' => 'dolor']], $collection->get());
    }

    public function test_set_should_update_length()
    {
        $collection = new Collection();
        $collection->set('lorem', 'ipsum');
        $this->assertSame(1, $collection->length);
    }

    public function test_push_should_append_value()
    {
        $collection = new Collection([1]);
        $collection->push('lorem');
        $this->assertEquals([1, 'lorem'], $collection->get());
    }

    public function test_push_should_accept_more_than_one_value()
    {
        $collection = new Collection([1]);
        $collection->push('lorem', 'ipsum', 'dolor');
        $this->assertEquals([1, 'lorem', 'ipsum', 'dolor'], $collection->get());
    }

    public function test_push_should_update_length_when_append_more_than_one_value()
    {
        $collection = new Collection([1]);
        $collection->push('lorem', 'ipsum', 'dolor');
        $this->assertEquals(4, $collection->length);
    }

    public function test_unshift_should_append_value_on_beginning()
    {
        $collection = new Collection([1]);
        $collection->unshift('lorem');
        $this->assertEquals(['lorem', 1], $collection->get());
    }

    public function test_unshift_should_append_more_than_one_value_on_beginning()
    {
        $collection = new Collection([1]);
        $collection->unshift('lorem', 'ipsum', 'dolor');
        $this->assertEquals(['lorem', 'ipsum', 'dolor', 1], $collection->get());
    }

    public function test_unshift_should_update_length()
    {
        $collection = new Collection([1]);
        $collection->unshift('lorem', 'ipsum', 'dolor');
        $this->assertEquals(4, $collection->length);
    }

    public function test_shift_should_remove_and_return_first_value()
    {
        $collection = new Collection(['lorem', 'ipsum', 'dolor']);
        $this->assertEquals('lorem', $collection->shift());
        $this->assertEquals(['ipsum', 'dolor'], $collection->get());
    }

    public function test_shift_should_update_length()
    {
        $collection = new Collection(['lorem', 'ipsum', 'dolor']);
        $collection->shift();
        $this->assertEquals(2, $collection->length);
    }

    public function test_pop_should_remove_and_return_last_value()
    {
        $collection = new Collection(['lorem', 'ipsum', 'dolor']);
        $this->assertEquals('dolor', $collection->pop());
        $this->assertEquals(['lorem', 'ipsum'], $collection->get());
    }

    public function test_pop_should_update_length()
    {
        $collection = new Collection(['lorem', 'ipsum', 'dolor']);
        $collection->pop();
        $this->assertEquals(2, $collection->length);
    }

    public function test_get_should_return_associated_value()
    {
        $collection = new Collection(['lorem' => 'ipsum']);
        $this->assertEquals('ipsum', $collection->get('lorem'));
    }

    public function test_get_should_return_collection_when_value_is_array()
    {
        $collection = new Collection(['lorem' => ['ipsum', 'dolor']]);
        $this->assertInstanceOf(Collection::class, $collection->get('lorem'));
    }

    public function test_get_should_accept_more_than_one_argument()
    {
        $collection = new Collection(['lorem' => 'ipsum', 'dolor' => 'sit']);
        $expect = [
            'lorem' => 'ipsum',
            'dolor' => 'sit',
            'amet'  => null,
        ];
        $this->assertEquals($expect, $collection->get('lorem', 'dolor', 'amet')->get());
    }

    public function test_get_should_accept_dot_notation()
    {
        $collection = new Collection([
            'lorem' => [
                'ipsum' => [
                    'dolor' => 'sit'
                ]
            ]
        ]);
        $expect = 'sit';
        $this->assertEquals($expect, $collection->get('lorem.ipsum.dolor'));
    }

    public function test_get_should_return_null_when_key_not_exists()
    {
        $collection = new Collection(['lorem', 'ipsum']);
        $this->assertEquals(null, $collection->get('dolor'));
    }

    public function test_array_sintax_should_associate_key_and_value()
    {
        $collection = new Collection();
        $collection['lorem'] = 'ipsum';
        $this->assertEquals(['lorem' => 'ipsum'], $collection->get());
    }

    public function test_array_sintax_should_update_length()
    {
        $collection = new Collection();
        $collection['lorem'] = 'ipsum';
        $this->assertEquals(1, $collection->length);
    }

    public function test_array_sintax_should_append_a_value()
    {
        $collection = new Collection();
        $collection[] = 'ipsum';
        $this->assertEquals(['ipsum'], $collection->get());
    }

    public function test_array_sintax_should_return_empty_collection_when_key_not_exists()
    {
        $collection = new Collection();
        $this->assertEquals(new Collection(), $collection['ipsum']);
    }

    public function test_interval_notation_should_return_slice()
    {
        $collection = new Collection([0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10]);
        $expect     = new Collection([2, 3, 4, 5, 6, 7]);
        $this->assertEquals($expect, $collection->get('2:7'));
    }

    public function test_interval_notation_with_array_sintax_should_return_slice()
    {
        $collection = new Collection([0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10]);
        $expect     = new Collection([2, 3, 4, 5, 6, 7]);
        $this->assertEquals($expect, $collection['2:7']);
    }

    public function test_reverse_interval_notation_should_return_slice()
    {
        $collection = new Collection([0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10]);
        $expect     = new Collection([7, 6, 5, 4, 3, 2]);
        $this->assertEquals($expect, $collection->get('7:2'));
    }

    public function test_reverse_interval_notation_with_array_sintax_should_return_slice()
    {
        $collection = new Collection([0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10]);
        $expect     = new Collection([7, 6, 5, 4, 3, 2]);
        $this->assertEquals($expect, $collection['7:2']);
    }

    public function test_interval_notation_should_update_length()
    {
        $collection = new Collection([0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10]);
        $this->assertEquals(5, $collection->get('1:5')->length);
    }

    public function test_interval_notation_array_sintax_associative_key_should_return_slice()
    {
        $collection = new Collection([
            'zero'  => 0, 'one'  => 1, 'two' => 2, 'three' => 3,
            'four'  => 4, 'five' => 5, 'six' => 6, 'seven' => 7,
            'eight' => 8, 'nine' => 9, 'ten' => 10
        ]);
        $expect = new Collection(['two'   => 2, 'three' => 3, 'four'  => 4, 'five'  => 5, 'six' => 6]);
        $this->assertEquals($expect, $collection['2:6']);
    }

    public function test_reverse_interval_notation_array_sintax_associative_key_should_return_slice()
    {
        $collection = new Collection([
            'zero'  => 0, 'one'  => 1, 'two' => 2, 'three' => 3,
            'four'  => 4, 'five' => 5, 'six' => 6, 'seven' => 7,
            'eight' => 8, 'nine' => 9, 'ten' => 10
        ]);
        $expect = new Collection(['six' => 6, 'five' => 5, 'four' => 4, 'three' => 3, 'two' => 2]);
        $this->assertEquals($expect, $collection['6:2']);
    }

    public function test_interval_notation_should_fill_object()
    {
        $collection = new Collection();
        $collection['5:8'] = 0;
        $expect = new Collection([5 => 0, 0, 0, 0]);
        $this->assertEquals($expect, $collection);
    }

    public function test_interval_notation_fill_should_update_length()
    {
        $collection = new Collection();
        $collection['5:8'] = 0;
        $this->assertEquals(4, $collection->length);
    }

    public function test_reverse_interval_notation_should_fill_object()
    {
        $collection = new Collection();
        $collection['8:5'] = 0;
        $expect =  new Collection([5 => 0, 0, 0, 0]);
        $this->assertEquals($expect, $collection);
    }

    public function test_isset_with_interval_notation_should_return_false()
    {
        $collection = new Collection([1 => 'a', 'b', 'c', 'd', 'e']);
        $this->assertFalse($collection->isset('1:6'));
    }

    public function test_isset_with_interval_notation_should_return_true()
    {
        $collection = new Collection([1 => 'a', 'b', 'c', 'd', 'e']);
        $this->assertTrue($collection->isset('1:5'));
    }

    public function test_unset_with_interval_notation_should_remove_values()
    {
        $collection = new Collection([0, 1, 2, 3, 4, 5]);
        $collection->unset('2:4');
        $this->assertEquals([0, 1, 5 => 5], $collection->get());
    }

    public function test_unset_with_interval_notation_should_update_length()
    {
        $collection = new Collection([0, 1, 2, 3, 4, 5]);
        $collection->unset('2:4');
        $this->assertEquals(3, $collection->length);
    }

    public function test_array_sintax_should_throws_exception_when_using_wrong_pattern()
    {
        $this->expectException(InvalidArgumentException::class);
        $collection = new Collection();
        $collection['A:B'];
    }

    public function test_append_array_sintax_should_throws_exception_when_using_wrong_pattern()
    {
        $this->expectException(InvalidArgumentException::class);
        $collection = new Collection();
        $collection['A:B'] = 10;
    }

    public function test_isset_with_array_sintax_should_throws_exception_when_using_wrong_pattern()
    {
        $this->expectException(InvalidArgumentException::class);
        $collection = new Collection();
        isset($collection['A:B']);
    }

    public function test_unset_with_array_sintax_should_throws_exception_when_using_wrong_pattern()
    {
        $this->expectException(InvalidArgumentException::class);
        $collection = new Collection();
        unset($collection['A:B']);
    }

    public function test_isset_should_return_true()
    {
        $array = new Collection();
        $array['lorem'] = 'ipsum';
        $this->assertEquals(true, isset($array['lorem']));
        $this->assertEquals(true, $array->isset('lorem'));
    }

    public function test_isset_should_return_false()
    {
        $array = new Collection();
        $array['lorem'] = 'ipsum';
        $this->assertEquals(false, isset($array['ipsum']));
        $this->assertEquals(false, $array->isset('ipsum'));
    }

    public function test_isset_with_dot_notation_should_return_true()
    {
        $array = new Collection();
        $array['lorem.ipsum'] = 'ipsum';
        $this->assertEquals(true, isset($array['lorem.ipsum']));
        $this->assertEquals(true, $array->isset('lorem.ipsum'));
    }

    public function test_isset_with_dot_notation_should_return_false()
    {
        $array = new Collection();
        $array['lorem.ipsum'] = 'ipsum';
        $this->assertEquals(false, isset($array['lorem.dolor']));
        $this->assertEquals(false, $array->isset('lorem.dolor'));
    }

    public function test_noset_should_return_false()
    {
        $array = new Collection();
        $array['lorem'] = 'ipsum';
        $this->assertEquals(false, $array->noset('lorem'));
    }

    public function test_noset_should_return_true()
    {
        $array = new Collection();
        $array['lorem'] = 'ipsum';
        $this->assertEquals(true, $array->noset('ipsum'));
    }

    public function test_noset_with_dot_notation_should_return_true()
    {
        $array = new Collection();
        $array['lorem.ipsum'] = 'ipsum';
        $this->assertEquals(true, $array->noset('lorem.dolor'));
    }

    public function test_noset_with_dot_notation_should_return_false()
    {
        $array = new Collection();
        $array['lorem.ipsum'] = 'ipsum';
        $this->assertEquals(false, $array->noset('lorem.ipsum'));
    }

    public function test_empty_should_return_false()
    {
        $array = new Collection();
        $array['lorem'] = 'ipsum';
        $this->assertEquals(false, $array->empty('lorem'));
    }

    public function test_empty_should_return_true()
    {
        $array = new Collection();
        $array['lorem'] = 'ipsum';
        $this->assertEquals(true, $array->empty('ipsum'));
    }

    public function test_empty_with_dot_notation_should_return_true()
    {
        $array = new Collection();
        $array['lorem.ipsum'] = false;
        $this->assertEquals(true, empty($array['lorem.ipsum']));
        $this->assertEquals(true, $array->empty('lorem.ipsum'));
    }

    public function test_empty_with_dot_notation_should_return_false()
    {
        $array = new Collection();
        $array['lorem.ipsum'] = 'ipsum';
        $this->assertEquals(false, empty($array['lorem.ipsum']));
        $this->assertEquals(false, $array->empty('lorem.ipsum'));
    }

    public function test_filled_should_return_true()
    {
        $array = new Collection();
        $array['lorem'] = 'ipsum';
        $this->assertEquals(true, $array->filled('lorem'));
    }

    public function test_filled_should_return_false()
    {
        $array = new Collection();
        $array['lorem'] = 'ipsum';
        $this->assertEquals(false, $array->filled('ipsum'));
    }

    public function test_filled_with_dot_notation_should_return_true()
    {
        $array = new Collection();
        $array['lorem.ipsum'] = 'lorem';
        $this->assertEquals(true, $array->filled('lorem.ipsum'));
    }

    public function test_filled_with_dot_notation_should_return_false()
    {
        $array = new Collection();
        $array['lorem.ipsum'] = false;
        $this->assertEquals(false, $array->filled('lorem.ipsum'));
    }

    public function test_unset_should_remove_value()
    {
        $array = new Collection(['lorem', 'ipsum']);
        $array->unset('lorem');
        $this->assertEquals(false, $array->isset('lorem'));
    }

    public function test_unset_with_dot_notation_should_remove_value()
    {
        $array = new Collection();
        $array['lorem.ipsum'] = 'sit';
        $array->unset('lorem.ipsum');
        $this->assertEquals(false, $array->isset('lorem.ipsum'));
    }

    public function test_iterator_foreach_should_iterate_object()
    {
        $collection = new Collection(['lorem', 'ipsum', 'dolor', 'sit']);
        foreach ($collection as $key => $value) {
            $this->assertEquals($collection[$key], $value);
        }
    }

    public function test_iterator_foreach_should_return_always_array()
    {
        $collection = new Collection([['lorem' => 'ipsum'], ['dolor' => 'sit']]);
        foreach ($collection as $key => $value) {
            $this->assertInternalType('array', $value);
        }
    }

    public function test_each_should_iterate_object_receiving_only_value()
    {
        $collection = new Collection(['lorem', 'ipsum', 'dolor', 'sit']);
        $collection->each(function ($value) use ($collection) {
            $this->assertInternalType('string', $value);
        });
    }

    public function test_each_should_iterate_object_receiving_key_and_value()
    {
        $collection = new Collection(['lorem', 'ipsum', 'dolor', 'sit']);
        $collection->each(function ($key, $value) use ($collection) {
            $this->assertEquals($collection[$key], $value);
        });
    }

    public function test_each_should_return_array_when_second_parameter_is_false()
    {
        $collection = new Collection([['lorem' => 'ipsum'], ['dolor' => 'sit']]);
        $collection->each(function ($value) {
            $this->assertInternalType('array', $value);
        });
    }

    public function test_each_should_return_collection_when_second_parameter_is_true()
    {
        $collection = new Collection([['lorem' => 'ipsum'], ['dolor' => 'sit']]);
        $collection->each(function ($value) {
            $this->assertInstanceOf(Collection::class, $value);
        }, true);
    }

    public function test_for_should_iterate_object_with_steps_receiving_only_value()
    {
        $collection = new Collection(['lorem', 'ipsum', 'dolor', 'sit']);
        $collection->for(0, 2, function ($value) use ($collection) {
            $this->assertInternalType('string', $value);
        });
    }

    public function test_for_should_iterate_object_with_steps_receiving_key_and_value()
    {
        $collection = new Collection(['lorem', 'ipsum', 'dolor', 'sit']);
        $collection->for(0, 2, function ($key, $value) use ($collection) {
            $this->assertEquals($collection[$key], $value);
        });
    }

    public function test_for_should_return_array_when_second_parameter_is_false()
    {
        $collection = new Collection([['lorem' => 'ipsum'], ['dolor' => 'sit']]);
        $collection->for(0, 2, function ($value) {
            $this->assertInternalType('array', $value);
        });
    }

    public function test_for_should_return_collection_when_second_parameter_is_true()
    {
        $collection = new Collection([['lorem' => 'ipsum'], ['dolor' => 'sit']]);
        $collection->for(0, 2, function ($value) {
            $this->assertInstanceOf(Collection::class, $value);
        }, true);
    }

    public function test_for_should_iterate_object_with_backward_when_step_is_negative()
    {
        $collection = new Collection(['lorem', 'ipsum', 'dolor', 'sit']);
        $collection->for(3, -1, function ($key, $value) use ($collection) {
            $this->assertEquals($collection[$key], $value);
        });
    }

    public function test_map_should_update_only_values()
    {
        $collection = new Collection(['lorem' => 'ipsum', 'dolor' => 'sit']);
        $map = $collection->map(function ($value) {
            return strtoupper($value);
        });
        $this->assertEquals(['lorem' => 'IPSUM', 'dolor' => 'SIT'], $map->get());
    }

    public function test_map_should_update_values_and_keys()
    {
        $collection = new Collection(['lorem' => 'ipsum', 'dolor' => 'sit']);
        $map = $collection->map(function ($key, $value) {
            return [strtoupper($key) => strtoupper($value)];
        });
        $this->assertEquals(['LOREM' => 'IPSUM', 'DOLOR' => 'SIT'], $map->get());
    }

    public function test_map_should_return_array_when_second_parameter_is_false()
    {
        $collection = new Collection([['lorem' => 'ipsum'], ['dolor' => 'sit']]);
        $collection->map(function ($key, $value) {
            $this->assertInternalType('array', $value);
            return [$key => $value];
        });
    }

    public function test_map_should_return_collection_when_second_parameter_is_true()
    {
        $collection = new Collection([['lorem' => 'ipsum'], ['dolor' => 'sit']]);
        $collection->map(function ($key, $value) {
            $this->assertInstanceOf(Collection::class, $value);
            return [$key => $value];
        }, true);
    }

    public function test_filter_by_value_should_remove_values_when_return_false()
    {
        $collection = new Collection([1, 2, 3, 4, 5, 6, 7, 8, 9]);
        $filter = $collection->filter(function ($value) {
            return $value < 5;
        });
        $expect = [1, 2, 3, 4];
        $this->assertEquals($expect, $filter->get());
    }

    public function test_filter_by_key_should_remove_values_when_return_false()
    {
        $collection = new Collection([1, 2, 3, 4, 5, 6, 7, 8, 9]);
        $filter = $collection->filter(function ($key, $value) {
            return $key > 5;
        });
        $expect = [7, 8, 9];
        $this->assertEquals($expect, $filter->values()->get());
    }

    public function test_filter_should_return_array_when_second_parameter_is_false()
    {
        $collection = new Collection([['lorem' => 'ipsum'], ['dolor' => 'sit']]);
        $collection->filter(function ($key, $value) {
            return $this->assertInternalType('array', $value);
        });
    }

    public function test_filter_should_return_collection_when_second_parameter_is_true()
    {
        $collection = new Collection([['lorem' => 'ipsum'], ['dolor' => 'sit']]);
        $collection->filter(function ($key, $value) {
            return $this->assertInstanceOf(Collection::class, $value);
        }, true);
    }

    public function test_reduce_should_sum_all_values()
    {
        $collection = new Collection([1, 2, 3, 4]);
        $result = $collection->reduce(function ($a, $b) {
            return $a + $b;
        });
        $this->assertEquals($expect = 10, $result);
    }

    public function test_reduce_should_return_array_when_second_parameter_is_false()
    {
        $collection = new Collection([['lorem' => 'ipsum'], ['dolor' => 'sit']]);
        $result = $collection->reduce(function ($a, $b) {
            $this->assertInternalType('array', $a);
            $this->assertInternalType('array', $b);
        });
    }

    public function test_reduce_should_return_collection_when_second_parameter_is_true()
    {
        $collection = new Collection([['lorem' => 'ipsum'], ['dolor' => 'sit']]);
        $result = $collection->reduce(function ($a, $b) {
            $this->assertInstanceOf(Collection::class, $a);
            $this->assertInstanceOf(Collection::class, $b);
        }, true);
    }

    public function test_find_should_return_element_when_callable_return_true()
    {
        $collection = new Collection([
            ['lorem' => 'ipsum', 'ipsum' => 'consectetur'],
            ['lorem' => 'sit',   'ipsum' => 'lorem'],
            ['lorem' => 'amet',  'ipsum' => 'sit']
        ]);
        $element = $collection->find(function ($key, $value) {
            return $value->get('ipsum') === 'lorem';
        }, true);
        $expect = ['lorem' => 'sit',   'ipsum' => 'lorem'];
        $this->assertEquals($expect, $element->get());
    }

    public function test_find_should_return_false_when_element_not_found()
    {
        $collection = new Collection([
            ['lorem' => 'ipsum', 'ipsum' => 'consectetur'],
            ['lorem' => 'sit',   'ipsum' => 'lorem'],
            ['lorem' => 'amet',  'ipsum' => 'sit']
        ]);
        $element = $collection->find(function ($key, $value) {
            return $value->get('ipsum') === 'dolor';
        }, true);
        $this->assertFalse($element);
    }

    public function test_find_should_return_array_when_second_parameter_is_false()
    {
        $collection = new Collection([['lorem' => 'ipsum'], ['dolor' => 'sit']]);
        $collection->find(function ($key, $value) {
            return $this->assertInternalType('array', $value);
        });
    }

    public function test_find_should_return_collection_when_second_parameter_is_true()
    {
        $collection = new Collection([['lorem' => 'ipsum'], ['dolor' => 'sit']]);
        $collection->find(function ($key, $value) {
            return $this->assertInstanceOf(Collection::class, $value);
        }, true);
    }
    
    public function test_chunk_should_divide_object_into_parts()
    {
        $collection = new Collection([1, 2, 3, 4, 5]);
        $expect     = new Collection([0 => [1, 2], 1 => [3, 4], 2 => [5]]);
        $this->assertEquals($expect, $collection->chunk(2));
    }

    public function test_keys_should_return_collection_of_all_keys()
    {
        $collection = new Collection(['three' => 3, 'eight' => 8, 'two' => 2]);
        $expect     = new Collection(['three', 'eight', 'two']);
        $this->assertEquals($expect, $collection->keys());
    }

    public function test_values_should_return_collection_of_all_values()
    {
        $collection = new Collection(['three' => 3, 'eight' => 8, 'two' => 2]);
        $expect     = new Collection([3, 8, 2]);
        $this->assertEquals($expect, $collection->values());
    }

    public function test_join_should_transform_object_content_into_string()
    {
        $collection = new Collection([1, 2, 3, 4, 5, 6]);
        $this->assertEquals('1|2|3|4|5|6', $collection->join('|'));
    }

    public function test_column_should_return_collection_of_a_given_column()
    {
        $collection = new Collection([
            'lorem1' => [
                'ipsum' => 'dolor1',
                'sit'   => 'amet1'
            ],
            'lorem2' => [
                'ipsum' => 'dolor2',
                'sit'   => 'amet2'
            ]
        ]);
        $expect = new Collection(['dolor1', 'dolor2']);
        $this->assertEquals($expect, $collection->column('ipsum'));
    }

    public function test_combine_should_use_first_argument_for_keys_and_second_for_values()
    {
        $collection = new Collection();
        $collection['KEYS']   = new Collection(['lorem', 'ipsum']);
        $collection['VALUES'] = new Collection(['dolor', 'amet']);
        $collection = Collection::combine($collection['KEYS'], $collection['VALUES']);
        $expect     = new Collection(['lorem' => 'dolor', 'ipsum'=> 'amet']);
        $this->assertEquals($expect, $collection);
    }

    public function test_count_should_return_length_of_object()
    {
        $collection    = new Collection([1, 2, 3, 4, 5]);
        $collection[2] = [1, 2, 3];

        $this->assertEquals(5, $collection->count());
        $this->assertEquals(3, $collection[2]->count());
    }

    public function test_recursive_count_should_return_length_of_all_dimensions()
    {
        $collection = new Collection([1, [2, 3], [2 => [1, 2, 3]]]);
        $this->assertEquals(9, $collection->count(COUNT_RECURSIVE));
    }

    public function test_recursive_count_should_not_affect_length()
    {
        $collection = new Collection([1, [2, 3], [2 => [1, 2, 3]]]);
        $collection->count(COUNT_RECURSIVE);
        $this->assertEquals(3, $collection->length);
    }

    public function test_first_should_return_the_first_element()
    {
        $collection = new Collection(['lorem' => 'ipsum', 'dolor' => 'sit', 'amet' => 'consectetur']);
        $this->assertEquals('ipsum', $collection->first());
    }

    public function test_last_should_return_the_last_element()
    {
        $collection = new Collection(['lorem' => 'ipsum', 'dolor' => 'sit', 'amet' => 'consectetur']);
        $this->assertEquals('consectetur', $collection->last());
    }

    public function test_lower_should_change_all_keys_to_lower_case()
    {
        $collection = new Collection(['Lorem' => ['Ipsum' => 'consectetur'], 'Dolor' => ['Amet' => 'elit']]);
        $this->assertEquals(['lorem' => ['ipsum' => 'consectetur'], 'dolor' => ['amet' => 'elit']], $collection->lower()->get());
    }

    public function test_upper_should_change_all_keys_to_upper_case()
    {
        $collection = new Collection(['Lorem' => ['Ipsum' => 'consectetur'], 'Dolor' => ['Amet' => 'elit']]);
        $this->assertEquals(['LOREM' => ['IPSUM' => 'consectetur'], 'DOLOR' => ['AMET' => 'elit']], $collection->upper()->get());
    }

    public function test_toString_should_transform_into_json()
    {
        $collection = new Collection(['lorem' => 1, 'Ipsum' => 2]);
        $expect = '{"lorem":1,"Ipsum":2}';
        $this->assertEquals($expect, $collection);
    }

    public function test_sort_should_order_content()
    {
        $collection = new Collection(['three' => 3, 'eight' => 8, 'two' => 2]);
        $expect = [2, 3, 8];
        $this->assertSame($expect, $collection->sort()->get());
    }

    public function test_rsort_should_order_content()
    {
        $collection = new Collection(['three' => 3, 'eight' => 8, 'two' => 2]);
        $expect = [8, 3, 2];
        $this->assertSame($expect, $collection->rsort()->get());
    }

    public function test_asort_should_order_content()
    {
        $collection = new Collection(['three' => 3, 'eight' => 8, 'two' => 2]);
        $expect = ['two' => 2, 'three' => 3, 'eight' => 8];
        $this->assertSame($expect, $collection->asort()->get());
    }

    public function test_arsort_should_order_content()
    {
        $collection = new Collection(['three' => 3, 'eight' => 8, 'two' => 2]);
        $expect = ['eight' => 8, 'three' => 3, 'two' => 2];
        $this->assertSame($expect, $collection->arsort()->get());
    }

    public function test_ksort_should_order_content()
    {
        $collection = new Collection(['three' => 3, 'eight' => 8, 'two' => 2]);
        $expect = ['eight' => 8, 'three' => 3, 'two' => 2];
        $this->assertSame($expect, $collection->ksort()->get());
    }

    public function test_krsort_should_order_content()
    {
        $collection = new Collection(['three' => 3, 'eight' => 8, 'two' => 2]);
        $expect = ['two' => 2, 'three' => 3, 'eight' => 8];
        $this->assertSame($expect, $collection->krsort()->get());
    }

    public function test_usort_should_order_content()
    {
        $collection = new Collection(['three' => 3, 'eight' => 8, 'two' => 2]);
        $expect = ['two' => 2, 'three' => 3, 'eight' => 8];
        $this->assertSame($expect, $collection->krsort(function ($a, $b) {
            return $a <=> $b;
        })->get());
    }

    public function test_unique_should_remove_duplicated_values()
    {
        $collection = new Collection([1, 2, 3, 1, 2, 3, 1, 2, 3, 1, 2, 3]);
        $expect     = new Collection([1, 2, 3]);
        $this->assertEquals($expect, $collection->unique());
    }

    public function test_merge_should_merge_all_dimensions()
    {
        $collection = new Collection([[1, 2], [2, 4], [4, 5, 13], [2, 3, 9]]);
        $expect     = new Collection([1, 2, 2, 4, 4, 5, 13, 2, 3, 9]);

        $this->assertEquals($expect, $collection->merge());
    }

    public function test_reverse_should_return_the_inverse_of_object()
    {
        $collection = new Collection([1, 2, 3, 4, 5, 6, 7, 8, 9]);
        $expect     = new Collection([9, 8, 7, 6, 5, 4, 3, 2, 1]);
        $this->assertEquals($expect, $collection->reverse());
    }

    public function test_search_should_return_position_of_ocurrence()
    {
        $collection = new Collection(['a', 'b', 'c', 'd', 'e']);
        $this->assertEquals(3, $collection->search('d'));
    }

    public function test_search_should_return_false_when_not_exists()
    {
        $collection = new Collection(['a', 'b', 'c', 'd', 'e']);
        $this->assertEquals(false, $collection->search('f'));
    }

    public function test_union_should_make_union_of_sets()
    {
        $collection = new Collection([[1, 2], [2, 4], [4, 5, 13], [2, 3, 9]]);
        $expect     = new Collection([1, 2, 4, 5, 13, 3, 9]);
        $this->assertEquals($expect, $collection->union()->values());
    }

    public function test_diff_should_make_difference_of_sets()
    {
        $collection = new Collection([[1, 2, 3, 4, 5, 6], [2, 4], [6, 1]]);
        $expect     = new Collection([3, 5]);
        $this->assertEquals($expect, $collection->diff()->values());
    }

    public function test_intersect_should_make_intersection_of_sets()
    {
        $collection = new Collection([[1, 2, 3, 4, 5, 6], [2, 3, 5, 6], [2, 3, 7, 9]]);
        $expect     = new Collection([2, 3]);
        $this->assertEquals($expect, $collection->intersect()->values());
    }

    public function test_cartesian_should_make_product_of_sets()
    {
        $collection = new Collection([[1, 2], [3, 4], [3, 5]]);
        $expect     = new Collection([[1, 3, 3], [1, 3, 5], [1, 4, 3], [1, 4, 5], [2, 3, 3], [2, 3, 5], [2, 4, 3], [2, 4, 5]]);
        $this->assertEquals($expect, $collection->cartesian());
    }

    public function test_flip_should_put_keys_as_values_and_values_as_keys()
    {
        $collection = new Collection(['lorem' => 1, 'ipsum' => 2, 'dolor' => 3]);
        $expect     = new Collection([1 => 'lorem', 2 => 'ipsum', 3 => 'dolor']);
        $this->assertEquals($expect, $collection->flip());
    }

    public function test_sum_should_return_total()
    {
        $collection = new Collection([1, 2, 3, 4, 5, 6, 7, 8, 9]);
        $this->assertEquals(45, $collection->sum());
    }

    public function test_contains_should_return_true()
    {
        $collection = new Collection([1, 2, 3, 4, 5, 6, 7, 8, 9]);
        $this->assertTrue($collection->contains(7));
    }

    public function test_contains_should_return_false()
    {
        $collection = new Collection([1, 2, 3, 4, 5, 6, 7, 8, 9]);
        $this->assertFalse($collection->contains(10));
    }

    public function test_array_access_as_property()
    {
        $collection = new Collection();
        $collection['lorem'] = 'dolor';
        $this->assertEquals('dolor', $collection->lorem);
    }

    public function test_array_set_value_as_property()
    {
        $collection = new Collection();
        $collection->lorem = 'dolor';
        $this->assertEquals(['lorem' => 'dolor'], $collection->get());
    }

    public function test_array_check_isset_value_as_property()
    {
        $collection = new Collection();
        $collection['lorem'] = 'dolor';
        $this->assertTrue(isset($collection->lorem));
        $this->assertFalse(isset($collection->ipsum));
    }

    public function test_array_unset_value_as_property()
    {
        $collection = new Collection();
        $collection['lorem'] = 'dolor';
        unset($collection->lorem);
        $this->assertEquals([], $collection->get());
    }

    public function test_override_readonly_property_should_throw_exception()
    {
        $this->expectException(InvalidArgumentException::class);
        $collection = new Collection();
        $collection->length = 10;
    }

    public function test_coalesce_should_return_first_non_null_value()
    {
        $collection = new Collection([null, null, null, 'lorem', null]);
        $this->assertEquals('lorem', $collection->coalesce());
    }

    public function test_coalesce_should_return_null_when_all_values_are_null()
    {
        $collection = new Collection([null, null, null, null, null]);
        $this->assertEquals(null, $collection->coalesce());
    }

    public function test_outer_should_return_full_diff()
    {
        $collection = new Collection([[1, 2, 3, 4, 5], [2, 5, 7, 9]]);
        $expect     = new Collection([[1, 3, 4], [7, 9]]);
        $this->assertEquals($expect, $collection->outer());
    }

    public function test_range_should_return_number_list()
    {
        $collection = Collection::range(1, 10, 2);
        $this->assertEquals([1, 3, 5, 7, 9], $collection->get());
    }

    public function test_range_should_return_alphabet_interval()
    {
        $collection = Collection::range('A', 'F');
        $this->assertEquals(['A', 'B', 'C', 'D', 'E', 'F'], $collection->get());
    }

    public function test_random_should_return_random_element()
    {
        $collection = Collection::range('A', 'F');
        $this->assertContains($collection->random(), ['A', 'B', 'C', 'D', 'E', 'F']);
    }

    public function test_walk_should_iterate_all_elements()
    {
        $collection = new Collection([1, [2, 3, 4], [[5, 6, 7], [8, 9]], [10, 11, 12], 13]);
        $i = 1;
        $collection->walk(function ($key, $value) use (&$i) {
            $this->assertEquals($i++, $value);
        });
    }

    public function test_walk_should_return_array_when_second_parameter_is_false()
    {
        $collection = new Collection([['lorem']]);
        $i = 1;
        $collection->walk(function ($key, $value) use (&$i) {
            if (is_string($value)) {
                return;
            }
            $this->assertInternalType('array', $value);
        }, \RecursiveIteratorIterator::SELF_FIRST);
    }

    public function test_walk_should_return_collection_when_third_parameter_is_true()
    {
        $collection = new Collection([['lorem']]);
        $i = 1;
        $collection->walk(function ($key, $value) use (&$i) {
            if (is_string($value)) {
                return;
            }
            $this->assertInstanceOf(Collection::class, $value);
        }, \RecursiveIteratorIterator::SELF_FIRST, true);
    }

    public function test_shuffle_should_mix_elements()
    {
        $range = Collection::range('A', 'C');

        $collection = new Collection();
        $collection['0:2'] = $range;

        $cartesian = $collection->cartesian();
        $shuffle   = $range->shuffle();

        $this->assertTrue($cartesian->contains($shuffle));
    }

    public function test_is_collection_should_return_true()
    {
        $this->assertTrue(Collection::isCollection(new Collection()));
    }

    public function test_is_collection_should_return_false()
    {
        $this->assertFalse(Collection::isCollection([]));
    }
}
