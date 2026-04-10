# quickstart.md

## Requirements
- PHP 8.3, Composer
- Database configured (MySQL/MariaDB)
- .env configured with DB and Sanctum

## Setup
1. composer install
2. cp .env.example .env and configure DB
3. php artisan key:generate
4. php artisan migrate --seed
5. php artisan test --filter=Patient

## Running tests
- Run full suite: php artisan test
- Run patient API tests: php artisan test --filter="Patient"

## Creating sample data
- Use Patient factory: \App\Models\Patient::factory()->create()
- Create socioeconomic via relationship: $patient->socioeconomic()->create([...])

