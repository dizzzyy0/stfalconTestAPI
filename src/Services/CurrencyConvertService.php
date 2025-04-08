<?php
declare(strict_types=1);

namespace App\Services;

use App\Enum\Currencies;

class CurrencyConvertService
{
    public function convertCurrency(Currencies $from, Currencies $to, float $amount):float
    {
        if($from === $to){
            return $amount;
        }
        $exchangeRate = $this->exchangeRate($from, $to);
        return $amount * $exchangeRate;
    }

    private function exchangeRate(Currencies $from, Currencies $to):float
    {
        return match([$from, $to]){
            [Currencies::USD, Currencies::UAH] => 41.45,
            [Currencies::USD, Currencies::EUR] => 0.93,
            [Currencies::EUR, Currencies::UAH] => 44.74,
            [Currencies::EUR, Currencies::USD] => 1.08,
            [Currencies::UAH, Currencies::USD] => 0.024,
            [Currencies::UAH, Currencies::EUR] => 0.022,
            default => 0,
        };
    }
}
