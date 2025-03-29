<?php
declare(strict_types=1);

namespace App\Types;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;

#[ORM\Embeddable]
class Coordinates
{
    #[ORM\Column(type: 'float')]
    #[Groups(['list', 'detail'])]
    private float $latitude;

    #[ORM\Column(type: 'float')]
    #[Groups(['list', 'detail'])]
    private float $longitude;

    public function getLatitude(): float
    {
        return $this->latitude;
    }

    public function setLatitude(float $latitude): self
    {
        $this->latitude = $latitude;
        return $this;
    }

    public function getLongitude(): float
    {
        return $this->longitude;
    }

    public function setLongitude(float $longitude): self
    {
        $this->longitude = $longitude;
        return $this;
    }
}
