<?php

namespace Cajudev\Interfaces;

/**
 * Interface Sortable
 * @package Cajudev\Interfaces
 */
interface Sortable
{
    /**
     * Sort the elements of the collection in ascending order
     *
     * @return mixed
     */
    public function sort(): Sortable;

    /**
     * Sort the elements of the collection in descending order
     *
     * @return mixed
     */
    public function rsort(): Sortable;

    /**
     * Sort the elements of the collection associatively in ascending order
     *
     * @return mixed
     */
    public function asort(): Sortable;

    /**
     * Sort the elements of the collection associatively in descending order
     *
     * @return mixed
     */
    public function arsort(): Sortable;

    /**
     * Sort the elements of the collection using keys in ascending order
     *
     * @return mixed
     */
    public function ksort(): Sortable;

    /**
     * Sort the elements of the collection using keys in descending order
     *
     * @return mixed
     */
    public function krsort(): Sortable;

    /**
     * Sort the elements of the collection using a custom function
     *
     * @param callable $callback
     * @return mixed
     */
    public function usort(callable $callback): Sortable;
}
