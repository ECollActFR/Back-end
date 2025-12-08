<?php

namespace App\State\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Dto\Output\RoomWithLastCapturesDto;
use App\Entity\Room;
use App\Repository\CaptureRepository;
use App\Repository\RoomRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class RoomWithLastCapturesProvider implements ProviderInterface
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

        $room = $this->roomRepository->find($roomId);
        
        if (!$room) {
            throw new NotFoundHttpException('Room not found');
        }

        return $this->createRoomWithLastCapturesDto($room);
    }

    private function createRoomWithLastCapturesDto(Room $room): RoomWithLastCapturesDto
    {
        $dto = new RoomWithLastCapturesDto();
        $dto->id = $room->getId();
        $dto->name = $room->getName();
        $dto->description = $room->getDescription();
        $dto->createdAt = $room->getCreatedAt();

        $lastCapturesByType = [];

        // Get the last capture for each type available in this room
        foreach ($room->getCaptureTypes() as $captureType) {
            $lastCapture = $this->captureRepository->findOneBy(
                ['room' => $room, 'type' => $captureType],
                ['dateCaptured' => 'DESC']
            );

            if ($lastCapture) {
                $lastCapturesByType[] = [
                    'type' => [
                        'id' => $captureType->getId(),
                        'name' => $captureType->getName(),
                        'description' => $captureType->getDescription()
                    ],
                    'capture' => [
                        'id' => $lastCapture->getId(),
                        'value' => $lastCapture->getValue(),
                        'description' => $lastCapture->getDescription(),
                        'createdAt' => $lastCapture->getCreatedAt()->format('c'),
                        'dateCaptured' => $lastCapture->getDateCaptured()?->format('c')
                    ]
                ];
            }
        }

        $dto->lastCapturesByType = $lastCapturesByType;

        return $dto;
    }
}