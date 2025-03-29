<?php
declare(strict_types=1);

namespace App\Types;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;

#[ORM\Embeddable]
class Size
{
    #[ORM\Column(type: 'integer')]
    #[Groups(['list', 'detail'])]
    private int $value;

    #[ORM\Column(type: 'string')]
    #[Groups(['list', 'detail'])]
    private string $measurement;

    public function getValue(): int
    {
        return $this->value;
    }

    public function setValue(int $value): self
    {
        $this->value = $value;
        return $this;
    }

    public function getMeasurement(): string
    {
        return $this->measurement;
    }

    public function setMeasurement(string $measurement): self
    {
        $this->measurement = $measurement;
        return $this;
    }
}
