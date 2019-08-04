<?php

use PHPUnit\Framework\TestCase;
use Cajudev\Collection;

class CollectionTest extends TestCase
{
    public function test_construct_from_array()
    {
        $regularArray = ['lorem', 'ipsum', 'dolor', 'sit', 'amet,', 'consectetur', 'adipiscing', 'elit'];
        $collection = new Collection($regularArray);
        self::assertEquals($regularArray, $collection->get());
    }

    public function test_construct_from_object_should_parse_attributes()
    {
        $object = new class {
            private   $private = 'lorem';
            public    $public = 'ipsum';
            protected $protected = 'dolor';
        };
        $collection = new Collection($object);
        $expect     = new Collection(['private' => 'lorem', 'public' => 'ipsum', 'protected' => 'dolor']);
        self::assertEquals($expect, $collection);
    }

    public function test_construct_from_another_collection_object_should_extract_data()
    {
        $collection1 = new Collection([1, 2, 3]);
        $collection2 = new Collection($collection1);
        self::assertSame([1, 2, 3], $collection2->get());
    }

    public function test_construct_from_another_type_should_throws_exception()
    {
        self::expectException(\InvalidArgumentException::class);
        $collection = new Collection('lorem');
    }

    public function test_set_with_value_and_key()
    {
        $collection = new Collection();
        $collection->set('lorem', 'ipsum');
        self::assertSame(['lorem' => 'ipsum'], $collection->get());
    }

    public function test_set_another_collection_should_insert_array()
    {
        $collection = new Collection();
        $collection->set('lorem', new Collection([1, 2, 3]));
        $array = $collection->get();
        self::assertEquals([1, 2, 3], $array['lorem']);
    }

    public function test_set_using_dot_notation()
    {
        $collection = new Collection();
        $collection->set('lorem.ipsum', 'dolor');
        self::assertSame(['lorem' => ['ipsum' => 'dolor']], $collection->get());
    }

    public function test_length_after_set() {
        $collection = new Collection();
        $collection->set('lorem', 'ipsum');
        self::assertSame(1, $collection->length);
    }

    public function test_push_one_value()
    {
        $collection = new Collection([1]);
        $collection->push('lorem');
        self::assertEquals([1, 'lorem'], $collection->get());
    }

    public function test_push_more_than_one_value()
    {
        $collection = new Collection([1]);
        $collection->push('lorem', 'ipsum', 'dolor');
        self::assertEquals([1, 'lorem', 'ipsum', 'dolor'], $collection->get());
    }

    public function test_length_after_push() {
        $collection = new Collection([1]);
        $collection->push('lorem', 'ipsum', 'dolor');
        self::assertEquals(4, $collection->length);
    }

    public function test_unshift_one_value()
    {
        $collection = new Collection([1]);
        $collection->unshift('lorem');
        self::assertEquals(['lorem', 1], $collection->get());
    }

    public function test_unshift_more_than_one_value()
    {
        $collection = new Collection([1]);
        $collection->unshift('lorem', 'ipsum', 'dolor');
        self::assertEquals(['lorem', 'ipsum', 'dolor', 1], $collection->get());
    }

    public function test_length_after_unshift() {
        $collection = new Collection([1]);
        $collection->unshift('lorem', 'ipsum', 'dolor');
        self::assertEquals(4, $collection->length);
    }

    public function test_shift_value_from_array()
    {
        $collection = new Collection(['lorem', 'ipsum', 'dolor']);
        self::assertEquals('lorem', $collection->shift());
        self::assertEquals(['ipsum', 'dolor'], $collection->get());
    }

    public function test_length_after_shift() {
        $collection = new Collection(['lorem', 'ipsum', 'dolor']);
        $collection->shift();
        self::assertEquals(2, $collection->length);
    }

    public function test_pop_value_from_array()
    {
        $collection = new Collection(['lorem', 'ipsum', 'dolor']);
        self::assertEquals('dolor', $collection->pop());
        self::assertEquals(['lorem', 'ipsum'], $collection->get());
    }

