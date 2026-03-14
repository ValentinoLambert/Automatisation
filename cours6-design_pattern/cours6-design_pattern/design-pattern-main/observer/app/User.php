<?php

namespace App;

class User implements \SplObserver
{
    public function __construct(
        private string $name,
        private bool $notified = false
    ) {}

    public function update(\SplSubject $subject): void
    {
        $this->notified = true;
    }

    public function isNotified(): bool
    {
        return $this->notified;
    }
}