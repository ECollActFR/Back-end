# Neutria - Environmental Monitoring API

Neutria is a RESTful API built with Symfony and API Platform designed to monitor and track environmental metrics in different rooms or spaces. The system captures various environmental data points like temperature, humidity, CO2 levels, luminosity, and noise levels.

## Overview

Neutria provides a comprehensive solution for monitoring environmental conditions across multiple rooms. Each room can have different types of sensors (capture types), and the system records measurements over time, allowing for historical tracking and analysis.

### Key Features

- **Multi-room monitoring**: Track environmental metrics across multiple rooms/spaces
- **Flexible capture types**: Support for temperature, humidity, CO2, luminosity, and noise measurements
- **Historical data tracking**: Store and retrieve historical environmental data
- **Equipment management**: Associate equipment with rooms to track resources
- **Acquisition systems**: Manage data acquisition systems linked to specific rooms
- **Device configuration**: Complete ESP32 device configuration with sensors, tasks, network, and system settings
- **Multi-tenant architecture**: Client account isolation with role-based access control
- **RESTful API**: Full CRUD operations via API Platform
- **Interactive documentation**: Swagger/OpenAPI documentation auto-generated
- **Super Admin functionality**: Complete system access for administrators
- **JWT authentication**: Secure token-based authentication system

## Technologies Used

- **Symfony 7.3** - Modern PHP framework
- **API Platform 4.2** - REST and GraphQL API framework
- **Doctrine ORM 3.5** - Database abstraction and ORM
- **PHP 8.2+** - Latest PHP version
- **MariaDB/MySQL** - Database server
- **LexikJWTAuthenticationBundle** - JWT authentication
- **Docker & Docker Compose** - Containerization
- **Nginx** - Web server
- **Carbon** - DateTime manipulation library
- **Symfony Security Voters** - Role-based access control system

## Prerequisites

- Docker (version 20.10 or higher)
- Docker Compose (version 2.0 or higher)
- Git
- (Optional) Make

## Installation

### 1. Clone the Repository

```bash
git clone <repository-url>
cd neutria/Back-end
```

### 2. Environment Configuration

The project includes environment templates. The default configuration works out of the box with Docker.

```bash
cd api
cp .env.template .env
```

Key environment variables:
- `APP_ENV`: Application environment (`dev` or `prod`)
- `APP_SECRET`: Application secret key
- `DATABASE_URL`: Database connection string
- `JWT_PASSPHRASE`: Passphrase for JWT encryption

### 3. Start Docker Containers

```bash
docker compose up -d
```

