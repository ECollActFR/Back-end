<?php

namespace App\Dto\Mapper;

use App\Dto\Input\UserInputDto;
use App\Dto\Input\UserUpdateDto;
use App\Dto\Output\UserOutputDto;
use App\Entity\User;

class UserMapper
{
    public function mapInputDtoToEntity(UserInputDto $dto, User $user): User
    {
        $user->setEmail($dto->email);
        // Le mot de passe sera haché dans le Processor
        $user->setFirstname($dto->firstname);
        $user->setLastname($dto->lastname);
        $user->setPhone($dto->phone);
        $user->setProfilePictureUrl($dto->profilePictureUrl);
        
        if (!empty($dto->roles)) {
            $user->setRoles($dto->roles);
        }

        return $user;
    }

    public function mapUpdateDtoToEntity(UserUpdateDto $dto, User $user): User
    {
        if ($dto->email !== null) {
            $user->setEmail($dto->email);
        }
        
        // Le mot de passe sera haché dans le Processor si fourni
        
        if ($dto->firstname !== null) {
            $user->setFirstname($dto->firstname);
        }
        
        if ($dto->lastname !== null) {
            $user->setLastname($dto->lastname);
        }
        
        if ($dto->phone !== null) {
            $user->setPhone($dto->phone);
        }
        
        if ($dto->profilePictureUrl !== null) {
            $user->setProfilePictureUrl($dto->profilePictureUrl);
        }
        
        if ($dto->roles !== null) {
            $user->setRoles($dto->roles);
        }

        return $user;
    }

    public function mapEntityToOutputDto(User $user): UserOutputDto
    {
        $buildingIds = [];
        foreach ($user->getBuildings() as $building) {
            $buildingIds[] = $building->getId();
        }

        $clientAccount = null;
        if ($user->getClientAccount()) {
            $clientAccount = [
                'id' => $user->getClientAccount()->getId(),
                'companyName' => $user->getClientAccount()->getCompanyName(),
                'siret' => $user->getClientAccount()->getSiret(),
                'address' => $user->getClientAccount()->getAddress(),
                'city' => $user->getClientAccount()->getCity(),
                'postalCode' => $user->getClientAccount()->getPostalCode(),
                'country' => $user->getClientAccount()->getCountry(),
                'phone' => $user->getClientAccount()->getPhone(),
                'contactEmail' => $user->getClientAccount()->getContactEmail(),
            ];
        }

        return new UserOutputDto(
            id: $user->getId(),
            email: $user->getEmail(),
            roles: $user->getRoles(),
            firstname: $user->getFirstname(),
            lastname: $user->getLastname(),
            phone: $user->getPhone(),
            profilePictureUrl: $user->getProfilePictureUrl(),
            isActive: $user->isActive(),
            emailVerified: $user->isEmailVerified(),
            createdAt: $user->getCreatedAt()?->format('c'),
            lastLogin: $user->getLastLogin()?->format('c'),
            buildingIds: $buildingIds,
            clientAccount: $clientAccount,
        );
    }
}
