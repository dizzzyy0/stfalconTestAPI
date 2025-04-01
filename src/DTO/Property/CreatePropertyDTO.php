<?php
declare(strict_types=1);

namespace App\DTO\Property;

use App\Entity\Property;
use App\Enum\Currencies;
use App\Enum\PropertyStatus;
use App\Enum\PropertyTypes;
use App\Types\Coordinates;
use App\Types\Price;
use App\Types\PropertyLocation;
use App\Types\Size;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

class CreatePropertyDTO
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Choice(callback: [PropertyTypes::class, 'cases'])]
        public readonly string $type,

        #[Assert\NotBlank]
        #[Assert\Positive]
        public readonly float $priceAmount,

        #[Assert\NotBlank]
        #[Assert\Choice(callback: [Currencies::class, 'cases'])]
        public readonly string $priceCurrency,

        #[Assert\NotBlank]
        #[Assert\Length(min: 1, max: 255)]
        public readonly string $address,

        #[Assert\NotBlank]
        public readonly float $latitude,

        #[Assert\NotBlank]
        public readonly float $longitude,

        #[Assert\NotBlank]
        #[Assert\Uuid]
        public readonly Uuid $agentId,

        #[Assert\NotBlank]
        #[Assert\Positive]
        public readonly int $sizeValue,

        #[Assert\NotBlank]
        #[Assert\Length(min: 1, max: 10)]
        public readonly string $sizeMeasurement,

        #[Assert\NotBlank]
        #[Assert\Length(min: 10)]
        public readonly string $description,

        #[Assert\NotBlank]
        #[Assert\Choice(callback: [PropertyStatus::class, 'cases'])]
        public readonly string $status = PropertyStatus::DRAFT->value
    ) {
    }

    /**
     * Створює Entity з даних DTO
     */
    public function toEntity(): Property
    {
        $property = new Property();

        $property->setType(PropertyTypes::fromId($this->type) ?? PropertyTypes::RESIDETIAL);

        $price = new Price();
        $price->setAmount($this->priceAmount);
        $price->setCurrency(Currencies::fromId($this->priceCurrency) ?? Currencies::UAH);
        $property->setPrice($price);

        $coordinates = new Coordinates();
        $coordinates->setLatitude($this->latitude);
        $coordinates->setLongitude($this->longitude);

        $location = new PropertyLocation();
        $location->setAddress($this->address);
        $location->setCoordinates($coordinates);
        $property->setLocation($location);

        $size = new Size();
        $size->setValue($this->sizeValue);
        $size->setMeasurement($this->sizeMeasurement);
        $property->setSize($size);

        $property->setDescription($this->description);
        $property->setStatus(PropertyStatus::fromId($this->status) ?? PropertyStatus::DRAFT);

        return $property;
    }

    /**
     * Створити DTO з даних запиту
     */
    public static function fromArray(array $data): self
    {
        return new self(
            type: $data['type'] ?? '',
            priceAmount: (float)($data['price']['amount'] ?? 0),
            priceCurrency: $data['price']['currency'] ?? '',
            address: $data['location']['address'] ?? '',
            latitude: (float)($data['location']['coordinates']['latitude'] ?? 0),
            longitude: (float)($data['location']['coordinates']['longitude'] ?? 0),
            agentId: Uuid::fromString($data['agentId'] ?? ''),
            sizeValue: (int)($data['size']['value'] ?? 0),
            sizeMeasurement: $data['size']['measurement'] ?? '',
            description: $data['description'] ?? '',
            status: $data['status'] ?? PropertyStatus::DRAFT->value
        );
    }
}