This will start three services:
- **nginx**: Web server (accessible at http://localhost:8000)
- **php**: PHP-FPM service
- **database**: MariaDB database

### 4. Install Dependencies

```bash
docker compose exec php composer install
```

### 5. Database Setup

Create the database and run migrations:

```bash
# Create database
docker compose exec php php bin/console doctrine:database:create

# Run migrations
docker compose exec php php bin/console doctrine:migrations:migrate
```

### 6. Load Sample Data (Optional)

To populate the database with sample data:

```bash
docker compose exec php php bin/console doctrine:fixtures:load
```

This will create:
- 3 client accounts (Neutria SAS, TechCorp, StartUp Innovation)
- 5 users with different roles (including 1 super admin)
- 5 capture types (Temperature, Humidity, CO2, Luminosity, Noise)
- 6 buildings across different client accounts
- 9 rooms with various sensor configurations
- Sample environmental captures for each room
- Equipment and acquisition systems for each room

### 7. Generate JWT Keys (if using authentication)

```bash
docker compose exec php php bin/console lexik:jwt:generate-keypair
```

## Usage

### Access Points

- **API Base URL**: http://localhost:8000
- **API Documentation**: http://localhost:8000/api (Interactive Swagger UI)
- **Database**: localhost:3306

### API Endpoints

#### Rooms

- `GET /api/rooms` - List all rooms
- `GET /api/rooms/{id}` - Get a specific room
- `GET /api/rooms/{id}/last` - Get room with last captures by type
- `POST /api/rooms` - Create a new room
- `PUT /api/rooms/{id}` - Update a room (full)
- `PATCH /api/rooms/{id}` - Update a room (partial)
- `DELETE /api/rooms/{id}` - Delete a room

#### Captures

- `GET /api/captures` - List all captures (paginated)
- `GET /api/captures/{id}` - Get a specific capture

Pagination parameters:
- `?page=1` - Page number
- `?itemsPerPage=30` - Items per page

#### Capture Types

- `GET /api/capture_types` - List all capture types
- `GET /api/capture_types/{id}` - Get a specific capture type
- `POST /api/capture_types` - Create a new capture type
- `PUT /api/capture_types/{id}` - Update a capture type
- `DELETE /api/capture_types/{id}` - Delete a capture type

#### Equipment

- `GET /api/equipment` - List all equipment
- `GET /api/equipment/{id}` - Get specific equipment
- `POST /api/equipment` - Create new equipment
- `PUT /api/equipment/{id}` - Update equipment
- `DELETE /api/equipment/{id}` - Delete equipment

#### Acquisition Systems

- `GET /api/acquisition_systems` - List all acquisition systems
- `GET /api/acquisition_systems/{id}` - Get a specific system
- `POST /api/acquisition_systems` - Create a new system
- `PUT /api/acquisition_systems/{id}` - Update a system
- `DELETE /api/acquisition_systems/{id}` - Delete a system

#### Device Configuration

- `GET /acquisition_systems/{id}/configuration` - Get complete device configuration
- `POST /acquisition_systems/{id}/configuration` - Update complete device configuration
- `PATCH /acquisition_systems/{id}/configuration` - Partial update device configuration

#### Device Sensors

- `GET /api/device_sensors` - List all device sensors
- `GET /api/device_sensors/{id}` - Get a specific device sensor
- `POST /api/device_sensors` - Create a new device sensor
- `PUT /api/device_sensors/{id}` - Update a device sensor
- `DELETE /api/device_sensors/{id}` - Delete a device sensor

#### Device Tasks

- `GET /api/device_tasks` - List all device tasks
- `GET /api/device_tasks/{id}` - Get a specific device task
- `POST /api/device_tasks` - Create a new device task
- `PUT /api/device_tasks/{id}` - Update a device task
- `DELETE /api/device_tasks/{id}` - Delete a device task

#### Device Network Configuration

- `GET /api/device_network_configs` - List all device network configurations
- `GET /api/device_network_configs/{id}` - Get a specific network configuration
- `POST /api/device_network_configs` - Create a new network configuration
- `PUT /api/device_network_configs/{id}` - Update a network configuration
- `DELETE /api/device_network_configs/{id}` - Delete a network configuration

#### Device System Configuration

- `GET /api/device_system_configs` - List all device system configurations
- `GET /api/device_system_configs/{id}` - Get a specific system configuration
- `POST /api/device_system_configs` - Create a new system configuration
- `PUT /api/device_system_configs/{id}` - Update a system configuration
- `DELETE /api/device_system_configs/{id}` - Delete a system configuration

#### Users & Authentication

- `GET /api/users` - List all users (filtered by client account)
- `GET /api/users/{id}` - Get a specific user
- `POST /api/users` - Create a new user
- `PATCH /api/users/{id}` - Update a user (partial)
- `DELETE /api/users/{id}` - Delete a user
- `GET /api/users/me` - Get current authenticated user
- `POST /api/users/{id}/desactivate` - Deactivate a user
- `POST /api/login_check` - Authenticate and get JWT token

#### Buildings

- `GET /api/buildings` - List all buildings (filtered by client account)
- `GET /api/buildings/{id}` - Get a specific building
- `POST /api/buildings` - Create a new building
- `PATCH /api/buildings/{id}` - Update a building (partial)
- `DELETE /api/buildings/{id}` - Delete a building

#### Client Accounts

- `GET /api/client_accounts` - List all client accounts
- `GET /api/client_accounts/{id}` - Get a specific client account
- `POST /api/client_accounts` - Create a new client account
- `PATCH /api/client_accounts/{id}` - Update a client account (partial)
- `DELETE /api/client_accounts/{id}` - Delete a client account

### API Examples

#### Get all rooms with pagination

```bash
curl -X GET "http://localhost:8000/api/rooms?page=1"
```

#### Get a room with its last captures by type

```bash
curl -X GET "http://localhost:8000/api/rooms/1/last"
```

Response example:
```json
{
  "id": 1,
  "name": "Bureau A1",
  "description": "Bureau individuel côté sud",
  "createdAt": "2025-10-10 12:30:00",
  "lastCapturesByType": [
    {
      "type": {
        "id": 1,
        "name": "Temperature",
        "description": "Mesure température en °C"
      },
      "capture": {
        "id": 42,
        "value": "21.5",
        "description": "Température",
        "createdAt": "2025-10-10T12:25:00+00:00",
        "dateCaptured": "2025-10-10T12:25:00+00:00"
      }
    }
  ]
}
```

#### Create a new room

```bash
curl -X POST "http://localhost:8000/api/rooms" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Lab 1",
    "description": "Research laboratory"
  }'
```

#### Get paginated captures

```bash
curl -X GET "http://localhost:8000/api/captures?page=1&itemsPerPage=20"
```

#### Get complete device configuration

```bash
curl -X GET "http://localhost:8000/acquisition_systems/1/configuration" \
  -H "Authorization: Bearer $TOKEN"
```

Response example:
```json
{
  "device": {
    "id": 1,
    "name": "ESP32-Office-01",
    "type": "ESP32_WROOM",
    "firmware_version": "1.0.0",
    "is_active": true,
    "last_seen": "2025-11-30T15:30:00+00:00",
    "room_id": 1
  },
  "network": {
    "wifi": {
      "ssid": "Office_WiFi"
    },
    "ntp": {
      "server": "pool.ntp.org",
      "timezone": "Europe/Paris",
      "gmt_offset_sec": 3600,
      "daylight_offset_sec": 3600
    }
  },
  "sensors": {
    "aht20": {
      "enabled": true,
      "type": "Temperature",
      "sensor_type": "aht20",
      "read_interval_ms": 5000,
      "i2c_sda_pin": 21,
      "i2c_scl_pin": 22
    },
    "mq135": {
      "enabled": true,
      "type": "CO2",
      "sensor_type": "mq135",
      "read_interval_ms": 10000,
      "adc_pin": 34,
      "warmup_duration_sec": 300
    }
  },
  "tasks": {
    "sensor_read": {
      "enabled": true,
      "interval_ms": 1000,
      "priority": 1
    },
    "api_post": {
      "enabled": true,
      "interval_ms": 30000,
      "priority": 2,
      "endpoint": "/api/captures",
      "batch_size": 10
    }
  },
  "system": {
    "debug": false,
    "buffer_size": 100,
    "deep_sleep_enabled": false,
    "web_server_enabled": true,
    "web_server_port": 80
  }
}
```

#### Update device configuration (partial)

```bash
curl -X PATCH "http://localhost:8000/acquisition_systems/1/configuration" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer $TOKEN" \
  -d '{
    "sensors": {
      "aht20": {
        "enabled": false
      }
    },
    "system": {
      "debug": true
    }
  }'
```

#### Authentication Example

```bash
# Login and get JWT token
curl -X POST "http://localhost:8000/api/login_check" \
  -H "Content-Type: application/json" \
  -d '{"username":"alexis.baron.nsd@gmail.com","password":"password"}'

# Use token for authenticated requests
TOKEN="your_jwt_token_here"
curl -X GET "http://localhost:8000/api/users" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Accept: application/ld+json"
```

#### Super Admin Access Example

```bash
# Super admin can see all resources across all client accounts
curl -X GET "http://localhost:8000/api/rooms" \
  -H "Authorization: Bearer $SUPER_ADMIN_TOKEN" \
  -H "Accept: application/ld+json"

# Normal user only sees resources from their client account
curl -X GET "http://localhost:8000/api/rooms" \
  -H "Authorization: Bearer $NORMAL_USER_TOKEN" \
  -H "Accept: application/ld+json"
```

## Authentication & Authorization

### Multi-Tenant Architecture

Neutria implements a robust multi-tenant architecture through the `ClientAccount` entity:

- **Client Isolation**: Each client account has completely isolated data
- **User Assignment**: Users are assigned to specific client accounts
- **Resource Scoping**: All resources (rooms, buildings, devices, captures) belong to a client account
- **Super Admin Override**: Super admins can access all client accounts for system management

### User Roles

The system implements a hierarchical role-based access control:

- **ROLE_USER**: Basic authenticated user access within their client account
- **ROLE_ADMIN**: Administrative access within their client account
- **ROLE_SUPER_ADMIN**: Full system access across all client accounts

### Access Control

- **Multi-tenant isolation**: Users can only access resources from their client account
- **Super Admin override**: Users with `ROLE_SUPER_ADMIN` can access all resources
- **JWT authentication**: Secure token-based authentication
- **Voter-based authorization**: Fine-grained permission control per entity type
- **Automatic filtering**: API Platform automatically filters queries by client account

### Default Users (from fixtures)

| Email | Role | Client Account | Access Level |
|-------|------|----------------|---------------|
| alexis.baron.nsd@gmail.com | **SUPER_ADMIN** | Neutria SAS | **All resources** |
| marie.dupont@example.com | USER | Neutria SAS | Neutria SAS resources only |
| thomas.martin@techcorp.com | USER | TechCorp | TechCorp resources only |
| sophie.bernard@techcorp.com | USER | TechCorp | TechCorp resources only |
| lucas.petit@startup.fr | USER | StartUp Innovation | StartUp resources only |

## ESP32 Device Configuration Architecture

The Neutria backend provides comprehensive configuration management for ESP32 acquisition systems through a hierarchical structure:

### Configuration Hierarchy

```
AcquisitionSystem (Device)
├── DeviceNetworkConfig (WiFi, NTP, Timezone)
├── DeviceSystemConfig (Debug, Buffer, Sleep, Web Server)
├── DeviceSensor[] (Multiple sensors per device)
│   ├── I2C Sensors (AHT20, BH1750)
│   ├── ADC Sensors (MQ135, Sound)
│   └── Digital Sensors (Generic)
└── DeviceTask[] (Scheduled tasks)
    ├── sensor_read (Data collection)
    ├── api_post (Data transmission)
    ├── display (LED/LCD output)
    ├── blink (Status indicators)
    └── custom (User-defined tasks)
```

### Supported Sensor Types

| Sensor Type | Data Type | Interface | Pins Required | Use Case |
|-------------|-----------|-----------|---------------|----------|
| **aht20** | Temperature & Humidity | I2C | SDA, SCL | Environmental monitoring |
| **mq135** | Air Quality (CO2) | ADC | ADC pin | Gas detection |
| **bh1750** | Light Intensity | I2C | SDA, SCL | Luminosity measurement |
| **sound_sensor** | Noise Level | ADC | ADC pin | Sound monitoring |
| **generic** | Custom | Digital/ADC | Configurable | Custom sensors |

### Task System

The ESP32 firmware supports a priority-based task scheduler:

- **sensor_read**: High priority (1), reads all enabled sensors
- **api_post**: Medium priority (2-5), posts data to backend API
- **display**: Low priority (6-8), handles local display updates
- **blink**: Low priority (9), status LED management
- **custom**: User-defined priority and configuration

### Configuration Workflow

1. **Device Registration**: ESP32 registers with backend via acquisition system endpoint
2. **Configuration Retrieval**: Device fetches complete configuration from `/acquisition_systems/{id}/configuration`
3. **Sensor Initialization**: Configure I2C/ADC pins based on sensor definitions
4. **Task Scheduling**: Start scheduled tasks with specified intervals
5. **Data Transmission**: Periodically send sensor data to backend API
6. **Configuration Updates**: Device can receive configuration updates via API

### JSON Configuration Format

The device configuration is structured as hierarchical JSON:

```json
{
  "device": { "id": 1, "name": "ESP32-Office-01" },
  "network": {
    "wifi": { "ssid": "NetworkName" },
    "ntp": { "server": "pool.ntp.org", "timezone": "Europe/Paris" }
  },
  "sensors": {
    "aht20": {
      "sensor_type": "aht20",
      "type": "Temperature",
      "i2c_sda_pin": 21,
      "i2c_scl_pin": 22,
      "read_interval_ms": 5000
    }
  },
  "tasks": {
    "sensor_read": { "interval_ms": 1000, "priority": 1 },
    "api_post": { "interval_ms": 30000, "priority": 2 }
  },
  "system": {
    "debug": false,
    "buffer_size": 100,
    "web_server_enabled": true
  }
}
```

## Data Model

### Room
Represents a physical space being monitored.

**Attributes:**
- `id`: Unique identifier
- `name`: Room name (max 15 characters)
- `description`: Room description (optional)
- `createdAt`: Creation timestamp
- `captureTypes`: Associated capture types (many-to-many)
- `captures`: Environmental captures (one-to-many)
- `equipment`: Equipment in the room (many-to-many)
- `acquisitionSystems`: Data acquisition systems (one-to-many)

### Capture
Represents a single environmental measurement.

**Attributes:**
- `id`: Unique identifier
- `value`: Measurement value (decimal 6,2)
- `description`: Capture description
- `room`: Associated room
- `type`: Type of capture (temperature, humidity, etc.)
- `createdAt`: Record creation timestamp
- `dateCaptured`: When the measurement was taken

### CaptureType
Defines types of environmental measurements.

**Attributes:**
- `id`: Unique identifier
- `name`: Type name (max 50 characters)
- `description`: Type description
- `createdAt`: Creation timestamp

**Default types:**
- Temperature (°C)
- Humidity (%)
- CO2 (ppm)
- Luminosity (lux)
- Noise (dB)

### Equipment
Represents equipment or devices in rooms.

**Attributes:**
- `id`: Unique identifier
- `name`: Equipment name
- `capacity`: Equipment capacity (optional)
- `createdAt`: Creation timestamp
- `rooms`: Associated rooms (many-to-many)

### AcquisitionSystem
Represents data acquisition systems attached to rooms.

**Attributes:**
- `id`: Unique identifier
- `name`: System name
- `room`: Associated room
- `deviceType`: Type of device (e.g., ESP32_WROOM)
- `firmwareVersion`: Firmware version
- `isActive`: Whether the system is active
- `lastSeen`: Last time the device was seen
- `createdAt`: Creation timestamp
- `networkConfig`: Network configuration (one-to-one)
- `systemConfig`: System configuration (one-to-one)
- `sensors`: Device sensors (one-to-many)
- `tasks`: Device tasks (one-to-many)

### DeviceSensor
Represents sensor configurations for ESP32 devices.

**Attributes:**
- `id`: Unique identifier
- `acquisitionSystem`: Associated acquisition system
- `captureType`: Type of environmental data captured
- `sensorType`: Hardware sensor type (aht20, mq135, bh1750, sound_sensor, generic)
- `enabled`: Whether the sensor is active
- `readIntervalMs`: Reading interval in milliseconds
- `i2cSdaPin`: I2C SDA pin (for I2C sensors)
- `i2cSclPin`: I2C SCL pin (for I2C sensors)
- `adcPin`: ADC pin (for analog sensors)
- `warmupDurationSec`: Warmup duration in seconds (for gas sensors)

### DeviceTask
Represents scheduled tasks for ESP32 devices.

**Attributes:**
- `id`: Unique identifier
- `acquisitionSystem`: Associated acquisition system
- `taskName`: Task type (sensor_read, api_post, display, blink, custom)
- `enabled`: Whether the task is active
- `intervalMs`: Execution interval in milliseconds
- `priority`: Task priority (1-10, lower number = higher priority)
- `taskConfig`: Additional task-specific configuration (JSON)

### DeviceNetworkConfig
Represents network configuration for ESP32 devices.

**Attributes:**
- `id`: Unique identifier
- `acquisitionSystem`: Associated acquisition system
- `wifiSsid`: WiFi network name
- `ntpServer`: NTP server for time synchronization
- `timezone`: Device timezone
- `gmtOffsetSec`: GMT offset in seconds
- `daylightOffsetSec`: Daylight saving time offset in seconds

### DeviceSystemConfig
Represents system configuration for ESP32 devices.

**Attributes:**
- `id`: Unique identifier
- `acquisitionSystem`: Associated acquisition system
- `debug`: Debug mode enabled
- `bufferSize`: Data buffer size
- `deepSleepEnabled`: Deep sleep mode enabled
- `webServerEnabled`: Built-in web server enabled
- `webServerPort`: Web server port

### User
Represents system users with role-based access.

**Attributes:**
- `id`: Unique identifier
- `email`: User email (unique)
- `roles`: User roles (ROLE_USER, ROLE_ADMIN, ROLE_SUPER_ADMIN)
- `firstname`: First name
- `lastname`: Last name
- `phone`: Phone number (optional)
- `isActive`: Account status
- `emailVerified`: Email verification status
- `createdAt`: Creation timestamp
- `lastLogin`: Last login timestamp
- `clientAccount`: Associated client account (optional for super admins)

### Building
Represents physical buildings containing rooms.

**Attributes:**
- `id`: Unique identifier
- `name`: Building name
- `owner`: User who owns the building
- `createdAt`: Creation timestamp

### ClientAccount
Represents client organizations for multi-tenant isolation.

**Attributes:**
- `id`: Unique identifier
- `companyName`: Company name
- `siret`: Company registration number
- `address`: Physical address
- `city`: City
- `postalCode`: Postal code
- `country`: Country
- `phone`: Company phone
- `contactEmail`: Contact email
- `isActive`: Account status
- `createdAt`: Creation timestamp
- `updatedAt`: Last update timestamp
- `users`: Associated users (one-to-many)
- `buildings`: Associated buildings (one-to-many)
- `rooms`: Associated rooms (through buildings)
- `acquisitionSystems`: Associated devices (through rooms)

## Project Structure

```
.
├── api/                          # Symfony application
│   ├── bin/                      # Console commands
│   ├── config/                   # Configuration files
│   │   ├── packages/             # Bundle configuration
│   │   ├── routes/               # Route definitions
│   │   └── jwt/                  # JWT keys (gitignored)
│   ├── migrations/               # Database migrations
│   ├── public/                   # Public web directory
│   ├── src/
│   │   ├── Controller/           # Custom controllers
│   │   │   ├── RoomController.php
│   │   │   ├── UsersController.php
│   │   │   └── AcquisitionSystemConfigController.php
│   │   ├── DataFixtures/         # Database fixtures
│   │   │   └── AppFixtures.php
│   │   ├── Entity/               # Doctrine entities
│   │   │   ├── Room.php
│   │   │   ├── Capture.php
│   │   │   ├── CaptureType.php
│   │   │   ├── Equipment.php
│   │   │   ├── AcquisitionSystem.php
│   │   │   ├── DeviceSensor.php
│   │   │   ├── DeviceTask.php
│   │   │   ├── DeviceNetworkConfig.php
│   │   │   ├── DeviceSystemConfig.php
│   │   │   ├── User.php
│   │   │   ├── Building.php
│   │   │   └── ClientAccount.php
│   │   ├── Security/             # Security components
│   │   │   └── Voter/           # Access control voters
│   │   │       ├── SuperAdminVoter.php
│   │   │       ├── RoomVoter.php
│   │   │       ├── BuildingVoter.php
│   │   │       ├── UserVoter.php
│   │   │       ├── AcquisitionSystemVoter.php
│   │   │       ├── CaptureVoter.php
│   │   │       ├── EquipmentVoter.php
│   │   │       └── ClientAccountVoter.php
│   │   ├── Service/              # Business logic services
│   │   │   └── ClientAccountAccessService.php
│   │   ├── State/                # API Platform state providers
│   │   │   ├── Provider/         # Custom data providers
│   │   │   └── Processor/       # Custom data processors
│   │   │       ├── CaptureTypeProcessor.php
│   │   │       ├── ClientAccountProcessor.php
│   │   │       ├── EquipmentProcessor.php
│   │   │       └── UserProcessor.php
│   │   ├── EventSubscriber/      # Event subscribers
│   │   │   └── ApiResponseSubscriber.php
│   │   └── Repository/           # Doctrine repositories
│   │       ├── DeviceNetworkConfigRepository.php
│   │       ├── DeviceSystemConfigRepository.php
│   │       ├── DeviceTaskRepository.php
│   │       └── DeviceSensorRepository.php
│   ├── var/                      # Cache and logs
│   ├── vendor/                   # Composer dependencies
│   ├── .env                      # Environment variables
│   └── composer.json             # PHP dependencies
├── build/                        # Docker build files
│   ├── nginx/                    # Nginx configuration
│   ├── php/                      # PHP-FPM configuration
│   └── database/                 # Database configuration
├── compose.yaml                  # Docker Compose configuration
└── README.md                     # This file
```

## Development

### Useful Commands

#### Doctrine

```bash
# Create a new entity
docker compose exec php php bin/console make:entity

# Generate a migration after entity changes
docker compose exec php php bin/console make:migration

# Execute pending migrations
docker compose exec php php bin/console doctrine:migrations:migrate

# Check database schema sync
docker compose exec php php bin/console doctrine:schema:validate
```

#### Cache Management

```bash
# Clear cache
docker compose exec php php bin/console cache:clear

# Warm up cache
docker compose exec php php bin/console cache:warmup
```

#### Database Management

```bash
# Drop database (WARNING: deletes all data)
docker compose exec php php bin/console doctrine:database:drop --force

# Create database
docker compose exec php php bin/console doctrine:database:create

# Load fixtures (sample data)
docker compose exec php php bin/console doctrine:fixtures:load --no-interaction
```

#### Debugging

```bash
# View application logs
docker compose logs -f php

# View nginx logs
docker compose logs -f nginx

# View database logs
docker compose logs -f database

# List all routes
docker compose exec php php bin/console debug:router

# Check container configuration
docker compose exec php php bin/console debug:config

# Access PHP container shell
docker compose exec php bash
```

### Adding New Entities

1. Generate entity:
```bash
docker compose exec php php bin/console make:entity EntityName
```

2. Create migration:
```bash
docker compose exec php php bin/console make:migration
```

3. Run migration:
```bash
docker compose exec php php bin/console doctrine:migrations:migrate
```

4. Update fixtures if needed (`api/src/DataFixtures/AppFixtures.php`)

### Custom Endpoints

Custom endpoints are defined in controllers (e.g., `RoomController.php:21`). The `/api/rooms/{id}/last` endpoint demonstrates how to create custom operations that return aggregated data.

## Security & Access Control

### Authentication
- JWT keys are auto-generated and stored in `api/config/jwt/`
- Token-based authentication with configurable expiration
- **Never commit** sensitive files:
  - `.env.local`
  - `config/jwt/private.pem`
  - `config/jwt/public.pem`

### Authorization
- **Multi-tenant isolation**: Users can only access resources from their client account
- **Role-based access control**: Hierarchical permissions (USER < ADMIN < SUPER_ADMIN)
- **Super Admin override**: Users with `ROLE_SUPER_ADMIN` can access all resources
- **Voter-based permissions**: Fine-grained access control for each entity type

### Security Best Practices
- Database credentials are defined in `compose.yaml` (change for production)
- CORS configuration in `config/packages/nelmio_cors.yaml`
- Use strong passwords and secrets in production environments
- Regular security updates for dependencies
- Monitor access logs for suspicious activity

### Super Admin Capabilities
Users with `ROLE_SUPER_ADMIN` can:
- View, create, update, and delete **all resources** across all client accounts
- Access all rooms, buildings, users, captures, and equipment
- Bypass client account isolation
- Manage all user accounts and client accounts
- Access system-wide administrative functions

### Normal User Limitations
Users with `ROLE_USER` can:
- Only access resources within their client account
- View and manage their own rooms and buildings
- See captures from their rooms only
- Cannot access other client accounts' data

## Production Deployment

### Preparation

1. Update environment variables:
```bash
APP_ENV=prod
APP_DEBUG=0
```

2. Optimize Composer autoloader:
```bash
composer install --no-dev --optimize-autoloader
```

3. Clear and warm up cache:
```bash
php bin/console cache:clear --env=prod
php bin/console cache:warmup --env=prod
```

4. Set proper file permissions:
```bash
chmod -R 755 var/cache var/log
```

### Recommendations

- Use environment variables for sensitive configuration
- Enable HTTPS/SSL
- Configure proper database backups
- Implement rate limiting
- Monitor application logs
- Use a process manager (e.g., supervisord) for PHP-FPM
- Consider using Redis/Memcached for caching
- Implement proper monitoring (e.g., Prometheus, Grafana)

## Testing

```bash
# Run tests (when implemented)
docker compose exec php php bin/phpunit
```

### Testing Super Admin Functionality

```bash
# Test super admin login
SUPER_TOKEN=$(curl -X POST "http://localhost:8000/api/login_check" \
  -H "Content-Type: application/json" \
  -d '{"username":"alexis.baron.nsd@gmail.com","password":"password"}' \
  | jq -r '.data.token')

# Test super admin access to all resources
curl -X GET "http://localhost:8000/api/rooms" \
  -H "Authorization: Bearer $SUPER_TOKEN" \
  -H "Accept: application/ld+json" | jq '.totalItems'

# Test normal user (limited access)
NORMAL_TOKEN=$(curl -X POST "http://localhost:8000/api/login_check" \
  -H "Content-Type: application/json" \
  -d '{"username":"thomas.martin@techcorp.com","password":"password"}' \
  | jq -r '.data.token')

curl -X GET "http://localhost:8000/api/rooms" \
  -H "Authorization: Bearer $NORMAL_TOKEN" \
  -H "Accept: application/ld+json" | jq '.totalItems'
```

## Troubleshooting

### Database Connection Issues

```bash
# Check database service status
docker compose ps database

# Verify database credentials in .env match compose.yaml
cat api/.env | grep DATABASE_URL
```

### Permission Issues

```bash
# Fix cache/log permissions
docker compose exec php chmod -R 777 var/cache var/log
```

### API Platform Not Showing

```bash
# Clear cache
docker compose exec php php bin/console cache:clear

# Check routes
docker compose exec php php bin/console debug:router | grep api
```

## Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

### Code Standards

- Follow PSR-12 coding standards
- Use type hints for parameters and return types
- Document complex logic with comments
- Write meaningful commit messages

## Performance & Optimization

### Database Optimization

#### Indexing Strategy
- **Primary indexes** on all entity IDs and foreign keys
- **Composite indexes** on frequently queried combinations (client_account + created_at)
- **JSON indexes** on device configuration fields for faster ESP32 config retrieval
- **Time-series optimization** on capture tables with partitioning by date

```sql
-- Example optimized indexes for captures
CREATE INDEX idx_capture_client_time ON capture(client_account_id, captured_at DESC);
CREATE INDEX idx_capture_sensor_type ON capture(capture_type_id, captured_at DESC);
CREATE INDEX idx_capture_room_time ON capture(room_id, captured_at DESC);
```

#### Query Optimization
- **Use Doctrine's DQL** for complex queries instead of raw SQL
- **Implement pagination** for large datasets using API Platform's built-in pagination
- **Cache frequently accessed data** with Redis or Memcached
- **Use batch processing** for bulk operations to avoid memory issues

```php
// Optimized query example
$captures = $captureRepository->findBy(
    ['clientAccount' => $clientAccount],
    ['capturedAt' => 'DESC'],
    100, // limit
    0    // offset
);
```

### API Performance

#### Response Optimization
- **Use serialization groups** to minimize response payloads
- **Implement HTTP caching** with ETags and Last-Modified headers
- **Enable gzip compression** for API responses
- **Use API Platform filters** efficiently to reduce database load

#### Caching Strategy
```yaml
# config/packages/cache.yaml
framework:
    cache:
        app: cache.adapter.redis
        system: cache.adapter.redis
        default_redis_provider: redis://localhost:6379
```

### ESP32 Device Optimization

#### Configuration Caching
- **Cache device configurations** locally on ESP32 to reduce API calls
- **Implement delta updates** - only send changed configuration parameters
- **Use configuration versioning** to detect when updates are needed
- **Batch sensor readings** to reduce network overhead

#### Data Transmission
- **Compress sensor data** before transmission using lightweight algorithms
- **Implement retry logic** with exponential backoff for failed transmissions
- **Use WebSocket connections** for real-time configuration updates
- **Buffer data locally** when network is unavailable

## API Versioning & Compatibility

### Versioning Strategy

The Neutria API follows **semantic versioning** with URL-based versioning:

```
/api/v1/rooms          # Version 1 (current stable)
/api/v2/rooms          # Version 2 (future features)
/api/rooms             # Defaults to latest stable version
```

### Backward Compatibility

#### Breaking Changes Policy
- **Major version bumps** for breaking changes (v1 → v2)
- **Minor version bumps** for new features (v1.1 → v1.2)
- **Patch versions** for bug fixes (v1.1.1 → v1.1.2)

#### Deprecation Process
1. **Announce deprecation** 6 months before removal
2. **Add deprecation headers** to API responses
3. **Provide migration guides** for affected endpoints
4. **Maintain old versions** for at least 12 months

```php
// Example deprecation header
header('Deprecation: true');
header('Sunset: ' . date('c', strtotime('+6 months')));
header('Link: </api/v2/rooms>; rel="successor-version"');
```

### Compatibility Matrix

| API Version | PHP Version | Symfony Version | Status |
|-------------|-------------|-----------------|---------|
| v1.x        | 8.1+        | 7.3+            | ✅ Stable |
| v2.x        | 8.2+        | 7.4+            | 🚧 Development |
| v3.x        | 8.3+        | 8.0+            | 📋 Planned |

### Client Library Support

#### Official SDKs
- **PHP SDK** - Full feature support, maintained by Neutria team
- **JavaScript/TypeScript SDK** - Browser and Node.js support
- **Python SDK** - For data analysis and automation
- **ESP32 Arduino Library** - Optimized for IoT devices

#### Third-party Integration
- **REST API** - Standard HTTP/JSON interface
- **GraphQL** - Query optimization for complex data needs
- **WebHooks** - Real-time event notifications
- **WebSocket API** - Live data streaming

## Advanced Usage Examples

### Multi-Tenant Data Analysis

#### Cross-Tenant Analytics (Super Admin)
```bash
# Get sensor data across all clients
curl -X GET "http://localhost:8000/api/analytics/sensor-data" \
  -H "Authorization: Bearer SUPER_ADMIN_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "startDate": "2024-01-01",
    "endDate": "2024-12-31",
    "sensorTypes": ["temperature", "humidity"],
    "groupBy": "client_account"
  }'
```

#### Custom Dashboard Integration
```javascript
// React component for real-time sensor data
function SensorDashboard({ clientId }) {
  const [data, setData] = useState([]);
  
  useEffect(() => {
    const ws = new WebSocket(`ws://localhost:8000/ws/sensors/${clientId}`);
    
    ws.onmessage = (event) => {
      const sensorData = JSON.parse(event.data);
      setData(prev => [...prev.slice(-99), sensorData]);
    };
    
    return () => ws.close();
  }, [clientId]);
  
  return <SensorChart data={data} />;
}
```

### ESP32 Advanced Configuration

#### Custom Sensor Integration
```cpp
// ESP32 custom sensor implementation
class CustomSensor : public DeviceSensor {
private:
    int pin;
    float calibrationOffset;
    
public:
    CustomSensor(int sensorPin, float offset) 
        : pin(sensorPin), calibrationOffset(offset) {}
    