    public function test_length_after_pop() {
        $collection = new Collection(['lorem', 'ipsum', 'dolor']);
        $collection->pop();
        self::assertEquals(2, $collection->length);
    }

    public function test_get_a_value_from_array()
    {
        $collection = new Collection(['lorem' => 'ipsum']);
        self::assertEquals('ipsum', $collection->get('lorem'));
    }

    public function test_get_should_return_instance_when_value_is_array()
    {
        $collection = new Collection(['lorem' => ['ipsum', 'dolor']]);
        self::assertInstanceOf(Collection::class, $collection->get('lorem'));
    }

    public function test_get_values_using_chained_get()
    {
        $collection = new Collection(['lorem' => ['ipsum' => 'dolor'], 'sit' => 'amet']);
        $expect = 'dolor';
        self::assertEquals($expect, $collection->get('lorem')->get('ipsum'));
    }

    public function test_get_several_values()
    {
        $collection = new Collection(['lorem' => 'ipsum', 'dolor' => 'sit']);
        $expect = [
            'lorem' => 'ipsum',
            'dolor' => 'sit',
            'amet'  => null,
        ];
        self::assertEquals($expect, $collection->get('lorem', 'dolor', 'amet')->get());
    }

    public function test_get_values_using_dot_notation()
    {
        $collection = new Collection([
            'lorem' => [
                'ipsum' => [
                    'dolor' => 'sit'
                ]
            ]
        ]);
        $expect = 'sit';
        self::assertEquals($expect, $collection->get('lorem.ipsum.dolor'));
    }

    public function test_get_inexistent_value_should_return_null()
    {
        $collection = new Collection(['lorem', 'ipsum']);
        self::assertEquals(null, $collection->get('dolor'));
    }

    public function test_set_a_value_using_array_sintax()
    {
        $collection = new Collection();
        $collection['lorem'] = 'ipsum';
        self::assertEquals(['lorem' => 'ipsum'], $collection->get());
    }

    public function test_length_after_set_value_with_array_sintax() {
        $collection = new Collection();
        $collection['lorem'] = 'ipsum';
        self::assertEquals(1, $collection->length);
    }

    public function test_append_a_value_using_array_sintax()
    {
        $collection = new Collection();
        $collection[] = 'ipsum';
        self::assertEquals(['ipsum'], $collection->get());
    }

    public function test_length_after_append_with_array_sintax() {
        $collection = new Collection();
        $collection[] = 'ipsum';
        self::assertEquals(1, $collection->length);
    }

    public function test_accessing_invalid_keys_should_return_new_instance_with_empty_content()
    {
        $collection = new Collection();
        self::assertInstanceOf(Collection::class, $collection['ipsum']);
    }

    public function test_set_value_with_dot_notation() {
        $collection = new Collection();
        $collection['lorem.ipsum.dolor'] = 'amet';
        $expect['lorem']['ipsum']['dolor'] = 'amet';
        self::assertEquals($expect, $collection->get());
    }

    public function test_get_value_with_dot_notation() {
        $collection = new Collection();
        $collection['lorem']['ipsum']['dolor'] = 'amet';
        self::assertEquals('amet', $collection->get('lorem.ipsum.dolor'));
    }

    public function test_get_multiple_value_with_dot_notation() {
        $collection = new Collection();
        $collection['lorem1']['ipsum1'] = 'dolor1';
        $collection['lorem2']['ipsum2'] = 'dolor2';
        $collection['lorem3']['ipsum3'] = 'dolor3';
        $collection['lorem4']['ipsum4'] = 'dolor4';

        $expect = new Collection();
        $expect['lorem1']['ipsum1'] = 'dolor1';
        $expect['lorem4']['ipsum4'] = 'dolor4';
        
        self::assertEquals($expect, $collection->get('lorem1.ipsum1', 'lorem4.ipsum4'));
    }

