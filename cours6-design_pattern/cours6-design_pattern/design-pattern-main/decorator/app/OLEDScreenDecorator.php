<?php

namespace App;

class OLEDScreenDecorator implements Computer
{
    public function __construct(private Computer $computer) {}

    public function getPrice(): int
    {
        return $this->computer->getPrice() + 200;
    }

    public function getDescription(): string
    {
        return $this->computer->getDescription() . ', with OLED screen';
    }
}
