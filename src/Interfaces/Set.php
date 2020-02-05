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
     * @return mixed
     */
    public function union(): Set;

    /**
     * Perform a difference of sets
     *
     * @return mixed
     */
    public function diff(): Set;

    /**
     * Perform a intersection of sets
     *
     * @return mixed
     */
    public function intersect(): Set;

    /**
     * Return the cartesian product of the sets
     *
     * @return mixed
     */
    public function cartesian(): Set;
}
