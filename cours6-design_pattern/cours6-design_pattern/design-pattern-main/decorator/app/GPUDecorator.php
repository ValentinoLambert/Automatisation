<?php

namespace App;

class GPUDecorator implements Computer
{
    public function __construct(private Computer $computer) {}

    public function getPrice(): int
    {
        return $this->computer->getPrice() + 300;
    }

    public function getDescription(): string
    {
        return $this->computer->getDescription() . ', with GPU';
    }
}
