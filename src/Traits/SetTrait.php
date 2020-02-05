<?php

namespace Cajudev\Traits;

use Cajudev\Interfaces\Set;

trait SetTrait
{
    public function union(): Set
    {
        return $this->merge()->unique();
    }

    public function diff(): Set
    {
        return $this->reduce('array_diff');
    }

    public function outer(): Set
    {
        return $this->return([
            $this->diff()->values()->get(),
            $this->reverse()->diff()->values()->get(),
        ]);
    }

    public function intersect(): Set
    {
        return $this->reduce('array_intersect');
    }

    public function cartesian(): Set
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
