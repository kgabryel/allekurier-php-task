<?php

namespace App\Core\Invoice\Application\Command\CreateInvoice;

use App\Core\User\Domain\User;

class CreateInvoiceCommand
{
    public function __construct(
        public readonly User $user,
        public readonly int $amount
    ) {}
}
