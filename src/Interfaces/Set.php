<?php

namespace Cajudev\Interfaces;

/**
 * Interface Set
 * @package Cajudev\Interfaces
 */
interface Set
{
    /**
     * Perform a union of sets
     *
     * @return static
     */
    public function union(): static;

    /**
     * Perform a difference of sets
     *
     * @return static
     */
    public function diff(): static;

    /**
     * Perform a intersection of sets
     *
     * @return static
     */
    public function intersect(): static;

    /**
     * Return the cartesian product of the sets
     *
     * @return static
     */
    public function cartesian(): static;
}