    public function test_interval_notation_index_key()
    {
        $collection = new Collection([0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10]);
        $expect     = new Collection([2, 3, 4, 5, 6, 7]);
        self::assertEquals($expect, $collection->get('2:7'));
    }

    public function test_interval_notation_array_sintax_index_key()
    {
        $collection = new Collection([0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10]);
        $expect     = new Collection([2, 3, 4, 5, 6, 7]);
        self::assertEquals($expect, $collection['2:7']);
    }

    public function test_interval_notation_reverse_index_key()
    {
        $collection = new Collection([0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10]);
        $expect     = new Collection([7, 6, 5, 4, 3, 2]);
        self::assertEquals($expect, $collection->get('7:2'));
    }

    public function test_interval_notation_array_sintax_reverse_index_key()
    {
        $collection = new Collection([0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10]);
        $expect     = new Collection([7, 6, 5, 4, 3, 2]);
        self::assertEquals($expect, $collection['7:2']);
    }

    public function test_length_after_interval_notation()
    {
        $collection = new Collection([0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10]);
        self::assertEquals(5, $collection->get('1:5')->length);
    }

    public function test_interval_notation_array_sintax_associative_key()
    {
        $collection = new Collection([
            'zero'  => 0, 'one'  => 1, 'two' => 2, 'three' => 3,
            'four'  => 4, 'five' => 5, 'six' => 6, 'seven' => 7,
            'eight' => 8, 'nine' => 9, 'ten' => 10
        ]);
        $expect = new Collection(['two'   => 2, 'three' => 3, 'four'  => 4, 'five'  => 5, 'six' => 6]);
        self::assertEquals($expect, $collection['2:6']);
    }

    public function test_interval_notation_array_sintax_reverse_associative_key()
    {
        $collection = new Collection([
            'zero'  => 0, 'one'  => 1, 'two' => 2, 'three' => 3,
            'four'  => 4, 'five' => 5, 'six' => 6, 'seven' => 7,
            'eight' => 8, 'nine' => 9, 'ten' => 10
        ]);
        $expect = new Collection(['six' => 6, 'five' => 5, 'four' => 4, 'three' => 3, 'two' => 2]);
        self::assertEquals($expect, $collection['6:2']);
    }

    public function test_interval_notation_set_values()
    {
        $collection = new Collection();
        $collection['5:8'] = 0;
        $expect = new Collection([5 => 0, 0, 0, 0]);
        self::assertEquals($expect, $collection);
    }

    public function test_length_after_interval_notation_set_values()
    {
        $collection = new Collection();
        $collection['5:8'] = 0;
        self::assertEquals(4, $collection->length);
    }

    public function test_interval_notation_set_values_reverse()
    {
        $collection = new Collection();
        $collection['8:5'] = 0;
        $expect =  new Collection([5 => 0, 0, 0, 0]);
        self::assertEquals($expect, $collection);
    }

    public function test_interval_notation_isset_should_return_false()
    {
        $collection = new Collection([1 => 'a', 'b', 'c', 'd', 'e']);
        self::assertFalse($collection->isset('1:6'));
    }

    public function test_interval_notation_isset_should_return_true()
    {
        $collection = new Collection([1 => 'a', 'b', 'c', 'd', 'e']);
        self::assertTrue($collection->isset('1:5'));
    }

    public function test_interval_notation_unset_values()
    {
        $collection = new Collection([0, 1, 2, 3, 4, 5]);
        $collection->unset('2:4');
        self::assertEquals([0, 1, 5 => 5], $collection->get());
    }

    public function test_length_after_unset_with_interval_notation() {
        $collection = new Collection([0, 1, 2, 3, 4, 5]);
        $collection->unset('2:4');
        self::assertEquals(3, $collection->length);
    }

    public function test_trying_access_using_wrong_pattern()
    {
        self::expectException(InvalidArgumentException::class);
        $collection = new Collection();
        $collection['A:B'];
    }

