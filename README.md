# eventProcessApp

## Description
Build a Laravel application that receives webhook events from external sources, processes them through a configurable pipeline, and provides an admin interface for monitoring.

## Local Setup
- create mysql database
    - mysql -u root -p -e "CREATE DATABASE eventsdb;"
- .env content for database connection, add values based on you local database information
    ```
    DB_CONNECTION=mysql
    DB_HOST=<HOST>
    DB_PORT=<PORT>
    DB_DATABASE=eventsdb
    DB_USERNAME=<USER_NAME>
    DB_PASSWORD=<PASSWORD>
    DB_SOCKET=<SOCKET_URL>

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
- access 
    - `http://127.0.0.1:8000/events`


- NOTES: 
    - Copilot/codex is use for code completion
    - Frontend using template based on bootstrap

### examples
Postman collection in  `events_api.postman_collection.json`



