<?php
declare(strict_types=1);

namespace App\Types;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;

#[ORM\Embeddable]
class PropertyLocation
{
    #[ORM\Column(type: 'string')]
    #[Groups(['list', 'detail'])]
    private string $address;

    #[ORM\Embedded(class: Coordinates::class)]
    #[Groups(['list', 'detail'])]
    private Coordinates $coordinates;

    public function __construct()
    {
        $this->coordinates = new Coordinates();
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;
        return $this;
    }

    public function getCoordinates(): Coordinates
    {
        return $this->coordinates;
    }

    public function setCoordinates(Coordinates $coordinates): self
    {
        $this->coordinates = $coordinates;
        return $this;
    }
}
