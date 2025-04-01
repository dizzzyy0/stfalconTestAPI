<?php
declare(strict_types=1);

namespace App\Presenters;

use App\Enum\Currencies;
use Doctrine\ORM\EntityManagerInterface;

class CurrencyPresenter
{

    public function present(Currencies $currency): array{
        return [
            'id' => $currency->value,
            'name' => $currency->getName(),
        ];
    }

    public function presentList(array $currencies): array{
        return array_map(fn (Currencies $currency) => $this->present($currency), $currencies);
    }
}