    SensorData read() override {
        float rawValue = analogRead(pin);
        float calibratedValue = rawValue * 3.3 / 4096 + calibrationOffset;
        
        return SensorData{
            .type = "custom_sensor",
            .value = calibratedValue,
            .unit = "V",
            .timestamp = millis()
        };
    }
};
```

#### Advanced Task Scheduling
```json
{
  "deviceTasks": [
    {
      "name": "adaptive_sensor_read",
      "type": "custom",
      "schedule": {
        "type": "adaptive",
        "baseInterval": 60000,
        "maxInterval": 300000,
        "triggerConditions": [
          {"type": "value_change", "threshold": 0.5},
          {"type": "time_window", "start": "08:00", "end": "18:00"}
        ]
      },
      "actions": [
        {"type": "read_sensors", "sensors": ["temperature", "humidity"]},
        {"type": "transmit_data", "endpoint": "/api/captures"},
        {"type": "update_display", "template": "current_readings"}
      ]
    }
  ]
}
```

### Custom API Endpoints

#### Advanced Filtering and Aggregation
```php
// Custom controller for advanced analytics
#[Route('/api/analytics')]
class AnalyticsController extends AbstractController {
    
    #[Route('/sensor-aggregation', methods: ['POST'])]
    public function getSensorAggregation(Request $request): JsonResponse {
        $data = json_decode($request->getContent(), true);
        
        $query = $this->entityManager->createQuery('
            SELECT 
                ct.name as sensorType,
                AVG(c.value) as avgValue,
                MIN(c.value) as minValue,
                MAX(c.value) as maxValue,
                COUNT(c.id) as sampleCount
            FROM App\Entity\Capture c
            JOIN c.captureType ct
            JOIN c.room r
            JOIN r.building b
            WHERE b.clientAccount = :clientAccount
            AND c.capturedAt BETWEEN :startDate AND :endDate
            AND ct.name IN (:sensorTypes)
            GROUP BY ct.name
        ');
        
        $query->setParameters([
            'clientAccount' => $this->getUser()->getClientAccount(),
            'startDate' => new DateTime($data['startDate']),
            'endDate' => new DateTime($data['endDate']),
            'sensorTypes' => $data['sensorTypes']
        ]);
        
        return new JsonResponse($query->getResult());
    }
}
```

### Batch Operations

#### Bulk Device Configuration
```bash
# Update multiple ESP32 devices simultaneously
curl -X POST "http://localhost:8000/api/bulk/device-configuration" \
  -H "Authorization: Bearer ADMIN_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "deviceIds": ["device_001", "device_002", "device_003"],
    "configuration": {
      "network": {
        "wifiSSID": "UpdatedNetwork",
        "ntpServer": "pool.ntp.org"
      },
      "sensors": [
        {
          "type": "AHT20",
          "pin": 21,
          "interval": 30000
        }
      ]
    },
    "options": {
      "validateOnly": false,
      "restartDevices": true,
      "rollbackOnError": true
    }
  }'
