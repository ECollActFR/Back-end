# Neutria API - Production Deployment Guide (Docker)

## Prerequisites

- Docker and Docker Compose installed on your production server
- External MySQL/MariaDB database accessible from your server
- Domain name configured (api.neutria.fr)
- SSL certificates (recommended: use a reverse proxy like Traefik or Nginx Proxy Manager)

## Files Overview

Production deployment uses separate files from development:

- `build/php/Dockerfile.prod` - Production PHP-FPM container with optimizations
- `build/nginx/Dockerfile.prod` - Production Nginx container
- `build/nginx/api.prod.conf` - Production Nginx configuration with security headers
- `compose.prod.yaml` - Production docker-compose file (no database)

## Deployment Steps

### 1. Prepare your environment file

Copy and configure your production environment:

```bash
cp api/.env.prod.example api/.env
```

Edit `api/.env` and update:
- `APP_SECRET` - Generate a strong random secret (use `php bin/console secrets:generate-keys`)
- `DATABASE_URL` - Update with your external database host/credentials
- `JWT_PASSPHRASE` - Set your JWT passphrase
- `CORS_ALLOW_ORIGIN` - Configure allowed origins

Example production `.env`:
```bash
APP_ENV=prod
APP_SECRET=your-generated-secret-key-here
APP_DEBUG=0
DATABASE_URL="mysql://username:password@external-db-host:3306/dbneutria?serverVersion=mariadb-10.11.2&charset=utf8mb4"
CORS_ALLOW_ORIGIN='^https?://(localhost|127\.0\.0\.1|climesense\.fr|.*\.climesense\.fr)(:[0-9]+)?$'
```

### 2. Generate JWT keys (if not already done)

```bash
# Create JWT directory
mkdir -p api/config/jwt

# Generate private key (it will ask for a passphrase - use the same as JWT_PASSPHRASE in .env)
openssl genpkey -out api/config/jwt/private.pem -aes256 -algorithm rsa -pkeyopt rsa_keygen_bits:4096

# Generate public key
openssl pkey -in api/config/jwt/private.pem -out api/config/jwt/public.pem -pubout

# Set proper permissions
chmod 600 api/config/jwt/private.pem
chmod 644 api/config/jwt/public.pem
```

### 3. Configure external database connection

Your `DATABASE_URL` in `api/.env` should point to your external database:

```env
# Example with external database
DATABASE_URL="mysql://userneutria:password@192.168.1.100:3306/dbneutria?serverVersion=mariadb-10.11.2&charset=utf8mb4"

# Or with domain name
DATABASE_URL="mysql://userneutria:password@db.neutria.fr:3306/dbneutria?serverVersion=mariadb-10.11.2&charset=utf8mb4"
```

### 4. Build and start containers

```bash
# Build production images
docker compose -f compose.prod.yaml build --no-cache

# Start services in detached mode
docker compose -f compose.prod.yaml up -d

# Check containers are running
docker compose -f compose.prod.yaml ps
```

### 5. Run database migrations (first deployment only)

```bash
# Create database tables
docker compose -f compose.prod.yaml exec php php bin/console doctrine:migrations:migrate --no-interaction

# Or if migrations don't exist yet
docker compose -f compose.prod.yaml exec php php bin/console doctrine:schema:update --force
```

### 6. Verify deployment

```bash
# Check containers status
docker compose -f compose.prod.yaml ps

# View logs
docker compose -f compose.prod.yaml logs -f

# Test API endpoint
curl http://localhost:8000/

# Check PHP-FPM status
docker compose -f compose.prod.yaml exec php php-fpm -t
```

## Updating the Application

To deploy a new version:

```bash
# Pull latest code
git pull

# Rebuild images with no cache
docker compose -f compose.prod.yaml build --no-cache

# Stop old containers and start new ones
docker compose -f compose.prod.yaml up -d

# Run migrations if needed
docker compose -f compose.prod.yaml exec php php bin/console doctrine:migrations:migrate --no-interaction

# Verify update
docker compose -f compose.prod.yaml logs -f
```

## SSL/HTTPS Configuration

For production, you should use a reverse proxy for SSL termination. Options:

### Option 1: Nginx Reverse Proxy on Host

Create `/etc/nginx/sites-available/api.neutria.fr`:

