# NYT Article Explorer

A PHP application that allows users to search, view, and save favorite articles from the New York Times API.

## Requirements

- PHP 8.2 or higher
- SQLite3
- Composer

## Installation

1. Clone the repository
2. Install dependencies:
   ```bash
   composer install
   ```
3. Copy `.env.example` to `.env` and configure:
   ```bash
   cp .env.example .env
   ```
4. Update `.env` with your NYT API key and JWT secret
5. Initialize the database (we'll create this script in the next step)
6. Start the development server:
   ```bash
   php -S localhost:8000 -t public
   ```

## Features

- User authentication with JWT
- Search NYT articles with pagination
- Save favorite articles
- View and manage favorite articles
- RESTful API design

## Project Structure

```
nyt/
├── src/
│   ├── Controllers/    # Request handlers
│   ├── Models/         # Database models
│   ├── Services/       # Business logic
│   ├── Utils/          # Helper classes
│   └── Config/         # Configuration
├── public/            # Web root
├── templates/         # HTML templates
├── database/         # SQLite database
└── vendor/           # Dependencies
```
