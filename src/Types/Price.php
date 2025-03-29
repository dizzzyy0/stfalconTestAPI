<?php
declare(strict_types=1);

namespace App\Types;

use App\Enum\Currencies;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;

#[ORM\Embeddable]
class Price
{
    #[ORM\Column(type: 'float')]
    #[Groups(['list', 'detail'])]
    private float $amount;

    #[ORM\Column(type: 'string', enumType: Currencies::class)]
    #[Groups(['list', 'detail'])]
    private Currencies $currency;

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): self
    {
        $this->amount = $amount;
        return $this;
    }

    public function getCurrency(): Currencies
    {
        return $this->currency;
    }

    public function setCurrency(Currencies $currency): self
    {
        $this->currency = $currency;
        return $this;
    }
}