```nginx
server {
    listen 443 ssl http2;
    server_name api.neutria.fr;

    ssl_certificate /etc/letsencrypt/live/api.neutria.fr/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/api.neutria.fr/privkey.pem;

    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers HIGH:!aNULL:!MD5;

    location / {
        proxy_pass http://localhost:8000;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
        proxy_set_header X-Forwarded-Host $host;
        proxy_set_header X-Forwarded-Port $server_port;
    }
}

# Redirect HTTP to HTTPS
server {
    listen 80;
    server_name api.neutria.fr;
    return 301 https://$server_name$request_uri;
}
```

### Option 2: Traefik (Docker-based)

Update `compose.prod.yaml` nginx service with Traefik labels:

```yaml
nginx:
  # ... existing config ...
  labels:
    - "traefik.enable=true"
    - "traefik.http.routers.neutria-api.rule=Host(`api.neutria.fr`)"
    - "traefik.http.routers.neutria-api.entrypoints=websecure"
    - "traefik.http.routers.neutria-api.tls.certresolver=letsencrypt"
    - "traefik.http.services.neutria-api.loadbalancer.server.port=80"
```

## Monitoring and Logs

```bash
# View all logs
docker compose -f compose.prod.yaml logs -f

# View specific service logs
docker compose -f compose.prod.yaml logs -f nginx
docker compose -f compose.prod.yaml logs -f php

# Monitor resource usage
docker stats

# Check container health
docker compose -f compose.prod.yaml ps
```

## Backup Strategy

Since the database is external, ensure you have backups for:

1. **Database** - Handled by your external DB server
2. **Uploaded files** - Back up the shared volume:
   ```bash
   docker run --rm -v neutria-backend-prod_public-files:/data -v $(pwd):/backup alpine tar czf /backup/public-files-backup.tar.gz -C /data .
   ```
3. **JWT keys** - `api/config/jwt/`
4. **Environment file** - `api/.env`
5. **Application code** - Git repository

## Performance Optimization

The production setup includes:

- **OPcache enabled** - PHP bytecode caching
- **Composer optimized** - Class map optimized for production
- **No dev dependencies** - Smaller image size
- **Symfony cache warmed** - Faster first requests
- **Static file caching** - 1 year cache for assets
- **Health checks** - Automatic container monitoring

## Monitoring & Alerting

### Prometheus Configuration

Create `monitoring/prometheus.yml`:

```yaml
global:
  scrape_interval: 15s
  evaluation_interval: 15s

rule_files:
  - "alert_rules.yml"

scrape_configs:
  - job_name: 'neutria-api'
    static_configs:
      - targets: ['localhost:8000']
    metrics_path: /metrics
    scrape_interval: 30s

  - job_name: 'docker-containers'
    static_configs:
      - targets: ['localhost:9323']
    scrape_interval: 15s

  - job_name: 'node-exporter'
    static_configs:
      - targets: ['localhost:9100']

alerting:
  alertmanagers:
    - static_configs:
        - targets:
          - alertmanager:9093
```

### Grafana Dashboard Setup

1. **Install Grafana Docker container**:
```bash
docker run -d \
  --name=grafana \
  -p 3000:3000 \
  -v grafana-storage:/var/lib/grafana \
  grafana/grafana-enterprise
```

2. **Key Dashboards to Import**:
   - Symfony Application Metrics
   - Docker Container Monitoring
   - MySQL/MariaDB Performance
   - System Resource Monitoring

3. **Essential Metrics to Track**:
   - API response times (p50, p95, p99)
   - Error rates by endpoint
   - Database query performance
   - Memory and CPU usage per container
   - Active users and requests per minute
   - ESP32 device connectivity status

### Alert Rules

Create `monitoring/alert_rules.yml`:

```yaml
groups:
  - name: neutria-api-alerts
    rules:
      - alert: HighErrorRate
        expr: rate(http_requests_total{status=~"5.."}[5m]) > 0.1
        for: 5m
        labels:
          severity: critical
        annotations:
          summary: "High error rate detected"
          description: "Error rate is {{ $value }} errors per second"

      - alert: HighResponseTime
        expr: histogram_quantile(0.95, rate(http_request_duration_seconds_bucket[5m])) > 2
        for: 5m
        labels:
          severity: warning
        annotations:
          summary: "High response time detected"
          description: "95th percentile response time is {{ $value }} seconds"

      - alert: DatabaseConnectionFailure
        expr: up{job="neutria-api"} == 0
        for: 1m
        labels:
          severity: critical
        annotations:
          summary: "API is down"
          description: "Neutria API has been down for more than 1 minute"

      - alert: HighMemoryUsage
        expr: container_memory_usage_bytes / container_spec_memory_limit_bytes > 0.9
        for: 5m
        labels:
          severity: warning
        annotations:
          summary: "High memory usage"
          description: "Container {{ $labels.name }} is using {{ $value }}% of memory"
```

