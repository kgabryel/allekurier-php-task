<?php

namespace App\Core\Invoice\Application\Command\CreateInvoice;

use App\Core\Invoice\Domain\Invoice;
use App\Core\Invoice\Domain\Repository\InvoiceRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class CreateInvoiceHandler
{
    public function __construct(
        private readonly InvoiceRepositoryInterface $invoiceRepository
    ) {}

    public function __invoke(CreateInvoiceCommand $command): void
    {
        $this->invoiceRepository->save(
            new Invoice($command->user, $command->amount)
        );

        $this->invoiceRepository->flush();
    }
}