```

## Development Best Practices

### Code Organization

#### Entity Design Principles
- **Single Responsibility**: Each entity should have one clear purpose
- **Consistent Naming**: Use descriptive, consistent naming conventions
- **Proper Relationships**: Define clear relationships with appropriate cascade options
- **Validation Groups**: Use validation groups for different contexts (create, update, etc.)

```php
// Example well-structured entity
#[ORM\Entity]
#[ApiResource(
    normalizationContext: ['groups' => ['sensor:read']],
    denormalizationContext: ['groups' => ['sensor:write']],
    validationContext: ['groups' => ['Default', 'sensor:validation']]
)]
class DeviceSensor {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['sensor:read'])]
    private ?int $id = null;
    
    #[ORM\Column(length: 50)]
    #[Groups(['sensor:read', 'sensor:write'])]
    #[Assert\NotBlank(groups: ['sensor:validation'])]
    private ?string $type = null;
    
    #[ORM\Column]
    #[Groups(['sensor:read', 'sensor:write'])]
    #[Assert\Range(min: 0, max: 39, groups: ['sensor:validation'])]
    private ?int $pin = null;
}
```

#### Service Layer Architecture
- **Business Logic Separation**: Keep business logic out of controllers
- **Dependency Injection**: Use constructor injection for better testability
- **Interface Segregation**: Create specific interfaces for different concerns
- **Error Handling**: Implement consistent error handling across services

```php
// Example service with proper architecture
interface DeviceConfigurationServiceInterface {
    public function getConfiguration(AcquisitionSystem $device): array;
    public function updateConfiguration(AcquisitionSystem $device, array $config): bool;
    public function validateConfiguration(array $config): ConstraintViolationListInterface;
}

