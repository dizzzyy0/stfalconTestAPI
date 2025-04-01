<?php
declare(strict_types=1);

namespace App\Presenters;

use App\Types\Price;

class PricePresenter
{
    public function __construct(private CurrencyPresenter $currencyPresenter){}

    public function present(Price $price): array
    {
        return [
            'amount' => $price->getAmount(),
            'currency' => $this->currencyPresenter->present($price->getCurrency()),
        ];
    }
}
