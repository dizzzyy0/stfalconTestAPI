<?php
declare(strict_types=1);

namespace App\Entity;

use App\Enum\PropertyStatus;
use App\Enum\PropertyTypes;
use App\Types\Price;
use App\Types\PropertyLocation;
use App\Types\Size;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
#[ORM\Table(name: 'properties')]
class Property
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[Groups(['list', 'detail'])]
    private Uuid $id;

    #[ORM\Column(type: 'string', enumType: PropertyTypes::class)]
    #[Groups(['list', 'detail'])]
    private PropertyTypes $type;

    #[ORM\Embedded(class: Price::class)]
    #[Groups(['list', 'detail'])]
    private Price $price;

    #[ORM\Embedded(class: PropertyLocation::class)]
    #[Groups(['list', 'detail'])]
    private PropertyLocation $location;

    #[ORM\Embedded(class: Size::class)]
    #[Groups(['list', 'detail'])]
    private Size $size;

    #[ORM\Column(type: 'text')]
    #[Groups(['list', 'detail'])]
    private string $description;

    #[ORM\Column(type: 'string', enumType: PropertyStatus::class)]
    #[Groups(['list', 'detail'])]
    private PropertyStatus $status;

    #[ORM\ManyToOne(inversedBy: 'properties')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Agent $agent = null;

    public function __construct()
    {
        $this->id = Uuid::v4();
        $this->price = new Price();
        $this->location = new PropertyLocation();
        $this->size = new Size();
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getType(): PropertyTypes
    {
        return $this->type;
    }

    public function setType(PropertyTypes $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function getPrice(): Price
    {
        return $this->price;
    }

    public function setPrice(Price $price): self
    {
        $this->price = $price;
        return $this;
    }

    public function getLocation(): PropertyLocation
    {
        return $this->location;
    }

    public function setLocation(PropertyLocation $location): self
    {
        $this->location = $location;
        return $this;
    }

    public function getSize(): Size
    {
        return $this->size;
    }

    public function setSize(Size $size): self
    {
        $this->size = $size;
        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getStatus(): PropertyStatus
    {
        return $this->status;
    }

    public function setStatus(PropertyStatus $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function getAgent(): ?Agent
    {
        return $this->agent;
    }

    public function setAgent(?Agent $agent): static
    {
        $this->agent = $agent;

        return $this;
    }
}