### Log Aggregation with ELK Stack

```yaml
# docker-compose.monitoring.yml
version: '3.8'
services:
  elasticsearch:
    image: docker.elastic.co/elasticsearch/elasticsearch:8.11.0
    environment:
      - discovery.type=single-node
      - "ES_JAVA_OPTS=-Xms512m -Xmx512m"
    volumes:
      - elasticsearch_data:/usr/share/elasticsearch/data
    ports:
      - "9200:9200"

  logstash:
    image: docker.elastic.co/logstash/logstash:8.11.0
    volumes:
      - ./monitoring/logstash.conf:/usr/share/logstash/pipeline/logstash.conf
    ports:
      - "5044:5044"
    depends_on:
      - elasticsearch

  kibana:
    image: docker.elastic.co/kibana/kibana:8.11.0
    ports:
      - "5601:5601"
    environment:
      - ELASTICSEARCH_HOSTS=http://elasticsearch:9200
    depends_on:
      - elasticsearch

volumes:
  elasticsearch_data:
```

## Backup & Disaster Recovery

### Database Backup Strategy

1. **Automated Daily Backups**:
```bash
#!/bin/bash
# backup-database.sh
DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/backups/database"
DB_NAME="dbneutria"

# Create backup directory
mkdir -p $BACKUP_DIR

# Dump database
mysqldump -h $DB_HOST -u $DB_USER -p$DB_PASSWORD $DB_NAME | gzip > $BACKUP_DIR/dbneutria_$DATE.sql.gz

# Keep only last 30 days
find $BACKUP_DIR -name "*.sql.gz" -mtime +30 -delete

# Upload to cloud storage (optional)
# aws s3 cp $BACKUP_DIR/dbneutria_$DATE.sql.gz s3://neutria-backups/database/
```

2. **Point-in-Time Recovery Setup**:
```sql
-- Enable binary logging in MySQL/MariaDB
-- Add to my.cnf:
[mysqld]
log-bin=mysql-bin
binlog_format=ROW
expire_logs_days=7
max_binlog_size=100M
```

3. **Backup Verification**:
```bash
#!/bin/bash
# verify-backup.sh
BACKUP_FILE=$1
TEMP_DB="dbneutria_test_$(date +%s)"

# Create test database
mysql -h $DB_HOST -u $DB_USER -p$DB_PASSWORD -e "CREATE DATABASE $TEMP_DB;"

# Restore backup to test database
gunzip < $BACKUP_FILE | mysql -h $DB_HOST -u $DB_USER -p$DB_PASSWORD $TEMP_DB

# Verify table count
TABLE_COUNT=$(mysql -h $DB_HOST -u $DB_USER -p$DB_PASSWORD $TEMP_DB -e "SHOW TABLES;" | wc -l)

if [ $TABLE_COUNT -gt 0 ]; then
    echo "Backup verification successful: $TABLE_COUNT tables found"
else
    echo "Backup verification failed: No tables found"
    exit 1
fi

# Clean up test database
mysql -h $DB_HOST -u $DB_USER -p$DB_PASSWORD -e "DROP DATABASE $TEMP_DB;"
```

### Application Data Backup

```bash
#!/bin/bash
# backup-application.sh
DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/backups/application"

# Backup Docker volumes
docker run --rm \
  -v neutria-backend-prod_public-files:/data \
  -v $BACKUP_DIR:/backup \
  alpine tar czf /backup/public-files_$DATE.tar.gz -C /data .

# Backup configuration files
tar czf $BACKUP_DIR/config_$DATE.tar.gz \
  api/.env \
  api/config/jwt/ \
  compose.prod.yaml \
  build/nginx/api.prod.conf

# Backup Git repository (excluding sensitive data)
git clone --bare /path/to/neutria-backend $BACKUP_DIR/repo_$DATE.git
```

### Disaster Recovery Plan

1. **RTO (Recovery Time Objective)**: 4 hours
2. **RPO (Recovery Point Objective)**: 1 hour

