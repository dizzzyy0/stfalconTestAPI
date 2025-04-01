<?php
declare(strict_types=1);

namespace App\DTO\User;

use Symfony\Component\Uid\Uuid;

class FavoriteDTO
{
    public function __construct(
        public Uuid $propertyId,
    )
    {}
}
