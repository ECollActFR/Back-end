# CLAUDE.md - Backend Development Guide

This file provides guidance for Claude Code when working with the Neutria backend Symfony application.

## Project Overview

The Neutria backend is a **single Symfony application** with multi-tenant architecture that provides:

- **Environmental monitoring API** for IoT sensor data
- **Multi-tenant client isolation** with role-based access control
- **ESP32 device configuration management** for IoT acquisition systems
- **RESTful API** with automatic CRUD operations via API Platform
- **JWT authentication** with secure token-based authentication

## Architecture

### Core Components

- **API Platform 4.2** - REST and GraphQL API framework with automatic documentation
- **Doctrine ORM 3.5** - Database abstraction with multi-tenant support
- **Symfony 7.3** - Modern PHP framework with security components
- **LexikJWTAuthenticationBundle** - JWT token authentication
- **Multi-tenant isolation** - Client account-based data separation

### Entity Relationships

```
ClientAccount (Multi-tenant root)
├── User[] (Assigned to client account)
├── Building[] (Belongs to client account)
│   └── Room[] (Belongs to building)
│       ├── Capture[] (Environmental data)
│       ├── Equipment[] (Room equipment)
│       └── AcquisitionSystem[] (ESP32 devices)
│           ├── DeviceNetworkConfig (WiFi/NTP config)
│           ├── DeviceSystemConfig (System settings)
│           ├── DeviceSensor[] (Sensor configurations)
│           └── DeviceTask[] (Scheduled tasks)
└── CaptureType[] (Global sensor types)
```

## Key Features

### 1. ESP32 Device Configuration

The system provides comprehensive ESP32 device management:

- **DeviceNetworkConfig**: WiFi SSID, NTP server, timezone configuration
- **DeviceSystemConfig**: Debug mode, buffer size, deep sleep, web server settings
- **DeviceSensor**: Sensor types (AHT20, MQ135, BH1750, sound), pins, intervals
- **DeviceTask**: Scheduled tasks (sensor_read, api_post, display, blink, custom)

### 2. Multi-Tenant Architecture

- **ClientAccount**: Root entity for tenant isolation
- **Automatic filtering**: API Platform filters all queries by client account
- **Super Admin override**: ROLE_SUPER_ADMIN can access all client data
- **Voter-based permissions**: Fine-grained access control per entity

### 3. Configuration Endpoints

Special endpoints for device configuration:
- `GET /acquisition_systems/{id}/configuration` - Get complete device config
- `POST /acquisition_systems/{id}/configuration` - Update complete config
- `PATCH /acquisition_systems/{id}/configuration` - Partial update config

## Development Guidelines

### Entity Development

1. **Always add ClientAccount relation** for multi-tenant entities
2. **Use API Platform attributes** for automatic API generation
3. **Add validation groups** for complex validation scenarios
4. **Implement proper serialization groups** for nested relationships

Example entity pattern:
```php
#[ORM\Entity]
#[ApiResource(
    normalizationContext: ['groups' => ['entity:read']],
    denormalizationContext: ['groups' => ['entity:write']]
)]
class NewEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['entity:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'newEntities')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['entity:read', 'entity:write'])]
    private ?ClientAccount $clientAccount = null;
}
```

### Security & Access Control

1. **Create Voters** for each entity type that needs custom permissions
2. **Extend AbstractVoter** and implement `supports()` and `voteOnAttribute()`
3. **Use ClientAccountAccessService** for automatic client account filtering
4. **Super Admin bypass** - always allow access for ROLE_SUPER_ADMIN

Example voter pattern:
```php
protected function supports(string $attribute, mixed $subject): bool
{
    return in_array($attribute, [self::VIEW, self::EDIT, self::DELETE])
        && $subject instanceof YourEntity;
}

protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
{
    $user = $token->getUser();
    if (!$user instanceof UserInterface) {
        return false;
    }

    // Super admin can do everything
    if (in_array('ROLE_SUPER_ADMIN', $user->getRoles())) {
        return true;
    }

    // Check client account ownership
    return $subject->getClientAccount() === $user->getClientAccount();
}
```