**Recovery Steps**:
```bash
# 1. Restore database
mysql -h $DB_HOST -u $DB_USER -p$DB_PASSWORD dbneutria < backup.sql

# 2. Restore application files
docker run --rm \
  -v neutria-backend-prod_public-files:/data \
  -v /backups:/backup \
  alpine tar xzf /backup/public-files_20251201_120000.tar.gz -C /data

# 3. Restart services
docker compose -f compose.prod.yaml down
docker compose -f compose.prod.yaml up -d

# 4. Verify functionality
curl -f http://localhost:8000/health || exit 1
```

## Scaling Strategies

### Horizontal Scaling

1. **Load Balancer Configuration**:
```nginx
# /etc/nginx/sites-available/api.neutria.fr
upstream neutria_api {
    least_conn;
    server 127.0.0.1:8000 max_fails=3 fail_timeout=30s;
    server 127.0.0.1:8001 max_fails=3 fail_timeout=30s;
    server 127.0.0.1:8002 max_fails=3 fail_timeout=30s;
}

server {
    listen 443 ssl http2;
    server_name api.neutria.fr;
    
    location / {
        proxy_pass http://neutria_api;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }
}
```

2. **Multi-Instance Docker Compose**:
```yaml
# compose.prod.yaml (scaled version)
version: '3.8'
services:
  php:
    build:
      context: .
      dockerfile: build/php/Dockerfile.prod
    volumes:
      - ./api:/app/api
      - neutria-backend-prod_public-files:/app/api/public
    environment:
      - APP_ENV=prod
    deploy:
      replicas: 3
      resources:
        limits:
          memory: 512M
        reservations:
          memory: 256M

  nginx:
    build:
      context: .
      dockerfile: build/nginx/Dockerfile.prod
    ports:
      - "8000:80"
    volumes:
      - neutria-backend-prod_public-files:/app/api/public
    depends_on:
      - php
    deploy:
      replicas: 2
```

### Database Scaling

1. **Read Replicas Setup**:
```yaml
# compose.prod.yaml with read replica
services:
  php:
    environment:
      - DATABASE_URL="mysql://user:pass@master-db:3306/dbneutria"
      - DATABASE_READ_URL="mysql://user:pass@replica-db:3306/dbneutria"
```

2. **Connection Pooling with ProxySQL**:
```yaml
  proxysql:
    image: proxysql/proxysql:2.4
    ports:
      - "6033:6033"
    volumes:
      - ./monitoring/proxysql.cnf:/etc/proxysql.cnf
    depends_on:
      - mysql-master
      - mysql-replica
```

### Caching Strategy

1. **Redis for Session Storage**:
```yaml
  redis:
    image: redis:7-alpine
    ports:
      - "6379:6379"
    volumes:
      - redis_data:/data
    command: redis-server --appendonly yes
```

2. **Symfony Cache Configuration**:
```yaml
# config/packages/cache.yaml
framework:
  cache:
    app: cache.adapter.redis
    default_redis_provider: redis://redis:6379
    system: cache.adapter.redis
```

### Auto-Scaling with Docker Swarm

```bash
# Initialize Docker Swarm
docker swarm init

# Deploy stack with auto-scaling
docker stack deploy -c compose.prod.yaml neutria

# Scale services based on load
docker service scale neutria_php=5
docker service scale neutria_nginx=3
```

## Maintenance Procedures

### Scheduled Maintenance Tasks

1. **Daily Tasks** (Cron job at 2:00 AM):
```bash
#!/bin/bash
# daily-maintenance.sh

# Clear Symfony cache
docker compose -f compose.prod.yaml exec php php bin/console cache:clear --env=prod

# Rotate logs
docker compose -f compose.prod.yaml exec php php bin/console cache:pool:clear cache.app

# Update device last_seen status
docker compose -f compose.prod.yaml exec php php bin/console app:update-device-status

# Clean up old sessions
docker compose -f compose.prod.yaml exec redis redis-cli --scan --pattern "sess:*" | xargs -r redis-cli del
```

2. **Weekly Tasks** (Sunday 3:00 AM):
```bash
#!/bin/bash
# weekly-maintenance.sh

# Optimize database tables
docker compose -f compose.prod.yaml exec php php bin/console doctrine:schema:validate

# Update Composer dependencies
docker compose -f compose.prod.yaml exec php composer update --no-dev --optimize-autoloader

# Clear old API logs (older than 30 days)
find /var/log/neutria -name "*.log" -mtime +30 -delete

# Security audit
docker compose -f compose.prod.yaml exec php php bin/console security:check
```