    public function test_trying_set_using_wrong_pattern()
    {
        self::expectException(InvalidArgumentException::class);
        $collection = new Collection();
        $collection['A:B'] = 10;
    }

    public function test_trying_isset_using_wrong_pattern()
    {
        self::expectException(InvalidArgumentException::class);
        $collection = new Collection();
        isset($collection['A:B']);
    }

    public function test_trying_unset_using_wrong_pattern()
    {
        self::expectException(InvalidArgumentException::class);
        $collection = new Collection();
        unset($collection['A:B']);
    }

    public function test_isset_should_return_true()
    {
        $array = new Collection();
        $array['lorem'] = 'ipsum';
        self::assertEquals(true, isset($array['lorem']));
        self::assertEquals(true, $array->isset('lorem'));
    }

    public function test_isset_should_return_false()
    {
        $array = new Collection();
        $array['lorem'] = 'ipsum';
        self::assertEquals(false, isset($array['ipsum']));
        self::assertEquals(false, $array->isset('ipsum'));
    }

    public function test_isset_with_dot_notation_should_return_true()
    {
        $array = new Collection();
        $array['lorem.ipsum'] = 'ipsum';
        self::assertEquals(true, isset($array['lorem.ipsum']));
        self::assertEquals(true, $array->isset('lorem.ipsum'));
    }

    public function test_isset_with_dot_notation_should_return_false()
    {
        $array = new Collection();
        $array['lorem.ipsum'] = 'ipsum';
        self::assertEquals(false, isset($array['lorem.dolor']));
        self::assertEquals(false, $array->isset('lorem.dolor'));
    }

    public function test_noset_should_return_false()
    {
        $array = new Collection();
        $array['lorem'] = 'ipsum';
        self::assertEquals(false, $array->noset('lorem'));
    }

    public function test_noset_should_return_true()
    {
        $array = new Collection();
        $array['lorem'] = 'ipsum';
        self::assertEquals(true, $array->noset('ipsum'));
    }

    public function test_noset_with_dot_notation_should_return_true()
    {
        $array = new Collection();
        $array['lorem.ipsum'] = 'ipsum';
        self::assertEquals(true, $array->noset('lorem.dolor'));
    }

    public function test_noset_with_dot_notation_should_return_false()
    {
        $array = new Collection();
        $array['lorem.ipsum'] = 'ipsum';
        self::assertEquals(false, $array->noset('lorem.ipsum'));
    }

    public function test_empty_should_return_false()
    {
        $array = new Collection();
        $array['lorem'] = 'ipsum';
        self::assertEquals(false, $array->empty('lorem'));
    }

    public function test_empty_should_return_true()
    {
        $array = new Collection();
        $array['lorem'] = 'ipsum';
        self::assertEquals(true, $array->empty('ipsum'));
    }

    public function test_empty_with_dot_notation_should_return_true()
    {
        $array = new Collection();
        $array['lorem.ipsum'] = false;
        self::assertEquals(true, empty($array['lorem.ipsum']));
        self::assertEquals(true, $array->empty('lorem.ipsum'));
    }

    public function test_empty_with_dot_notation_should_return_false()
    {
        $array = new Collection();
        $array['lorem.ipsum'] = 'ipsum';
        self::assertEquals(false, empty($array['lorem.ipsum']));
        self::assertEquals(false, $array->empty('lorem.ipsum'));
    }

    public function test_filled_should_return_true()
    {
        $array = new Collection();
        $array['lorem'] = 'ipsum';
        self::assertEquals(true, $array->filled('lorem'));
    }

    public function test_filled_should_return_false()
    {
        $array = new Collection();
        $array['lorem'] = 'ipsum';
        self::assertEquals(false, $array->filled('ipsum'));
    }

    public function test_filled_with_dot_notation_should_return_true()
    {
        $array = new Collection();
        $array['lorem.ipsum'] = 'lorem';
        self::assertEquals(true, $array->filled('lorem.ipsum'));
    }

