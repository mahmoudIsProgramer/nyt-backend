<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;

// Load environment variables
$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

try {
    // Create database directory if it doesn't exist
    if (!file_exists(__DIR__)) {
        mkdir(__DIR__, 0777, true);
    }

    // Initialize database
    $db = new SQLite3(__DIR__ . '/nyt.sqlite');
    
    // Enable foreign key support
    $db->exec('PRAGMA foreign_keys = ON;');
    
    // Read and execute the schema
    $schema = file_get_contents(__DIR__ . '/schema.sql');
    $db->exec($schema);
    
    echo "Database initialized successfully!\n";
} catch (Exception $e) {
    echo "Error initializing database: " . $e->getMessage() . "\n";
    exit(1);
}
