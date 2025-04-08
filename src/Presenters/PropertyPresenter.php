<?php
declare(strict_types=1);

namespace App\Presenters;

use App\Entity\Property;

final readonly class PropertyPresenter
{

    public function __construct(
        private readonly PropertyTypesPresenter $propertyTypesPresenter,
        private readonly PricePresenter $pricePresenter,
        private readonly LocationPresenter $locationPresenter,
        private readonly SizePresenter $sizePresenter,
        private readonly PropertyStatusPresenter $propertyStatusPresenter,
    ){}

    public function present(Property $property): array{
        return [
            'id' => $property->getId(),
            'type' => $this->propertyTypesPresenter->present($property->getType()),
            'price' => $this->pricePresenter->present($property->getPrice()),
            'location' => $this->locationPresenter->present($property->getLocation()),
            'size' => $this->sizePresenter->present($property->getSize()),
            'description' => $property->getDescription(),
            'status' => $this->propertyStatusPresenter->present($property->getStatus()),
        ];
    }

    public function presentPaginatedProperty(array $propertyData): array{
        $results = $propertyData['results'] ?? $propertyData['result'] ?? [];
        return [
            'results' => array_map(fn (Property $property) => $this->present($property), $results),
            'metadata' => [
                'total' => $propertyData['total'],
                'offset' => $propertyData['offset'],
                'limit' => $propertyData['limit'],
            ],
        ];
    }

    public function presentList(array $properties): array{
        return [
            'results' => array_map(fn (Property $property) => $this->present($property), $properties),
        ];
    }
}
