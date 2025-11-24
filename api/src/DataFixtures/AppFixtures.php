<?php

namespace App\DataFixtures;

use App\Entity\AcquisitionSystem;
use App\Entity\Building;
use App\Entity\CaptureType;
use App\Entity\DeviceNetworkConfig;
use App\Entity\DeviceSensor;
use App\Entity\DeviceSystemConfig;
use App\Entity\DeviceTask;
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
        $acquisitionSystemEntities = [];
        foreach ($roomsData as $index => $roomData) {
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
            $acquisitionSystem->setDeviceType('ESP32_WROOM');
            $acquisitionSystem->setFirmwareVersion('3.0.0');
            $acquisitionSystem->setIsActive(true);
            $room->addAcquisitionSystem($acquisitionSystem);

            $manager->persist($room);
            $manager->persist($acquisitionSystem);
            $roomEntities[] = $room;
            $acquisitionSystemEntities[] = $acquisitionSystem;
        }

        // Configure all acquisition systems with ESP32 configuration
        foreach ($acquisitionSystemEntities as $systemIndex => $esp32Config) {
            // Network Configuration (DHCP - no fixed IP)
            $networkConfig = new DeviceNetworkConfig();
            $networkConfig->setAcquisitionSystem($esp32Config);
            $networkConfig->setWifiSsid('Freebox Maison');
            $networkConfig->setNtpServer('time.google.com');
            $networkConfig->setTimezone('Europe/Paris');
            $networkConfig->setGmtOffsetSec(3600);
            $networkConfig->setDaylightOffsetSec(3600);
            $esp32Config->setNetworkConfig($networkConfig);
            $manager->persist($networkConfig);

            // Sensor Configuration - AHT20 for Temperature
            $sensorAht20Temp = new DeviceSensor();
            $sensorAht20Temp->setAcquisitionSystem($esp32Config);
            $sensorAht20Temp->setCaptureType($captureTypeEntities['Temperature']);
            $sensorAht20Temp->setSensorType('aht20');
            $sensorAht20Temp->setEnabled(true);
            $sensorAht20Temp->setReadIntervalMs(10000);
            $sensorAht20Temp->setI2cSdaPin(26);
            $sensorAht20Temp->setI2cSclPin(27);
            $esp32Config->addSensor($sensorAht20Temp);
            $manager->persist($sensorAht20Temp);

            // Sensor Configuration - AHT20 for Humidity
            $sensorAht20Hum = new DeviceSensor();
            $sensorAht20Hum->setAcquisitionSystem($esp32Config);
            $sensorAht20Hum->setCaptureType($captureTypeEntities['Humidité']);
            $sensorAht20Hum->setSensorType('aht20');
            $sensorAht20Hum->setEnabled(true);
            $sensorAht20Hum->setReadIntervalMs(10000);
            $sensorAht20Hum->setI2cSdaPin(26);
            $sensorAht20Hum->setI2cSclPin(27);
            $esp32Config->addSensor($sensorAht20Hum);
            $manager->persist($sensorAht20Hum);

            // Sensor Configuration - MQ135 for Air Quality (CO2)
            $sensorMq135 = new DeviceSensor();
            $sensorMq135->setAcquisitionSystem($esp32Config);
            $sensorMq135->setCaptureType($captureTypeEntities['CO2']);
            $sensorMq135->setSensorType('mq135');
            $sensorMq135->setEnabled(true);
            $sensorMq135->setReadIntervalMs(10000);
            $sensorMq135->setAdcPin(39);
            $sensorMq135->setWarmupDurationSec(180);
            $esp32Config->addSensor($sensorMq135);
            $manager->persist($sensorMq135);

            // Task Configuration - Sensor Read
            $taskSensorRead = new DeviceTask();
            $taskSensorRead->setAcquisitionSystem($esp32Config);
            $taskSensorRead->setTaskName('sensor_read');
            $taskSensorRead->setEnabled(true);
            $taskSensorRead->setIntervalMs(10000);
            $taskSensorRead->setPriority(4);
            $esp32Config->addTask($taskSensorRead);
            $manager->persist($taskSensorRead);

            // Task Configuration - API Post
            $taskApiPost = new DeviceTask();
            $taskApiPost->setAcquisitionSystem($esp32Config);
            $taskApiPost->setTaskName('api_post');
            $taskApiPost->setEnabled(true);
            $taskApiPost->setIntervalMs(60000);
            $taskApiPost->setPriority(3);
            $taskApiPost->setTaskConfig([
                'endpoint' => 'https://api.ecollact.fr/captures',
                'room_id' => $roomEntities[$systemIndex]->getId()
            ]);
            $esp32Config->addTask($taskApiPost);
            $manager->persist($taskApiPost);

            // Task Configuration - Display
            $taskDisplay = new DeviceTask();
            $taskDisplay->setAcquisitionSystem($esp32Config);
            $taskDisplay->setTaskName('display');
            $taskDisplay->setEnabled(true);
            $taskDisplay->setIntervalMs(2000);
            $taskDisplay->setPriority(2);
            $taskDisplay->setTaskConfig([
                'oled_width' => 128,
                'oled_height' => 64
            ]);
            $esp32Config->addTask($taskDisplay);
            $manager->persist($taskDisplay);

            // Task Configuration - Blink
            $taskBlink = new DeviceTask();
            $taskBlink->setAcquisitionSystem($esp32Config);
            $taskBlink->setTaskName('blink');
            $taskBlink->setEnabled(true);
            $taskBlink->setIntervalMs(1000);
            $taskBlink->setPriority(1);
            $taskBlink->setTaskConfig([
                'led_pin' => 2
            ]);
            $esp32Config->addTask($taskBlink);
            $manager->persist($taskBlink);

            // System Configuration
            $systemConfig = new DeviceSystemConfig();
            $systemConfig->setAcquisitionSystem($esp32Config);
            $systemConfig->setDebug(false);
            $systemConfig->setBufferSize(100);
            $systemConfig->setDeepSleepEnabled(false);
            $systemConfig->setWebServerEnabled(true);
            $systemConfig->setWebServerPort(80);
            $esp32Config->setSystemConfig($systemConfig);
            $manager->persist($systemConfig);
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
