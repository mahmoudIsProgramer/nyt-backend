<?php

class DatabaseMigration
{
    private $pdo;
    private $migrationsPath;
    private $migrationsTable = 'migrations';

    public function __construct(string $dbPath, string $migrationsPath)
    {
        $this->migrationsPath = $migrationsPath;
        $this->connect($dbPath);
        $this->createMigrationsTable();
    }

    private function connect(string $dbPath): void
    {
        try {
            $this->pdo = new PDO("sqlite:$dbPath");
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            echo "✓ Connected to database successfully\n";
        } catch (PDOException $e) {
            die("Connection failed: " . $e->getMessage() . "\n");
        }
    }

    private function createMigrationsTable(): void
    {
        $sql = "CREATE TABLE IF NOT EXISTS {$this->migrationsTable} (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            migration VARCHAR(255) NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )";

        try {
            $this->pdo->exec($sql);
            echo "✓ Migrations table checked/created\n";
        } catch (PDOException $e) {
            die("Error creating migrations table: " . $e->getMessage() . "\n");
        }
    }

    public function run(): void
    {
        $files = glob($this->migrationsPath . "/*.sql");
        sort($files); // Ensure files are processed in order

        foreach ($files as $file) {
            $migrationName = basename($file);
            
            // Skip if migration already executed
            if ($this->hasMigrationRun($migrationName)) {
                echo "→ Migration {$migrationName} already executed\n";
                continue;
            }

            // Run migration
            try {
                $sql = file_get_contents($file);
                $this->pdo->exec($sql);
                $this->recordMigration($migrationName);
                echo "✓ Executed migration: {$migrationName}\n";
            } catch (PDOException $e) {
                die("Error executing migration {$migrationName}: " . $e->getMessage() . "\n");
            }
        }

        echo "\n✓ All migrations completed successfully!\n";
    }

    private function hasMigrationRun(string $migrationName): bool
    {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM {$this->migrationsTable} WHERE migration = ?");
        $stmt->execute([$migrationName]);
        return (bool) $stmt->fetchColumn();
    }

    private function recordMigration(string $migrationName): void
    {
        $stmt = $this->pdo->prepare("INSERT INTO {$this->migrationsTable} (migration) VALUES (?)");
        $stmt->execute([$migrationName]);
    }
}

// Configuration
$dbPath = __DIR__ . '/database.sqlite';
$migrationsPath = __DIR__ . '/migrations';

// Run migrations
try {
    $migration = new DatabaseMigration($dbPath, $migrationsPath);
    $migration->run();
} catch (Exception $e) {
    die("Migration failed: " . $e->getMessage() . "\n");
}