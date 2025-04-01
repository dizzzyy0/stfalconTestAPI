<?php
declare(strict_types=1);

namespace App\DTO\Property;

class PropertyVisibilityDTO
{
    public function __construct(
        public array $visibleStatuses,
    ){}
}
