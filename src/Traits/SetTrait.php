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
        $this->content = $this->merge()->unique()->get();
        $this->count();
        return $this;
    }

    /**
     * Perform a difference of sets
     * 
     * @return self
     */
    public function diff(): self
    {   
        $this->content = $this->reduce('array_diff')->get();
        $this->count();
        return $this;
    }

    /**
     * Perform a intersection of sets
     * 
     * @return self
     */
    public function intersect(): self
    {
        $this->content = $this->reduce('array_intersect')->get();
        $this->count();
        return $this;
    }

    /**
     * Perform a cartesian product of sets
     * 
     * @return self
     */
    public function cartesian(): self
    {
        $result = [[]];
        foreach ($this->content as $key => $values) {
            $append = [];
            foreach ($result as $product) {
                foreach ($values as $item) {
                    $product[$key] = $item;
                    $append[] = $product;
                }
            }
            $result = $append;
        }
        $this->content = $result;
        $this->count();
        return $this;
    }
}