<?php

namespace App\Dto\Input;

use Symfony\Component\Validator\Constraints as Assert;

readonly class UserInputDto
{
    public function __construct(
        #[Assert\NotBlank(message: "L'email est obligatoire")]
        #[Assert\Email(message: "L'email n'est pas valide")]
        #[Assert\Length(max: 180)]
        public string $email,

        #[Assert\NotBlank(message: "Le prénom est obligatoire")]
        #[Assert\Length(max: 60)]
        public string $firstname,

        #[Assert\NotBlank(message: "Le nom est obligatoire")]
        #[Assert\Length(max: 60)]
        public string $lastname,

        #[Assert\Length(max: 16)]
        public ?string $phone = null,

        #[Assert\Length(max: 255)]
        public ?string $profilePicture = null,

        public array $roles = [],

        public ?int $clientAccountId = null,
    ) {}
}