class DeviceConfigurationService implements DeviceConfigurationServiceInterface {
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ValidatorInterface $validator,
        private CacheInterface $cache
    ) {}
    
    public function getConfiguration(AcquisitionSystem $device): array {
        $cacheKey = "device_config_{$device->getId()}";
        
        return $this->cache->get($cacheKey, function() use ($device) {
            return $this->buildConfiguration($device);
        });
    }
}
```

### Testing Strategies

#### Unit Testing
```php
// Example unit test for service
class DeviceConfigurationServiceTest extends TestCase {
    private DeviceConfigurationService $service;
    private EntityManagerInterface&MockObject $entityManager;
    
    protected function setUp(): void {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->service = new DeviceConfigurationService(
            $this->entityManager,
            $this->createMock(ValidatorInterface::class),
            $this->createMock(CacheInterface::class)
        );
    }
    
    public function testGetConfigurationReturnsValidStructure(): void {
        $device = new AcquisitionSystem();
        $device->setId(1);
        
        $config = $this->service->getConfiguration($device);
        
        $this->assertArrayHasKey('network', $config);
        $this->assertArrayHasKey('sensors', $config);
        $this->assertArrayHasKey('tasks', $config);
    }
}
```

#### Integration Testing
```php
// Example API integration test
class DeviceConfigurationApiTest extends ApiTestCase {
    public function testGetDeviceConfiguration(): void {
        $client = static::createClient();
        $user = $this->createUserWithRole('ROLE_USER');
        
        $device = $this->createAcquisitionSystem($user->getClientAccount());
        
        $client->loginUser($user);
        $client->request('GET', "/api/acquisition_systems/{$device->getId()}/configuration");
        
        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            'network' => ['wifiSSID' => 'TestNetwork'],
            'sensors' => [],
            'tasks' => []
        ]);
    }
}
```

### Security Best Practices

#### Input Validation
```php
// Example comprehensive validation
class DeviceConfigurationDto {
    #[Assert\NotBlank]
    #[Assert\Type('array')]
    public array $network;
    
