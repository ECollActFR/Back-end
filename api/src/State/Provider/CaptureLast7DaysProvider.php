<?php

namespace App\State\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Dto\Output\CaptureLast7DaysDto;
use App\Entity\Room;
use App\Repository\CaptureRepository;
use App\Repository\RoomRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CaptureLast7DaysProvider implements ProviderInterface
{
    public function __construct(
        private RoomRepository $roomRepository,
        private CaptureRepository $captureRepository
    ) {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $roomId = $uriVariables['id'] ?? null;
        
        if (!$roomId) {
            throw new NotFoundHttpException('Room ID is required');
        }

        // API Platform + Voter gèrent automatiquement l'autorisation
        $room = $this->roomRepository->find($roomId);
        
        if (!$room) {
            throw new NotFoundHttpException('Room not found');
        }

        return $this->createCaptureLast7DaysDto($room);
    }

    private function createCaptureLast7DaysDto(Room $room): CaptureLast7DaysDto
    {
        $dto = new CaptureLast7DaysDto();
        $dto->roomId = $room->getId();
        $dto->roomName = $room->getName();
        $dto->roomDescription = $room->getDescription();
        
        $startDate = new \DateTime('7 days ago');
        $endDate = new \DateTime();
        $dto->startDate = $startDate;
        $dto->endDate = $endDate;

        $dataByType = [];
        $totalCount = 0;

        // Pour chaque type disponible dans la salle
        foreach ($room->getCaptureTypes() as $captureType) {
            $captures = $this->captureRepository->findLast7DaysByTypeAndRoom(
                $captureType->getId(), 
                $room->getId()
            );

            if (!empty($captures)) {
                $stats = $this->calculateStats($captures);
                
                $dataByType[] = [
                    'type' => $captureType,
                    'captures' => $captures,
                    'count' => count($captures),
                    'stats' => $stats
                ];
                
                $totalCount += count($captures);
            }
        }

        $dto->dataByType = $dataByType;
        $dto->totalCount = $totalCount;
        $dto->typeCount = count($dataByType);

        return $dto;
    }

    private function calculateStats(array $captures): array
    {
        if (empty($captures)) {
            return ['min' => null, 'max' => null, 'avg' => null, 'latest' => null];
        }

        $values = array_map(fn($c) => (float) $c->getValue(), $captures);
        $latest = $captures[0]; // Déjà ordonné par date DESC

        return [
            'min' => min($values),
            'max' => max($values),
            'avg' => round(array_sum($values) / count($values), 2),
            'latest' => $latest
        ];
    }
}