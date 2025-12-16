<?php

namespace App\Dto\Input;

use Symfony\Component\Validator\Constraints as Assert;

readonly class DeviceTaskInputDto
{
    public function __construct(
        #[Assert\Length(max: 32, maxMessage: 'Le nom de la tâche ne peut pas dépasser {{ limit }} caractères')]
        public ?string $taskName = null,

        #[Assert\Length(max: 16, maxMessage: 'Le type de tâche ne peut pas dépasser {{ limit }} caractères')]
        public ?string $taskType = null,

        #[Assert\Type('array', message: 'Les paramètres doivent être un tableau')]
        public ?array $parameters = null,

        #[Assert\Type('bool', message: 'L\'activation doit être un booléen')]
        public ?bool $enabled = null,

        #[Assert\Type('integer', message: 'L\'intervalle d\'exécution doit être un entier')]
        #[Assert\Positive(message: 'L\'intervalle d\'exécution doit être positif')]
        public ?int $executionInterval = null,

        #[Assert\Type('integer', message: 'La priorité doit être un entier')]
        #[Assert\Range(min: 0, max: 10, minMessage: 'La priorité doit être au moins {{ min }}', maxMessage: 'La priorité ne peut pas dépasser {{ max }}')]
        public ?int $priority = null,

        #[Assert\NotBlank(message: 'Le système d\'acquisition est obligatoire')]
        #[Assert\Type('integer', message: 'L\'ID du système d\'acquisition doit être un entier')]
        public int $acquisitionSystemId,

        #[Assert\Type('integer', message: 'L\'ID du client account doit être un entier')]
        public ?int $clientAccountId = null,
    ) {}
}