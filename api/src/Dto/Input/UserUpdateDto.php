<?php

namespace App\Dto\Input;

use Symfony\Component\Validator\Constraints as Assert;

readonly class UserUpdateDto
{
    public function __construct(
        #[Assert\Email(message: "L'email n'est pas valide")]
        #[Assert\Length(max: 180)]
        public ?string $email = null,

        #[Assert\Length(min: 8, minMessage: "Le mot de passe doit contenir au moins {{ limit }} caractères")]
        public ?string $password = null,

        #[Assert\Length(max: 60)]
        public ?string $firstname = null,

        #[Assert\Length(max: 60)]
        public ?string $lastname = null,

        #[Assert\Length(max: 16)]
        public ?string $phone = null,

        #[Assert\Length(max: 255)]
        public ?string $profilePicture = null,

        public ?array $roles = null,
    ) {}
}
