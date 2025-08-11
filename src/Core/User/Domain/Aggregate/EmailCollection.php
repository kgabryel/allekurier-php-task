<?php

namespace App\Core\User\Domain\Aggregate;

class EmailCollection
{
    private array $emails;

    public function __construct(string ...$emails)
    {
        $this->emails = $emails;
    }

    public function getEmails(): array
    {
        return $this->emails;
    }
}