    #[Assert\Type('array')]
    #[Assert\All([
        new Assert\Collection([
            'type' => [new Assert\NotBlank()],
            'pin' => [new Assert\Type('integer'), new Assert\Range(['min' => 0, 'max' => 39])],
            'interval' => [new Assert\Type('integer'), new Assert\Positive()]
        ])
    ])]
    public array $sensors = [];
}
```

#### Access Control Implementation
```php
// Example voter with comprehensive checks
class AcquisitionSystemVoter extends AbstractVoter {
    protected function supports(string $attribute, mixed $subject): bool {
        return in_array($attribute, [self::VIEW, self::EDIT, self::DELETE])
            && $subject instanceof AcquisitionSystem;
    }
    
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool {
        $user = $token->getUser();
        if (!$user instanceof UserInterface) {
            return false;
        }
        
        // Super admin bypass
        if (in_array('ROLE_SUPER_ADMIN', $user->getRoles())) {
            return true;
        }
        
        // Client account ownership check
        if ($subject->getClientAccount() !== $user->getClientAccount()) {
            return false;
        }
        
        // Additional business logic checks
        return match($attribute) {
            self::VIEW => true,
            self::EDIT => $this->canEdit($subject, $user),
            self::DELETE => $this->canDelete($subject, $user),
            default => false
        };
    }
}
```

### Performance Monitoring

#### Database Query Optimization
```php
// Example optimized repository method
class CaptureRepository extends ServiceEntityRepository {
    public function findByClientAccountAndDateRange(
        ClientAccount $clientAccount,
        DateTime $startDate,
        DateTime $endDate,
        array $sensorTypes = []
    ): array {
        $qb = $this->createQueryBuilder('c')
            ->join('c.captureType', 'ct')
            ->join('c.room', 'r')
            ->join('r.building', 'b')
            ->where('b.clientAccount = :clientAccount')
            ->andWhere('c.capturedAt BETWEEN :startDate AND :endDate')
            ->setParameter('clientAccount', $clientAccount)
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
            ->orderBy('c.capturedAt', 'DESC');
            
        if (!empty($sensorTypes)) {
            $qb->andWhere('ct.name IN (:sensorTypes)')
               ->setParameter('sensorTypes', $sensorTypes);
        }
        
        return $qb->getQuery()
            ->useQueryCache(true)
            ->useResultCache(true, 3600, 'captures_' . $clientAccount->getId() . '_' . $startDate->format('Y-m-d'))
            ->getResult();
    }
}
```

### Documentation Standards

#### API Documentation
- **OpenAPI Specification**: Maintain comprehensive API documentation
- **Code Examples**: Provide working examples for all endpoints
- **Error Responses**: Document all possible error responses
- **Authentication**: Clearly document authentication requirements

#### Code Documentation
```php
/**
 * Retrieves device configuration for ESP32 devices.
 * 
 * This method compiles a complete configuration structure including
 * network settings, sensor configurations, and scheduled tasks.
 * The configuration is cached for 5 minutes to improve performance.
 * 
 * @param AcquisitionSystem $device The device to configure
 * @return array Complete device configuration
 * @throws DeviceNotFoundException If device is not found
 * @throws ConfigurationException If configuration is invalid
 * 
 * @example
 * $config = $service->getConfiguration($device);
 * echo $config['network']['wifiSSID'];
 */
public function getConfiguration(AcquisitionSystem $device): array;
```

## License

This project is licensed under the GNU General Public License v3.0 (GPL-3.0). See the [LICENSE](LICENSE) file for details.

## Authors

- Project maintained by the Neutria team

## Support

For issues, questions, or contributions, please use the GitHub issue tracker.

## Acknowledgments

- [Symfony](https://symfony.com/)
- [API Platform](https://api-platform.com/)
- [Doctrine ORM](https://www.doctrine-project.org/)
- [LexikJWTAuthenticationBundle](https://github.com/lexik/LexikJWTAuthenticationBundle)
- [Carbon](https://carbon.nesbot.com/)
