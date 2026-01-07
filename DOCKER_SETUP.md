# Docker Development Setup

## Quick Start

### 1. Configure your environment
Copy the environment template and update it with your user information:

```bash
cp .env.example .env
# Edit .env with your user info:
# USER_ID=$(id -u)
# GROUP_ID=$(id -g) 
# USER_NAME=$(id -un)
# GROUP_NAME=$(id -gn)
```

### 2. Fix existing permissions (if needed)
If you have permission issues with files created by Docker containers:

```bash
./fix-permissions.sh
```

### 3. Start the development environment
```bash
docker-compose up --build
```

## Environment Variables

The `.env` file at the root contains Docker user mapping:

- `USER_ID`: Your user ID (run `id -u` to get it)
- `GROUP_ID`: Your group ID (run `id -g` to get it)  
- `USER_NAME`: Your username (run `id -un` to get it)
- `GROUP_NAME`: Your group name (run `id -gn` to get it)
- `COMPOSE_PROJECT_NAME`: Project name for Docker containers

## Team Setup

Each developer should:

1. Copy `.env.example` to `.env`
2. Update the values with their system user info
3. Never commit `.env` to Git (already in .gitignore)

## Troubleshooting

### Permission Issues
If files are created with `root` ownership:

```bash
./fix-permissions.sh
docker-compose down
docker-compose up --build
```

### Check Current User
```bash
id
# Output: uid=1000(username) gid=1000(username) groups=1000(username)
```

## Daily Usage

```bash
# Start containers
docker-compose up -d

# View logs
docker-compose logs -f php

# Execute commands in container
docker-compose exec php bash

# Stop containers
docker-compose down
```