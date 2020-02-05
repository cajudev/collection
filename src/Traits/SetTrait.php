<?php

namespace Cajudev\Traits;

trait SetTrait
{
    public function union(): self
    {
        return $this->merge()->unique();
    }

    public function diff(): self
    {
        return $this->reduce('array_diff');
    }

    public function outer(): self
    {
        return $this->return([
            $this->diff()->values()->get(),
            $this->reverse()->diff()->values()->get(),
        ]);
    }

    public function intersect(): self
    {
        return $this->reduce('array_intersect');
    }

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
