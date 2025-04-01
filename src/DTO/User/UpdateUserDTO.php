<?php
declare(strict_types=1);

namespace App\DTO\User;
use Symfony\Component\Validator\Constraints as Assert;

class UpdateUserDTO
{
    public function __construct(
        #[Assert\Choice(
            choices: ['ROLE_AGENT', 'ROLE_CUSTOMER'],
            message: 'Choose a valid role: {{ choices }}'
        )]
        public readonly ?string $role = null,
        #[Assert\Email(
            message: "The email '{{ value }}' is not a valid email.",
            mode: Assert\Email::VALIDATION_MODE_STRICT
        )]
        public readonly ?string $email = null,
        public readonly ?string $password = null,
        #[Assert\Length(min: 3, max: 50)]
        public readonly ?string $name = null,
        #[Assert\Length(13)]
        public readonly ?string $phone = null,
    ){}
}
