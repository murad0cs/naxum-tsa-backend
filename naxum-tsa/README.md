# Naxum TSA Backend

A Laravel 12 backend application for Company ABC's multi-level marketing commission tracking system. This system manages distributor commissions and generates reports for handbag sales.

## Table of Contents

1. [Requirements](#requirements)
2. [Installation](#installation)
3. [Running the Application](#running-the-application)
4. [Database Setup](#database-setup)
5. [API Endpoints](#api-endpoints)
6. [Running Tests](#running-tests)
7. [Project Structure](#project-structure)
8. [Troubleshooting](#troubleshooting)

## Requirements

- Docker Desktop (recommended) OR PHP 8.4+ with Composer
- MariaDB 11+ (included in Docker setup)
- Git

## Installation

### Using Docker (Recommended)

1. Clone the repository:
```bash
git clone <repository-url>
cd naxum-tsa
```

2. Copy the environment file:
```bash
cp .env.example .env
```

3. Update the .env file with these database settings:
```
DB_CONNECTION=mariadb
DB_HOST=mariadb
DB_PORT=3306
DB_DATABASE=nxm_assessment_2023
DB_USERNAME=sail
DB_PASSWORD=password
```

4. Build and start the Docker containers:
```bash
# On Windows PowerShell
$env:WWWUSER=1000; $env:WWWGROUP=1000; docker compose up -d

# On Linux/Mac
WWWUSER=$(id -u) WWWGROUP=$(id -g) docker compose up -d
```

5. Generate application key:
```bash
docker compose exec laravel.test php artisan key:generate
```

6. Import the database (first time only):
```bash
# On Windows PowerShell
Get-Content database/nxm_assessment_2023.sql | docker compose exec -T mariadb mariadb -u sail -ppassword nxm_assessment_2023

# On Linux/Mac
docker compose exec -T mariadb mariadb -u sail -ppassword nxm_assessment_2023 < database/nxm_assessment_2023.sql
```

### Without Docker (Manual Setup)

1. Clone the repository:
```bash
git clone <repository-url>
cd naxum-tsa
```

2. Install PHP dependencies:
```bash
composer install
```

3. Copy and configure environment:
```bash
cp .env.example .env
```

4. Update .env with your local database settings:
```
DB_CONNECTION=mariadb
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nxm_assessment_2023
DB_USERNAME=root
DB_PASSWORD=your_password
```

5. Generate application key:
```bash
php artisan key:generate
```

6. Create the database and import data:
```bash
mysql -u root -p -e "CREATE DATABASE nxm_assessment_2023;"
mysql -u root -p nxm_assessment_2023 < database/nxm_assessment_2023.sql
```

## Running the Application

### With Docker

The application runs on port 80 by default.

```bash
# Start containers
docker compose up -d

# Stop containers
docker compose down

# View running containers
docker compose ps

# Restart containers
docker compose restart
```

Access the application at: http://localhost

### Without Docker

```bash
php artisan serve
```

Access the application at: http://localhost:8000

## Database Setup

The database schema is provided in `database/nxm_assessment_2023.sql`. This file contains:

- 6 tables: users, orders, order_items, products, categories, user_category
- 18,252 user records
- 4,234 order records
- 7,698 order item records
- 80 product records

The schema has not been modified. Only the provided tables and data are used.

### Accessing the Database

With Docker:
```bash
# Connect to MariaDB
docker compose exec mariadb mariadb -u sail -ppassword nxm_assessment_2023

# Run a query
docker compose exec mariadb mariadb -u sail -ppassword nxm_assessment_2023 -e "SELECT COUNT(*) FROM users;"
```

Without Docker:
```bash
mysql -u root -p nxm_assessment_2023
```

### Useful Database Queries

Check record counts:
```sql
SELECT 'users' as tbl, COUNT(*) as count FROM users
UNION ALL SELECT 'orders', COUNT(*) FROM orders
UNION ALL SELECT 'order_items', COUNT(*) FROM order_items;
```

View table structure:
```sql
DESCRIBE users;
DESCRIBE orders;
```

## API Endpoints

### Task 1: Commission Report

**Get Commission Report**
```
GET /api/commission-report
```

Query Parameters:
- `distributor` - Filter by distributor ID, first name, or last name
- `date_from` - Filter orders from this date (YYYY-MM-DD)
- `date_to` - Filter orders up to this date (YYYY-MM-DD)
- `invoice` - Filter by invoice number
- `per_page` - Number of results per page (default: 10)
- `page` - Page number (default: 1)

Example:
```bash
curl "http://localhost/api/commission-report?invoice=ABC4170"
curl "http://localhost/api/commission-report?distributor=Purdy&date_from=2020-01-01"
```

**Get Order Items**
```
GET /api/commission-report/order/{orderId}/items
```

Example:
```bash
curl "http://localhost/api/commission-report/order/218463/items"
```

### Task 2: Top Distributors

**Get Top 200 Distributors**
```
GET /api/top-distributors
```

Query Parameters:
- `per_page` - Number of results per page (default: 10)
- `page` - Page number (default: 1)

Example:
```bash
curl "http://localhost/api/top-distributors?per_page=200"
```

### Testing with Postman

A Postman collection is included at `postman_collection.json`. Import it into Postman to test all endpoints.

## Running Tests

### With Docker

```bash
docker compose exec laravel.test php artisan test
```

Run specific test suites:
```bash
# Unit tests only
docker compose exec laravel.test php artisan test --testsuite=Unit

# Feature tests only
docker compose exec laravel.test php artisan test --testsuite=Feature
```

### Without Docker

```bash
php artisan test
```

### Expected Test Results

All 37 tests should pass:
- 15 unit tests for commission percentage calculation
- 11 feature tests for commission report API
- 9 feature tests for top distributors API
- 2 example tests

### Test Cases Verified

Commission Report (Task 1):
- ABC4170: $6.00 commission
- ABC6931: $37.20 commission
- ABC23352: $27.60 commission
- ABC3010: $0 commission
- ABC19323: $0 commission

Top Distributors (Task 2):
- Demario Purdy: $22,026.75 (Rank 1)
- Floy Miller: $9,645.00
- Loy Schamberger: $575.00

## Project Structure

```
app/
  Http/
    Controllers/
      CommissionReportController.php    # Task 1 controller
      TopDistributorsController.php     # Task 2 controller
  Models/
    User.php                            # User model
    Order.php                           # Order model
    OrderItem.php                       # Order item model
    Product.php                         # Product model
    Category.php                        # Category model
    UserCategory.php                    # User-category pivot model
  Repositories/
    Contracts/
      CommissionReportRepositoryInterface.php
      TopDistributorsRepositoryInterface.php
    CommissionReportRepository.php      # Task 1 data access
    TopDistributorsRepository.php       # Task 2 data access
  Services/
    CommissionReportService.php         # Task 1 business logic
    TopDistributorsService.php          # Task 2 business logic
  Providers/
    RepositoryServiceProvider.php       # Dependency injection bindings

routes/
  api.php                               # API route definitions

tests/
  Unit/
    CommissionReportTest.php            # Unit tests for commission calculation
  Feature/
    CommissionReportApiTest.php         # API tests for Task 1
    TopDistributorsApiTest.php          # API tests for Task 2

database/
  nxm_assessment_2023.sql               # Original database dump
```

## Viewing Logs

The application uses a structured logging system with separate log files for different concerns.

### Log Storage Location

All logs are stored in the `storage/logs/` directory:

```
Project Root
└── storage/
    └── logs/
        ├── laravel.log              # General application logs
        ├── api-YYYY-MM-DD.log       # Daily API request logs (JSON)
        ├── queries-YYYY-MM-DD.log   # Daily database query logs (JSON)
        └── errors-YYYY-MM-DD.log    # Daily error logs (JSON)
```

Physical paths:
- With Docker: Container path `/var/www/html/storage/logs/` is mounted to your host
- Without Docker: `<project-root>/storage/logs/`

Daily log files (api, queries, errors) rotate automatically and are retained for 14 days by default.

### Log Files

| File | Format | Description |
|------|--------|-------------|
| storage/logs/laravel.log | Text | General application logs |
| storage/logs/api-YYYY-MM-DD.log | JSON | API request logs with timing and performance |
| storage/logs/queries-YYYY-MM-DD.log | JSON | Database query logs with execution time |
| storage/logs/errors-YYYY-MM-DD.log | JSON | Error-only logs with stack traces |

### Log Configuration

Environment variables for logging:

| Variable | Default | Description |
|----------|---------|-------------|
| LOG_CHANNEL | stack | Default log channel |
| LOG_STACK | daily | Channels in stack |
| LOG_LEVEL | debug | Minimum log level |
| LOG_DAILY_DAYS | 14 | Days to retain logs |
| LOG_QUERIES | false | Enable query logging in production |
| LOG_SLOW_QUERY_THRESHOLD | 100 | Slow query threshold in ms |

### With Docker

```bash
# View Laravel logs
docker compose exec laravel.test cat storage/logs/laravel.log

# View API request logs (with date)
docker compose exec laravel.test cat storage/logs/api-2025-12-10.log

# View database query logs
docker compose exec laravel.test cat storage/logs/queries-2025-12-10.log

# View error logs only
docker compose exec laravel.test cat storage/logs/errors-2025-12-10.log

# Tail logs in real-time
docker compose exec laravel.test tail -f storage/logs/laravel.log

# View Docker container logs
docker compose logs laravel.test
docker compose logs mariadb

# Follow Docker logs
docker compose logs -f laravel.test

# List all log files
docker compose exec laravel.test ls -la storage/logs/
```

### Without Docker

```bash
# View Laravel logs
cat storage/logs/laravel.log

# View API request logs
cat storage/logs/api-*.log

# Tail logs in real-time
tail -f storage/logs/laravel.log

# List all log files
ls -la storage/logs/
```

### Structured JSON Log Format

All API and query logs use pure JSON format (one JSON object per line) for easy parsing by log aggregation tools like ELK Stack, Datadog, Splunk, or CloudWatch.

### API Request Log Structure

Each API request is logged as a structured JSON object:

```json
{
  "message": "api_request",
  "context": {
    "request": {
      "id": "req_20251210071611_d6bee186",
      "timestamp": "2025-12-10T07:16:11+00:00",
      "method": "GET",
      "path": "api/commission-report",
      "full_url": "http://localhost/api/commission-report?per_page=10",
      "query_params": {"per_page": "10"},
      "route_name": "commission-report.index"
    },
    "client": {
      "ip": "127.0.0.1",
      "user_agent": "Mozilla/5.0..."
    },
    "response": {
      "status_code": 200,
      "status_text": "OK",
      "content_length": 4096
    },
    "performance": {
      "duration_ms": 84.23,
      "memory_bytes": 2097152,
      "memory_peak_bytes": 42467328
    },
    "environment": {
      "app_env": "production",
      "php_version": "8.4.15"
    }
  },
  "level": 200,
  "level_name": "INFO",
  "channel": "production",
  "datetime": "2025-12-10T07:16:11.983578+00:00"
}
```

Fields included:
- request.id: Unique identifier for request tracing (also in X-Request-ID header)
- request.timestamp: ISO 8601 timestamp
- request.method: HTTP method (GET, POST, etc.)
- request.path: Request path
- request.full_url: Complete URL with query string
- request.query_params: Query parameters as object
- request.route_name: Laravel route name
- client.ip: Client IP address
- client.user_agent: Client user agent string
- response.status_code: HTTP response status code
- response.status_text: HTTP status text
- response.content_length: Response body size in bytes
- performance.duration_ms: Request processing time in milliseconds
- performance.memory_bytes: Memory used during request
- performance.memory_peak_bytes: Peak memory usage

### Query Log Structure

Database queries are logged as structured JSON:

```json
{
  "message": "query",
  "context": {
    "query": {
      "sql": "SELECT * FROM users WHERE id = ?",
      "bindings": [1],
      "connection": "mariadb"
    },
    "performance": {
      "time_ms": 54.95,
      "is_slow": false,
      "threshold_ms": 100.0
    },
    "context": {
      "timestamp": "2025-12-10T07:16:12+00:00",
      "request_id": "req_20251210071612_4da26366"
    }
  },
  "level": 100,
  "level_name": "DEBUG",
  "datetime": "2025-12-10T07:16:12.338597+00:00"
}
```

Fields included:
- query.sql: The SQL query with placeholders
- query.bindings: Parameter values
- query.connection: Database connection name
- performance.time_ms: Query execution time
- performance.is_slow: Whether query exceeded threshold
- performance.threshold_ms: Slow query threshold
- context.request_id: Links query to API request for tracing

Slow queries (over threshold) are logged with level "WARNING" instead of "DEBUG".

### Parsing Logs

Example commands for parsing JSON logs:

```bash
# Pretty print a single log entry
docker compose exec laravel.test bash -c "tail -1 storage/logs/api-*.log | jq ."

# Extract all request IDs and durations
docker compose exec laravel.test bash -c "cat storage/logs/api-*.log | jq '{id: .context.request.id, duration: .context.performance.duration_ms}'"

# Find slow requests (over 500ms)
docker compose exec laravel.test bash -c "cat storage/logs/api-*.log | jq 'select(.context.performance.duration_ms > 500)'"

# Find all error responses
docker compose exec laravel.test bash -c "cat storage/logs/api-*.log | jq 'select(.context.response.status_code >= 400)'"

# Find slow queries
docker compose exec laravel.test bash -c "cat storage/logs/queries-*.log | jq 'select(.context.performance.is_slow == true)'"
```

### Clear Logs

```bash
# With Docker
docker compose exec laravel.test php artisan log:clear

# Without Docker
php artisan log:clear

# Or manually
echo "" > storage/logs/laravel.log
```

## Troubleshooting

### Docker containers not starting

Check if ports 80 and 3306 are available:
```bash
# Windows
netstat -ano | findstr :80
netstat -ano | findstr :3306

# Linux/Mac
lsof -i :80
lsof -i :3306
```

Change ports in compose.yaml if needed.

### Database connection errors

1. Verify MariaDB container is running:
```bash
docker compose ps
```

2. Check database exists:
```bash
docker compose exec mariadb mariadb -u sail -ppassword -e "SHOW DATABASES;"
```

3. Verify connection settings in .env match compose.yaml

### Permission errors on Linux/Mac

Set correct ownership:
```bash
sudo chown -R $USER:$USER .
chmod -R 755 storage bootstrap/cache
```

### Cache issues

Clear all caches:
```bash
# With Docker
docker compose exec laravel.test php artisan config:clear
docker compose exec laravel.test php artisan cache:clear
docker compose exec laravel.test php artisan route:clear

# Without Docker
php artisan config:clear
php artisan cache:clear
php artisan route:clear
```

### Rebuild Docker containers

```bash
docker compose down
docker compose build --no-cache
docker compose up -d
```

## Commission Calculation Logic

Distributors earn commission based on the number of distributors they have referred at the time of order:

| Referred Distributors | Commission Rate |
|-----------------------|-----------------|
| 0 - 4                 | 5%              |
| 5 - 10                | 10%             |
| 11 - 20               | 15%             |
| 21 - 29               | 20%             |
| 30+                   | 30%             |

Commission is only earned when:
1. The purchaser is a Customer (category_id = 2)
2. The purchaser's referrer is a Distributor (category_id = 1)

## License

This project is proprietary software developed for Naxum TSA assessment.