    public function test_filled_with_dot_notation_should_return_false()
    {
        $array = new Collection();
        $array['lorem.ipsum'] = false;
        self::assertEquals(false, $array->filled('lorem.ipsum'));
    }

    public function test_unset_values()
    {
        $array = new Collection(['lorem', 'ipsum']);
        $array->unset('lorem');
        self::assertEquals(false, $array->isset('lorem'));
    }

    public function test_unset_key_using_dot_notation()
    {
        $array = new Collection();
        $array['lorem.ipsum'] = 'sit';
        $array->unset('lorem.ipsum');
        self::assertEquals(false, $array->isset('lorem.ipsum'));
    }

    public function test_unset_key_using_dot_notation_key_non_existent()
    {
        $array = new Collection();
        $array['lorem.ipsum.dolor'] = 'amet';

        $array->unset('lorem.dolor.ipsum');
        self::assertEquals(false, $array->isset('lorem.dolor.ipsum'));
    }

    public function test_iterating_array_foreach_should_return_collections()
    {
        $collection = new Collection([['lorem' => 'ipsum'], ['dolor' => 'sit']]);
        foreach ($collection as $key => $value) {
            self::assertInstanceOf(Collection::class, $value);
        }
    }

    public function test_iterating_array_with_method_each()
    {
        $collection = new Collection([['lorem' => 'ipsum'], ['dolor' => 'sit']]);
        $collection->each(function($key, $value) {
            self::assertTrue(is_array($value));
        });
    }

    public function test_iterating_using_method_for_forward()
    {
        $collection = new Collection(['lorem', 'ipsum', 'dolor', 'sit']);
        $collection->for(0, 2, function($key, $value) use ($collection) {
            self::assertEquals($collection[$key], $value);
        });
    }

    public function test_iterating_using_method_for_backward()
    {
        $collection = new Collection(['lorem', 'ipsum', 'dolor', 'sit']);
        $collection->for(3, -1, function($key, $value) use ($collection) {
            self::assertEquals($collection[$key], $value);
        });
    }

    public function test_map_keys()
    {
        $collection = new Collection(['lorem' => 'ipsum', 'dolor' => 'sit']);
        $map = $collection->map(function($key, $value) {
            return [strtoupper($key) => $value];
        });
        self::assertEquals(['LOREM' => 'ipsum', 'DOLOR' => 'sit'], $map->get());
    }

    public function test_map_keys_and_values()
    {
        $collection = new Collection(['lorem' => 'ipsum', 'dolor' => 'sit']);
        $map = $collection->map(function($key, $value) {
            return [strtoupper($key) => strtoupper($value)];
        });
        self::assertEquals(['LOREM' => 'IPSUM', 'DOLOR' => 'SIT'], $map->get());
    }

    public function test_filter()
    {
        $collection = new Collection([1, 2, 3, 4, 5, 6, 7, 8, 9]);
        $filter = $collection->filter(function($key, $value) {
            return $value < 5;
        });
        $expect = [1, 2, 3, 4];
        self::assertEquals($expect, $filter->get());
    }

    public function test_reduce()
    {
        $collection = new Collection([1, 2, 3, 4]);
        $result = $collection->reduce(function($a, $b) {
            return $a + $b;
        });
        $expect = 10;
        self::assertEquals($expect, $result);
    }

    public function test_chunk()
    {
        $collection = new Collection([1, 2, 3, 4, 5]);
        $expect     = new Collection([0 => [1, 2], 1 => [3, 4], 2 => [5]]);
        self::assertEquals($expect, $collection->chunk(2));
    }

    public function test_keys()
    {
        $collection = new Collection(['three' => 3, 'eight' => 8, 'two' => 2]);
        $expect     = new Collection(['three', 'eight', 'two']);
        self::assertEquals($expect, $collection->keys());
    }

    public function test_values()
    {
        $collection = new Collection(['three' => 3, 'eight' => 8, 'two' => 2]);
        $expect     = new Collection([3, 8, 2]);
        self::assertEquals($expect, $collection->values());
    }

