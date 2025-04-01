<?php
declare(strict_types=1);

namespace App\DTO\User;
use Symfony\Component\Validator\Constraints as Assert;

class RegisterUserDTO
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Choice(
            choices: ['ROLE_AGENT', 'ROLE_CUSTOMER'],
            message: 'Choose a valid role: {{ choices }}'
        )]
        public readonly string $role,
        #[Assert\NotBlank]
        #[Assert\Email(
            message: "The email '{{ value }}' is not a valid email.",
            mode: Assert\Email::VALIDATION_MODE_STRICT
        )]
        public readonly string $email,
        #[Assert\NotBlank]
        public readonly string $password,
        #[Assert\NotBlank]
        #[Assert\Length(min: 3, max: 50)]
        public readonly string $name,
        #[Assert\NotBlank]
        #[Assert\Length(13)]
        public readonly string $phone,
    ){}
}
