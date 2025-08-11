<?php

namespace App\Tests\DataProvider;

class InvoiceAmountDataProvider
{
    public static function invalidAmountProvider(): array
    {
        return [
            [0],
            [-1]
        ];
    }
}