    public function test_join()
    {
        $collection = new Collection([1, 2, 3, 4, 5, 6]);
        self::assertEquals('1|2|3|4|5|6', $collection->join('|'));
    }

    public function test_column()
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
        self::assertEquals($expect, $collection->column('ipsum'));
    }

    public function test_combine()
    {
        $collection = new Collection();
        $collection['KEYS']   = new Collection(['lorem', 'ipsum']);
        $collection['VALUES'] = new Collection(['dolor', 'amet']);
        $collection = Collection::combine($collection['KEYS'], $collection['VALUES']);
        $expect     = new Collection(['lorem' => 'dolor', 'ipsum'=> 'amet']);
        self::assertEquals($expect, $collection);
    }

    public function test_count()
    {
        $collection    = new Collection([1, 2, 3, 4, 5]);
        $collection[2] = [1, 2, 3];

        self::assertEquals(5, $collection->count());
        self::assertEquals(3, $collection[2]->count());
    }

    public function test_recursive_count()
    {
        $collection = new Collection([1, [2, 3], [2 => [1, 2, 3]]]);
        self::assertEquals(9, $collection->count(COUNT_RECURSIVE));
    }

    public function test_recursive_count_should_not_affect_length()
    {
        $collection = new Collection([1, [2, 3], [2 => [1, 2, 3]]]);
        $collection->count(COUNT_RECURSIVE);
        self::assertEquals(3, $collection->length);
    }

    public function test_first_should_return_the_first_element()
    {
        $collection = new Collection(['lorem' => 'ipsum', 'dolor' => 'sit', 'amet' => 'consectetur']);
        self::assertEquals('ipsum', $collection->first());
    }

    public function test_last_should_return_the_last_element()
    {
        $collection = new Collection(['lorem' => 'ipsum', 'dolor' => 'sit', 'amet' => 'consectetur']);
        self::assertEquals('consectetur', $collection->last());
    }

    public function test_lower_case()
    {
        $collection = new Collection(['Lorem' => ['Ipsum' => 'consectetur'], 'Dolor' => ['Amet' => 'elit']]);
        self::assertEquals(['lorem' => ['ipsum' => 'consectetur'], 'dolor' => ['amet' => 'elit']], $collection->lower()->get());
    }

    public function test_upper_case()
    {
        $collection = new Collection(['Lorem' => ['Ipsum' => 'consectetur'], 'Dolor' => ['Amet' => 'elit']]);
        self::assertEquals(['LOREM' => ['IPSUM' => 'consectetur'], 'DOLOR' => ['AMET' => 'elit']], $collection->upper()->get());
    }

    public function test_toString()
    {
        $collection = new Collection(['lorem' => 1, 'Ipsum' => 2]);
        $expect = '{"lorem":1,"Ipsum":2}';
        self::assertEquals($expect, $collection);
    }

    public function test_sort() {
        $collection = new Collection(['three' => 3, 'eight' => 8, 'two' => 2]);
        $collection->sort();
        $expect = [2, 3, 8];
        self::assertSame($expect, $collection->get());
    }

    public function test_rsort() {
        $collection = new Collection(['three' => 3, 'eight' => 8, 'two' => 2]);
        $collection->rsort();
        $expect = [8, 3, 2];
        self::assertSame($expect, $collection->get());
    }

    public function test_asort() {
        $collection = new Collection(['three' => 3, 'eight' => 8, 'two' => 2]);
        $collection->asort();
        $expect = ['two' => 2, 'three' => 3, 'eight' => 8];
        self::assertSame($expect, $collection->get());
    }

    public function test_arsort() {
        $collection = new Collection(['three' => 3, 'eight' => 8, 'two' => 2]);
        $collection->arsort();
        $expect = ['eight' => 8, 'three' => 3, 'two' => 2];
        self::assertSame($expect, $collection->get());
    }

    public function test_ksort() {
        $collection = new Collection(['three' => 3, 'eight' => 8, 'two' => 2]);
        $collection->ksort();
        $expect = ['eight' => 8, 'three' => 3, 'two' => 2];
        self::assertSame($expect, $collection->get());
    }

    public function test_krsort() {
        $collection = new Collection(['three' => 3, 'eight' => 8, 'two' => 2]);
        $collection->krsort();
        $expect = ['two' => 2, 'three' => 3, 'eight' => 8];
        self::assertSame($expect, $collection->get());
    }

    public function test_unique()
    {
        $collection = new Collection([1, 2, 3, 1, 2, 3, 1, 2, 3, 1, 2, 3]);
        $expect     = new Collection([1, 2, 3]);
        self::assertEquals($expect, $collection->unique());
    }

    public function test_merge()
    {
        $collection = new Collection([[1, 2], [2, 4], [4, 5, 13], [2, 3, 9]]);
        $expect     = new Collection([1, 2, 2, 4, 4, 5, 13, 2, 3, 9]);

        self::assertEquals($expect, $collection->merge());
    }

    public function test_reverse()
    {
        $collection = new Collection([1, 2, 3, 4, 5, 6, 7, 8, 9]);
        $expect     = new Collection([9, 8, 7, 6, 5, 4, 3, 2, 1]);
        self::assertEquals($expect, $collection->reverse());
    }

    public function test_search()
    {
        $collection = new Collection(['a', 'b', 'c', 'd', 'e']);
        self::assertEquals(3, $collection->search('d'));
    }

    public function test_union()
    {
        $collection = new Collection([[1, 2], [2, 4], [4, 5, 13], [2, 3, 9]]);
        $expect     = new Collection([1, 2, 4, 5, 13, 3, 9]);
        self::assertEquals($expect, $collection->union()->values());
    }

    public function test_diff()
    {
        $collection = new Collection([[1, 2, 3, 4, 5, 6], [2, 4], [6, 1]]);
        $expect     = new Collection([3, 5]);
        self::assertEquals($expect, $collection->diff()->values());
    }

    public function test_intersect()
    {
        $collection = new Collection([[1, 2, 3, 4, 5, 6], [2, 3, 5, 6], [2, 3, 7, 9]]);
        $expect     = new Collection([2, 3]);
        self::assertEquals($expect, $collection->intersect()->values());
    }

    public function test_cartesian()
    {
        $collection = new Collection([[1, 2], [3, 4], [3, 5]]);
        $expect     = new Collection([[1, 3, 3], [1, 3, 5], [1, 4, 3], [1, 4, 5], [2, 3, 3], [2, 3, 5], [2, 4, 3], [2, 4, 5]]);
        self::assertEquals($expect, $collection->cartesian());
    }

    public function test_flip()
    {
        $collection = new Collection(['lorem' => 1, 'ipsum' => 2, 'dolor' => 3]);
        $expect     = new Collection([1 => 'lorem', 2 => 'ipsum', 3 => 'dolor']);
        self::assertEquals($expect, $collection->flip());
    }

    public function test_sum()
    {
        $collection = new Collection([1, 2, 3, 4, 5, 6, 7, 8, 9]);
        self::assertEquals(45, $collection->sum());
    }

    public function test_contains_should_return_true()
    {
        $collection = new Collection([1, 2, 3, 4, 5, 6, 7, 8, 9]);
        self::assertTrue($collection->contains(7));
    }

    public function test_contains_should_return_false()
    {
        $collection = new Collection([1, 2, 3, 4, 5, 6, 7, 8, 9]);
        self::assertFalse($collection->contains(10));
    }

    public function test_length_after_override_value() {
        $collection = new Collection();
        $collection['lorem'] = 'ipsum';
        $collection['lorem'] = 'dolor';
        self::assertEquals(1, $collection->length);
    }

    public function test_length_after_multidimensional_value() {
        $collection = new Collection();
        $collection['lorem']['ipsum'] = 'dolor';
        $collection['lorem']['amet'] = 'dolor';
        self::assertEquals(1, $collection->length);
    }

    public function test_length_after_override_multidimensional_value() {
        $collection = new Collection();
        $collection['lorem']['ipsum'] = 'dolor';
        $collection['lorem']['ipsum'] = 'amet';
        self::assertEquals(1, $collection->length);
    }

    public function test_length_after_add_value_dot_notation() {
        $collection = new Collection();
        $collection['lorem.ipsum'] = 'dolor';
        self::assertEquals(1, $collection->length);
    }

    public function test_length_after_override_value_dot_notation() {
        $collection = new Collection();
        $collection['lorem.ipsum'] = 'dolor';
        $collection['lorem.ipsum'] = 'amet';
        self::assertEquals(1, $collection->length);
    }

    public function test_array_access_as_property() {
        $collection = new Collection();
        $collection['lorem'] = 'dolor';
        self::assertEquals('dolor', $collection->lorem);
    }

    public function test_array_set_value_as_property() {
        $collection = new Collection();
        $collection->lorem = 'dolor';
        self::assertEquals(['lorem' => 'dolor'], $collection->get());
    }

    public function test_array_check_isset_value_as_property() {
        $collection = new Collection();
        $collection['lorem'] = 'dolor';
        self::assertTrue(isset($collection->lorem));
        self::assertFalse(isset($collection->ipsum));
    }

    public function test_array_unset_value_as_property() {
        $collection = new Collection();
        $collection['lorem'] = 'dolor';
        unset($collection->lorem);
        self::assertEquals([], $collection->get());
    }

    public function test_override_readonly_property_should_throw_exception() {
        self::expectException(InvalidArgumentException::class);
        $collection = new Collection();
        $collection->length = 10;
    }

    public function test_coalesce_should_return_first_non_null_value() {
        $collection = new Collection([null, null, null, 'lorem', null]);
        self::assertEquals('lorem', $collection->coalesce());
    }

    public function test_coalesce_should_return_null_when_all_values_are_null() {
        $collection = new Collection([null, null, null, null, null]);
        self::assertEquals(null, $collection->coalesce());
    }

    public function test_outer_should_return_full_diff() {
        $collection = new Collection([[1, 2, 3, 4, 5], [2, 5, 7, 9]]);
        $expect     = new Collection([[1, 3, 4], [7, 9]]);
        self::assertEquals($expect, $collection->outer());
    }

    public function test_range_should_return_number_list() {
        $collection = Collection::range(1, 10, 2);
        self::assertEquals([1, 3, 5, 7, 9], $collection->get());
    }

    public function test_range_should_return_alphabet_interval() {
        $collection = Collection::range('A', 'F');
        self::assertEquals(['A', 'B', 'C', 'D', 'E', 'F'], $collection->get());
    }

    public function test_random_should_return_random_element() {
        $collection = Collection::range('A', 'F');
        self::assertContains($collection->random(), ['A', 'B', 'C', 'D', 'E', 'F']);
    }

    public function test_walk_should_iterate_all_elements() {
        $collection = new Collection([1, [2, 3, 4], [[5, 6, 7], [8, 9]], [10, 11, 12], 13]);
        $i = 1;
        $collection->walk(function($key, $value) use (&$i) {
            self::assertEquals($i++, $value);
        });
    }

    public function test_shuffle_should_mix_elements() {
        $range = Collection::range('A', 'C');

        $collection = new Collection();
        $collection['0:2'] = $range;

        $cartesian = $collection->cartesian();
        $shuffle   = $range->shuffle();

        self::assertTrue($cartesian->contains($shuffle));
    }

    public function test_is_collection_should_return_true() {
        self::assertTrue(Collection::isCollection(new Collection()));
    }

    public function test_is_collection_should_return_false() {
        self::assertFalse(Collection::isCollection([]));
    }
}