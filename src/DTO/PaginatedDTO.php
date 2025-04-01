<?php
declare(strict_types=1);

namespace App\DTO;
use Symfony\Component\Validator\Constraints as Assert;

class PaginatedDTO
{
    public function __construct(
        #[Assert\NotBlank]
        public readonly int $offset,
        #[Assert\NotBlank]
        public readonly int $limit,
    ){}
}
