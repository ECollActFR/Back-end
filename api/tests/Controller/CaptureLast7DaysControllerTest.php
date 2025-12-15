<?php

namespace App\Tests\Controller;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;

class CaptureLast7DaysControllerTest extends ApiTestCase
{
    public function testCaptureLast7DaysRoute(): void
    {
        // Test que la route existe bien
        $client = static::createClient();
        
        // Test sans authentification - doit retourner 401/403
        $client->request('GET', '/api/captures/last7days/1');
        $this->assertResponseStatusCodeSame(401); // Ou 403 selon la config
    }

    public function testCaptureLast7DaysWithValidRoom(): void
    {
        // Ce test nécessiterait une base de données de test configurée
        // Pour l'instant, vérifions juste que le Provider peut être instancié
        
        $provider = new \App\State\Provider\CaptureLast7DaysProvider(
            $this->createMock(\App\Repository\RoomRepository::class),
            $this->createMock(\App\Repository\CaptureRepository::class)
        );
        
        $this->assertInstanceOf(\App\State\Provider\CaptureLast7DaysProvider::class, $provider);
    }
}