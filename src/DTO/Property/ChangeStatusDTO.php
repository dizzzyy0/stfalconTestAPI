<?php
declare(strict_types=1);

namespace App\DTO\Property;
use App\Enum\PropertyStatus;
use Symfony\Component\Validator\Constraints as Assert;
class ChangeStatusDTO
{
    public function __construct(
        #[Assert\NotNull]
        #[Assert\Choice(callback: 'getValidStatuses')]
        public readonly string $status
    ) {}

    public static function getValidStatuses(): array
    {
        return array_map(fn($case) => $case->value, PropertyStatus::cases());
    }

    public static function fromArray(array $data): self
    {
        return new self(
            status: $data['status'] ?? ''
        );
    }
}
