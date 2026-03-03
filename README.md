# eventProcessApp
## DEV: ANA MARIA RODRIGUEZ HERNANDEZ
## Description
Build a Laravel application that receives webhook events from external sources, processes them through a configurable pipeline, and provides an admin interface for monitoring.

## Local Setup Manual
- create mysql database
    - mysql -u root -p -e "CREATE DATABASE eventsdb;"
- .env content for database connection, add values based on you local database information
```
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=8889
    DB_DATABASE=events_db
    DB_USERNAME=root
    DB_PASSWORD=root

    ENRICHMENT_API_URL=http://localhost:8000/api/enrichment-mock
    ENRICHMENT_API_TOKEN=secret-token-mock
    ENRICHMENT_API_TIMEOUT=5


```
- You should move to event-process-app folder
    - `cd event-process-app`
- Install dependencies
    - `composer install`
- Run migrations
    - `php artisan migrate`
- Run seeders
    - `php artisan db:seed`
- run server
	- `php artisan serve`
- run frontend 
    -  `npm install Installs`
    -  `npm run dev`
    -  `npm run build`
- access to frontend 
    - `http://127.0.0.1:8000/events/events-dashboard`

- endpoints
    - check Postman collection in  `events_api.postman_collection.json`

- NOTES: 
    - Copilot/codex is use for code completion
    - Frontend using template based on bootstrap

- queries in `queries.sql`

# Local Setup Docker
## Docker (one command)
- build all service
    - `docker compose up --build`
- install dependencies
    - `docker compose exec app composer install`
- Migrations
    - `docker compose exec app php artisan migrate`
- Seeders
    - `docker compose exec app php artisan db:seed`
- Manual run vite(if need)
    - `docker compose exec node npm install`
    - `docker compose exec node npm run build`
- access
    - `http://localhost:8000/events/events-dashboard`
    - user:test@example.com
    - pws: password123
- Job commands
    - verify queue worker runner: `docker compose exec app php artisan queue:work`
    - trigger the job again: `docker compose exec app php artisan queue:retry all`
    - scores can be assign in the events list as well in the score button.
    - create rule button is for display only


# CODE STRUCTURE
## Events Type
- form_provider
- payment_gateway
- status_tracker


## Bakcend/Frontend Elements
1. controller
	- app/Http/Controllers/Api/WebhookController.php
	- app/Http/Controllers/Api/EnrichmentMockController.php
2. models
	- app/Models/Event.php
	- app/Models/EventAudit.php
	- app/Models/EventScore.php
	- app/Models/EnrichmentMock.php
3. migrations
	- database/migrations/2026_03_01_182615_create_events_table.php
	- database/migrations/2026_03_01_182654_create_events_audit_table.php
	- database/migrations/2026_03_01_182715_create_events_score_rules_table.php
4. seeders
	- database/seeders/EventScoreRuleSeeder.php
	- database/seeders/EventSeeder.php
5. routers
	- event-process-app/routes/api.php
	- event-process-app/routes/web.php
6. views
	- resources/views/Events/event-dashboard-page.blade.php
	- resources/views/Events/event-score-rules-page.blade.php
7. Filament Elements
	- pages
		- app/Filament/Events/Pages/EventScoreRulesPage.php
		- app/Filament/Events/Pages/EventsDashboardPage.php
	- Widgets
		- app/Filament/Events/Widgets/EventScoreRulesTableWidget.php
		- app/Filament/Events/Widgets/EventsTableWidget.php
	- providers
		- app/Providers/Filament/EventsPanelProvider.php
8. Postman
	-

## Core Process Elements
1. Jobs 
    - `app/Jobs/NormalizeEventJob.php`: 
    	- This method retrieves the raw event from the database, processes it using the appropriate normalizer based on the source, and updates the event record with the normalized payload or any errors encountered during processing.
    	- Job is trigger in `WebhookController`

## services
2. Factory
	- `app/Services/Sources/SourceFactory.php`:
		- Factory method to create a SourceNormalizer instance based on the source name.
3. Contracts
	- `app/Services/Sources/Contracts/SourceNormalizer.php`:
		- Defines the contract for normalizing incoming event payloads from various sources
4. Normalizers
    - `app/Services/Sources/Normalizers/FormProviderNormalizer.php`: 
        - This class is responsible for validating and normalizing incoming event payloads from a form provider source.
        - examples like google docs forms
    - `event-process-app/app/Services/Sources/Normalizers/PaymentGatewayNormalizer.php`:
        - This class is responsible for validating and normalizing incoming event payloads from a payment gateway source.
        - example like paypal
    - `event-process-app/app/Services/Sources/Normalizers/StatusTrackerNormalizer.php`:
    	- This class is responsible for validating and normalizing incoming event payloads from a status tracking source.
    	- example like tracking numbers for orders or packages
5. Enrichment service
    - endpoint is call in `NormalizeEventJob.php`
	- `event-process-app/app/Services/EnrichmentService.php` :
		- EnrichmentService is responsible for enriching an event by calling an external enrichment API Mock.
	- `event-process-app/config/services.php`
        - mock configurations
	- `event-process-app/app/Providers/AppServiceProvider.php`
        - limiter for the enrichment service

## Payload Formats
- form_provider
```
{
    "submission_id": "abc123",
    "email": "user@example.com",
    "answers": {
        "budget": 1000,
        "timeline": "Q3 2024",
        "interested_in": ["product_a", "product_b"],
    },
    "submitted_at": "2024-06-01T12:00:00Z"
    "metadata": {
        "source": "typeform",
        "form_id": "form_456"
    }    
}
```

- payment_gateway
```
{
    "transaction": {
        "id": "txn_123456",
        "amount_cents": 5000,
        "currency": "USD",
        "occurred_at": "2024-06-01T12:00:00Z",
        "status": "succeeded",
        "provider": "paypal"
    },
    "customer_email": "user@example.com"
}
```

- status_tracker
```
{
    "tracking_number": "TRACK123456",
    "status": "shipped",
    "occurred_at": "2024-06-01T12:00:00Z",
    "email": "user@example.com"
}


```