### API Platform Configuration

1. **Use serialization groups** to control what data is exposed
2. **Implement custom processors** for complex business logic
3. **Add custom providers** for specialized queries
4. **Use ItemOperations** and **CollectionOperations** for custom endpoints

### Device Configuration

When working with ESP32 device configuration:

1. **Use hierarchical JSON structure** for device config
2. **Validate sensor pin assignments** (ESP32 pin constraints)
3. **Support partial updates** with PATCH operations
4. **Handle device-specific validation** (I2C pins, ADC pins, etc.)

## Common Development Commands

### Database Operations
```bash
# Create entity
php bin/console make:entity

# Generate migration
php bin/console make:migration

# Run migrations
php bin/console doctrine:migrations:migrate

# Validate schema
php bin/console doctrine:schema:validate

# Load fixtures
php bin/console doctrine:fixtures:load
```

### API Development
```bash
# Debug routes
php bin/console debug:router

# Check API Platform configuration
php bin/console debug:api-platform

# Generate JWT keys
php bin/console lexik:jwt:generate-keypair

# Test API endpoints
curl -X GET "http://localhost:8000/api/rooms" -H "Accept: application/ld+json"
```

### Security Debugging
```bash
# Debug security voters
php bin/console debug:container --tag=voter

# Check user roles
php bin/console debug:security --user=user@example.com

# Test authentication
curl -X POST "http://localhost:8000/api/login_check" \
  -H "Content-Type: application/json" \
  -d '{"username":"user@example.com","password":"password"}'
```

## Testing

### Unit Tests
```bash
# Run all tests
php bin/phpunit

# Run specific test
php bin/phpunit tests/Entity/RoomTest.php

# Run with coverage
php bin/phpunit --coverage-html coverage
```

### API Testing
Use WebTestCase for API endpoint testing:
```php
public function testGetRooms(): void
{
    $client = static::createClient();
    $client->request('GET', '/api/rooms');
    
    $this->assertResponseIsSuccessful();
    $this->assertJsonContains(['@type' => 'hydra:Collection']);
}
```

## Production Considerations

### Performance
- **Enable OPcache** for production PHP performance
- **Use Redis/Memcached** for session storage
- **Implement database indexing** for frequently queried fields
- **Use API Platform pagination** for large collections

### Security
- **Use HTTPS** in production with proper SSL certificates
- **Configure CORS** for specific allowed origins
- **Implement rate limiting** for API endpoints
- **Regular security updates** for dependencies

### Multi-Tenant Best Practices
- **Always validate client account ownership** in custom code
- **Use database constraints** to enforce data integrity
- **Implement proper backup strategies** per client account
- **Monitor resource usage** per tenant

## File Structure Conventions

```
api/src/
├── Controller/           # Custom controllers (beyond API Platform auto-generation)
├── Entity/              # Doctrine entities with API Platform attributes
├── Security/Voter/      # Access control voters for each entity type
├── Service/            # Business logic services
├── State/Processor/    # API Platform processors for business logic
├── Repository/         # Custom Doctrine repositories
└── DataFixtures/       # Test data generation
```

## Integration with ESP32

The backend is designed to work seamlessly with ESP32 acquisition systems:

1. **Device Registration**: ESP32 devices register as AcquisitionSystem entities
2. **Configuration Retrieval**: Devices fetch configuration from dedicated endpoints
3. **Data Transmission**: Sensor data is posted to standard capture endpoints
4. **Status Updates**: Devices update their status and last_seen timestamps

### ESP32 Configuration Flow
1. ESP32 connects to WiFi using DeviceNetworkConfig
2. Device synchronizes time via NTP server
3. Device fetches complete configuration from backend
4. Sensors are initialized based on DeviceSensor configurations
5. Tasks are scheduled according to DeviceTask settings
6. Data is periodically transmitted to backend API

This architecture enables complete remote management of ESP32 IoT devices through the Neutria backend API.