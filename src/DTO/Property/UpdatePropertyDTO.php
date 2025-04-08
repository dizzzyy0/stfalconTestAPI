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
use Symfony\Component\Validator\Constraints as Assert;

class UpdatePropertyDTO
{
    public function __construct(
        #[Assert\Choice(callback: [PropertyTypes::class, 'cases'])]
        public readonly ?string $type = null,

        #[Assert\Positive]
        public readonly ?float  $priceAmount = null,

        #[Assert\Choice(callback: [Currencies::class, 'cases'])]
        public readonly ?string $priceCurrency = null,

        #[Assert\Length(min: 1, max: 255)]
        public readonly ?string $address = null,

        public readonly ?float  $latitude = null,

        public readonly ?float  $longitude = null,

        #[Assert\Positive]
        public readonly ?int    $sizeValue = null,

        #[Assert\Length(min: 1, max: 10)]
        public readonly ?string $sizeMeasurement = null,

        #[Assert\Length(min: 10)]
        public readonly ?string $description = null,

        #[Assert\Choice(callback: [PropertyStatus::class, 'cases'])]
        public readonly ?string $status = null
    )
    {
    }

    /**
     * Застосовує зміни з DTO до існуючого Entity
     */
    public function toEntity(Property $property): Property
    {
        if ($this->type !== null) {
            $property->setType(PropertyTypes::fromId($this->type) ?? $property->getType());
        }

        if ($this->priceAmount !== null) {
            $property->getPrice()->setAmount($this->priceAmount);
        }

        if ($this->priceCurrency !== null) {
            $property->getPrice()->setCurrency(Currencies::fromId($this->priceCurrency) ?? $property->getPrice()->getCurrency());
        }

        if ($this->address !== null) {
            $property->getLocation()->setAddress($this->address);
        }

        if ($this->latitude !== null) {
            $property->getLocation()->getCoordinates()->setLatitude($this->latitude);
        }

        if ($this->longitude !== null) {
            $property->getLocation()->getCoordinates()->setLongitude($this->longitude);
        }

        if ($this->sizeValue !== null) {
            $property->getSize()->setValue($this->sizeValue);
        }

        if ($this->sizeMeasurement !== null) {
            $property->getSize()->setMeasurement($this->sizeMeasurement);
        }

        if ($this->description !== null) {
            $property->setDescription($this->description);
        }

        if ($this->status !== null) {
            $property->setStatus(PropertyStatus::fromId($this->status) ?? $property->getStatus());
        }

        return $property;
    }

    /**
     * Створити DTO з даних запиту
     */
    public static function fromArray(array $data): self
    {
        $price = $data['price'] ?? [];
        $location = $data['location'] ?? [];
        $coordinates = $location['coordinates'] ?? [];
        $size = $data['size'] ?? [];

        return new self(
            type: $data['type'] ?? null,
            priceAmount: isset($price['amount']) ? (float)$price['amount'] : null,
            priceCurrency: $price['currency'] ?? null,
            address: $location['address'] ?? null,
            latitude: isset($coordinates['latitude']) ? (float)$coordinates['latitude'] : null,
            longitude: isset($coordinates['longitude']) ? (float)$coordinates['longitude'] : null,
            sizeValue: isset($size['value']) ? (int)$size['value'] : null,
            sizeMeasurement: $size['measurement'] ?? null,
            description: $data['description'] ?? null,
            status: $data['status'] ?? null
        );
    }
}
