<?php

namespace App\DataFixtures;

use App\Entity\AcquisitionSystem;
use App\Entity\Building;
use App\Entity\CaptureType;
use App\Entity\Equipment;
use App\Entity\Room;
use App\Entity\Capture;
use App\Entity\User;
use Carbon\Carbon;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $userPasswordHasher;

    public function __construct(UserPasswordHasherInterface $userPasswordHasher) {
        $this->userPasswordHasher = $userPasswordHasher;
    }

    public function load(ObjectManager $manager): void
    {

        $user = new User("Alexis", "Baron", "alexis.baron.nsd@gmail.com", "+33782058609");
        $user->setRoles(["ROLE_USER"]);
        $user->setPassword($this->userPasswordHasher->hashPassword($user, "password"));
        $manager->persist($user);

        // Create Building
        $building = new Building();
        $building->setName("Bâtiment Principal");
        $building->setOwner($user);
        $manager->persist($building);
        
        // Create CaptureTypes
        $captureTypes = [
            ['Temperature', 'Mesure température en °C'],
            ['Humidité', 'Mesure humidité en %'],
            ['CO2', 'Mesure CO2 en ppm'],
            ['Luminosité', 'Mesure luminosité en lux'],
            ['Bruit', 'Mesure bruit en dB'],
        ];

        $captureTypeEntities = [];
        foreach ($captureTypes as [$name, $description]) {
            $captureType = new CaptureType($name, $description);
            $manager->persist($captureType);
            $captureTypeEntities[$name] = $captureType;
        }

        // Create Equipment
        $equipmentData = [
            ['Ordinateur', 12],
            ['Wifi', 1],
            ['Machine à café', 1],
            ['Fontaine à eau', 1],
            ['Ecrans', 24],
            ['Chaises', 50]
        ];

        $equipmentEntities = [];
        foreach ($equipmentData as [$name, $capacity]) {
            $equipment = new Equipment();
            $equipment->setName($name);
            $equipment->setCapacity($capacity);
            $manager->persist($equipment);
            $equipmentEntities[$name] = $equipment;
        }

        // Create Rooms with their available CaptureTypes and Equipment
        $roomsData = [
            [
                'name' => 'Bureau A1',
                'description' => 'Bureau individuel côté sud',
                'captureTypes' => ['Temperature', 'Humidité', 'CO2'],
                'equipment' => ['Ordinateur', 'Ecrans', 'Chaises'],
                'acquisitionSystem' => 'Sensor-A1-001'
            ],
            [
                'name' => 'Bureau A2',
                'description' => 'Bureau individuel côté nord',
                'captureTypes' => ['Temperature', 'Humidité', 'CO2'],
                'equipment' => ['Ordinateur', 'Ecrans', 'Chaises'],
                'acquisitionSystem' => 'Sensor-A2-001'
            ],
            [
                'name' => 'Open Space',
                'description' => 'Espace partagé 20 pers',
                'captureTypes' => ['Temperature', 'Humidité', 'CO2', 'Luminosité', 'Bruit'],
                'equipment' => ['Ordinateur', 'Wifi', 'Ecrans', 'Chaises'],
                'acquisitionSystem' => 'Sensor-OS-001'
            ],
            [
                'name' => 'Réunion',
                'description' => 'Salle réunion 8 pers',
                'captureTypes' => ['Temperature', 'Humidité', 'CO2', 'Bruit'],
                'equipment' => ['Wifi', 'Ecrans', 'Chaises'],
                'acquisitionSystem' => 'Sensor-RE-001'
            ],
            [
                'name' => 'Kitchen',
                'description' => 'Espace détente',
                'captureTypes' => ['Temperature', 'Humidité'],
                'equipment' => ['Machine à café', 'Fontaine à eau', 'Chaises'],
                'acquisitionSystem' => 'Sensor-KT-001'
            ],
            [
                'name' => 'Hall',
                'description' => 'Hall accueil',
                'captureTypes' => ['Temperature', 'Luminosité'],
                'equipment' => ['Chaises'],
                'acquisitionSystem' => 'Sensor-HL-001'
            ],
        ];

        $roomEntities = [];
        foreach ($roomsData as $roomData) {
            $room = new Room();
            $room->setName($roomData['name']);
            $room->setDescription($roomData['description']);
            $room->setBuilding($building); // Associate room with building

            // Add capture types to room
            foreach ($roomData['captureTypes'] as $typeName) {
                $room->addCaptureType($captureTypeEntities[$typeName]);
            }

            // Add equipment to room
            foreach ($roomData['equipment'] as $equipmentName) {
                $room->addEquipment($equipmentEntities[$equipmentName]);
            }

            // Add acquisition system to room
            $acquisitionSystem = new AcquisitionSystem();
            $acquisitionSystem->setName($roomData['acquisitionSystem']);
            $acquisitionSystem->setRoom($room);
            $room->addAcquisitionSystem($acquisitionSystem);

            $manager->persist($room);
            $manager->persist($acquisitionSystem);
            $roomEntities[] = $room;
        }

        // Create sample Captures
        $captureData = [
            'Temperature' => ['values' => ['21.5', '22.8', '20.1', '23.2', '19.7'], 'description' => 'Température'],
            'Humidité' => ['values' => ['45.2', '52.8', '38.1', '61.3', '44.7'], 'description' => 'Humidité'],
            'CO2' => ['values' => ['420', '580', '390', '720', '450'], 'description' => 'CO2'],
            'Luminosité' => ['values' => ['320', '450', '280', '520', '380'], 'description' => 'Luminosité'],
            'Bruit' => ['values' => ['42.5', '38.2', '55.8', '35.1', '48.9'], 'description' => 'Bruit'],
        ];

        foreach ($roomEntities as $room) {
            $hourOffset = 0;

            // Create captures only for types available in the room
            foreach ($room->getCaptureTypes() as $captureType) {
                $typeName = $captureType->getName();
                $data = $captureData[$typeName];

                // Create multiple captures for each type (simulating history)
                for ($i = 0; $i < 3; $i++) {
                    $capture = new Capture();
                    $capture->setDateCaptured(Carbon::now());
                    $capture->setValue($data['values'][array_rand($data['values'])]);
                    $capture->setDescription($data['description']);
                    $capture->setRoom($room);
                    $capture->setType($captureType);
                    $manager->persist($capture);
                    $hourOffset++;
                }
            }
        }

        $manager->flush();
    }
}
