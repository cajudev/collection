<?php

namespace Cajudev\Traits;

trait SetTrait
{
    /**
     * Perform a union of sets
     * 
     * @return self
     */
    public function union(): self
    {
        return $this->merge()->unique();
    }

    /**
     * Perform a difference of sets
     * 
     * @return self
     */
    public function diff(): self
    {   
        return $this->reduce('array_diff');
    }

    /**
     * Perform a full difference of sets
     * 
     * @return self
     */
    public function outer(): self
    {   
        return $this->return([
            $this->diff()->values()->get(),
            $this->reverse()->diff()->values()->get(),
        ]);
    }

    /**
     * Perform a intersection of sets
     * 
     * @return self
     */
    public function intersect(): self
    {
        return $this->reduce('array_intersect');
    }

    /**
     * Perform a cartesian product of sets
     * 
     * @return self
     */
    public function cartesian(): self
    {
        $cartesian = [[]];
        foreach ($this->content as $key => $values) {
            $append = [];
            foreach ($cartesian as $product) {
                foreach ($values as $item) {
                    $product[$key] = $item;
                    $append[] = $product;
                }
            }
            $cartesian = $append;
        }
        return $this->return($cartesian);
    }
}