# NYT Article Explorer

A PHP application that allows users to search, view, and save favorite articles from the New York Times API.
## Api Requests
    Postman Collection Included In Project Files 

## Requirements

- PHP 8.2 or higher
- SQLite3
- Composer

## Installation

1. Clone the repository
git clone https://github.com/mahmoudIsProgramer/nyt-backend.git
2. Install dependencies:
   ```bash
   composer install
   ```
3. Copy `.env.example` to `.env` and configure:
   ```bash
   cp .env.example .env
   ```
4. Update `.env` with your NYT API key and JWT secret

5. Initialize and migrate the database:
   ```bash
   # Create SQLite database file
   touch database/database.sqlite
   
   # Run migrations
   php database/migrate.php
   ```

6. Start the development server:
   ```bash
   php -S localhost:8000 -t public
   ```

