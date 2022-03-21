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
     * @return static
     */
    public function sort(): static;

    /**
     * Sort the elements of the collection in descending order
     *
     * @return static
     */
    public function rsort(): static;

    /**
     * Sort the elements of the collection associatively in ascending order
     *
     * @return static
     */
    public function asort(): static;

    /**
     * Sort the elements of the collection associatively in descending order
     *
     * @return static
     */
    public function arsort(): static;

    /**
     * Sort the elements of the collection using keys in ascending order
     *
     * @return static
     */
    public function ksort(): static;

    /**
     * Sort the elements of the collection using keys in descending order
     *
     * @return static
     */
    public function krsort(): static;

    /**
     * Sort the elements of the collection using a custom function
     *
     * @param callable $callback
     * @return static
     */
    public function usort(callable $callback): static;
}