3. **Monthly Tasks** (1st of month):
```bash
#!/bin/bash
# monthly-maintenance.sh

# Full system update
docker compose -f compose.prod.yaml pull
docker compose -f compose.prod.yaml build --no-cache
docker compose -f compose.prod.yaml up -d

# Database optimization
docker compose -f compose.prod.yaml exec php php bin/console doctrine:migrations:migrate

# SSL certificate renewal check
certbot renew --dry-run
```

### Zero-Downtime Deployment

```bash
#!/bin/bash
# zero-downtime-deploy.sh

# 1. Pull latest code
git pull

# 2. Build new images
docker compose -f compose.prod.yaml build --no-cache

# 3. Start new containers alongside old ones
docker compose -f compose.prod.yaml up -d --scale php=2

# 4. Wait for health checks
sleep 30

# 5. Run migrations on new containers
docker compose -f compose.prod.yaml exec php php bin/console doctrine:migrations:migrate --no-interaction

# 6. Stop old containers
docker compose -f compose.prod.yaml up -d --scale php=1

# 7. Clean up unused images
docker image prune -f
```

### Health Check Monitoring

```yaml
# Enhanced health checks in compose.prod.yaml
services:
  php:
    healthcheck:
      test: ["CMD", "php-fpm", "-t"]
      interval: 30s
      timeout: 10s
      retries: 3
      start_period: 40s

  nginx:
    healthcheck:
      test: ["CMD", "curl", "-f", "http://localhost/health"]
      interval: 30s
      timeout: 10s
      retries: 3
      start_period: 40s
```

### Maintenance Mode Implementation

```php
// src/Controller/MaintenanceController.php
#[Route('/maintenance')]
class MaintenanceController extends AbstractController
{
    #[Route('/enable', name: 'maintenance_enable')]
    public function enable(): Response
    {
        file_put_contents('../var/maintenance.flag', 'enabled');
        return new Response('Maintenance mode enabled');
    }

    #[Route('/disable', name: 'maintenance_disable')]
    public function disable(): Response
    {
        if (file_exists('../var/maintenance.flag')) {
            unlink('../var/maintenance.flag');
        }
        return new Response('Maintenance mode disabled');
    }
}
```

### Performance Monitoring During Maintenance

```bash
# Monitor system resources during maintenance
watch -n 5 'docker stats --no-stream'

# Monitor API response times
curl -w "@curl-format.txt" -o /dev/null -s "http://localhost:8000/api/health"

# Database performance monitoring
docker compose -f compose.prod.yaml exec php php bin/console doctrine:query:sql "SHOW PROCESSLIST"
```

## Troubleshooting

### Container won't start

```bash
# Check logs for errors
docker compose -f compose.prod.yaml logs

# Check specific service
docker compose -f compose.prod.yaml logs php
```

### Database connection issues

```bash
# Test database connection from PHP container
docker compose -f compose.prod.yaml exec php php bin/console doctrine:query:sql "SELECT 1"

# Check DATABASE_URL is correct
docker compose -f compose.prod.yaml exec php php bin/console debug:container --env-vars
```

### Permission issues

```bash
# Fix permissions in container
docker compose -f compose.prod.yaml exec php chown -R www-data:www-data /app/api/var /app/api/public
```

### Clear opcache after code changes

```bash
# Restart PHP container to clear opcache
docker compose -f compose.prod.yaml restart php
```

### High memory usage

```bash
# Check resource usage
docker stats

# Adjust PHP memory limit in Dockerfile.prod if needed
# memory_limit=512M can be increased
```

## Security Checklist

- [ ] `.env` file has strong `APP_SECRET`
- [ ] JWT keys are generated with strong passphrase
- [ ] Database uses strong passwords
- [ ] CORS is configured for specific domains (not `*`)
- [ ] SSL/TLS is enabled (HTTPS)
- [ ] `APP_DEBUG=0` in production
- [ ] File permissions are correct (JWT keys are 600)
- [ ] Firewall rules allow only necessary ports
- [ ] Regular backups are scheduled
- [ ] Monitoring is set up for errors and performance

## Production vs Development

| Feature | Development | Production |
|---------|-------------|------------|
| Environment | `APP_ENV=dev` | `APP_ENV=prod` |
| Debug | Enabled | Disabled |
| OPcache | Disabled | Enabled with no validation |
| Composer | With dev deps | No dev deps, optimized |
| Cache warmup | No | Yes |
| Volumes | Bind mounts | Named volumes for uploads |
| Database | Containerized | External |
| HTTPS | Optional | Required (reverse proxy) |
| Health checks | No | Yes |

---

**Last Updated:** 2025-10-13
