<?php

namespace Cajudev\Traits;

use Cajudev\Interfaces\Set;

trait SetTrait
{
    public function union(): static
    {
        return $this->merge()->unique();
    }

    public function diff(): static
    {
        return $this->reduce('array_diff');
    }

    public function outer(): static
    {
        return $this->return([
            $this->diff()->values()->get(),
            $this->reverse()->diff()->values()->get(),
        ]);
    }

    public function intersect(): static
    {
        return $this->reduce('array_intersect');
    }

    public function cartesian(): static
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
