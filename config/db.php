<?php
// config/db.php
declare(strict_types=1);

// Load environment variables
require_once __DIR__ . '/../api/load_env.php';

// Detect if PostgreSQL is available (Railway)
$use_postgres = !empty($_ENV['DATABASE_URL']) || 
               (!empty($_ENV['PGHOST']) && !empty($_ENV['PGDATABASE']) && 
                !empty($_ENV['PGUSER']) && !empty($_ENV['PGPASSWORD']));

// If running on Railway and Postgres is not configured, fail fast
$is_prod_env = !empty($_ENV['RAILWAY_ENVIRONMENT']) || !empty($_ENV['RAILWAY_STATIC_URL']) || !empty($_ENV['RAILWAY_PROJECT_ID']);
if ($is_prod_env && !$use_postgres) {
    http_response_code(500);
    die("Database configuration error: PostgreSQL is required in production. Please set DATABASE_URL (or PGHOST/PGDATABASE/PGUSER/PGPASSWORD).");
}

// MySQL configuration (for local development)
$mysql_host = $_ENV['DB_HOST'] ?? '127.0.0.1';
$mysql_db = $_ENV['DB_NAME'] ?? 'mood_tracker';
$mysql_user = $_ENV['DB_USER'] ?? 'root';
$mysql_pass = $_ENV['DB_PASS'] ?? '';

function getPDO(): PDO {
    global $use_postgres, $mysql_host, $mysql_db, $mysql_user, $mysql_pass;
    
    static $pdo = null;
    if ($pdo === null) {
        if ($use_postgres) {
            // PostgreSQL connection (Railway)
            if (!empty($_ENV['DATABASE_URL'])) {
                // Parse DATABASE_URL: postgresql://user:password@host:port/database
                $url = parse_url($_ENV['DATABASE_URL']);
                $dsn = sprintf(
                    "pgsql:host=%s;port=%s;dbname=%s",
                    $url['host'],
                    $url['port'] ?? 5432,
                    ltrim($url['path'], '/')
                );
                $user = $url['user'];
                $pass = $url['pass'];
            } else {
                // Use individual PostgreSQL environment variables
                $dsn = sprintf(
                    "pgsql:host=%s;port=%s;dbname=%s",
                    $_ENV['PGHOST'],
                    $_ENV['PGPORT'] ?? 5432,
                    $_ENV['PGDATABASE']
                );
                $user = $_ENV['PGUSER'];
                $pass = $_ENV['PGPASSWORD'];
            }
        } else {
            // MySQL connection (local development)
            $dsn = "mysql:host={$mysql_host};dbname={$mysql_db};charset=utf8mb4";
            $user = $mysql_user;
            $pass = $mysql_pass;
        }
        
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ];
        
        try {
            $pdo = new PDO($dsn, $user, $pass, $options);
        } catch (PDOException $e) {
            // Check if it's a driver error
            if (strpos($e->getMessage(), 'could not find driver') !== false || 
                strpos($e->getMessage(), 'driver') !== false) {
                $driverNeeded = $use_postgres ? 'pdo_pgsql' : 'pdo_mysql';
                error_log("PDO Driver Error: {$driverNeeded} extension is not installed. Error: " . $e->getMessage());
                if (php_sapi_name() !== 'cli') {
                    http_response_code(500);
                    die(json_encode([
                        'error' => 'Database driver not installed',
                        'message' => "The {$driverNeeded} PHP extension is required but not installed. Please install it in your Railway environment.",
                        'driver' => $driverNeeded
                    ]));
                }
            }
            throw $e;
        }
    }
    return $pdo;
}

// Helper function to get database type
function getDbType(): string {
    global $use_postgres;
    return $use_postgres ? 'postgresql' : 'mysql';
}

// Helper function to check if using PostgreSQL
function isPostgreSQL(): bool {
    global $use_postgres;
    return $use_postgres;
}

// Auto-initialize database on first connection (only if tables don't exist)
function ensureDatabaseInitialized() {
    static $initialized = false;
    if ($initialized) return;
    
    try {
        $pdo = getPDO();
        // Check if users table exists
        $isPostgres = isPostgreSQL();
        if ($isPostgres) {
            $stmt = $pdo->query("SELECT 1 FROM information_schema.tables WHERE table_schema = 'public' AND table_name = 'users' LIMIT 1");
        } else {
            $stmt = $pdo->query("SHOW TABLES LIKE 'users'");
        }
        $tableExists = $stmt->fetch() !== false;
        
        if (!$tableExists) {
            // Include and run init function silently (without output)
            // Define constant to prevent output when included
            if (!defined('INIT_DB_INCLUDED')) {
                require_once __DIR__ . '/../api/init.php';
            }
            // Call init_db() but don't output anything
            $result = init_db();
            if (!$result['success']) {
                error_log("Database initialization failed: " . ($result['message'] ?? 'Unknown error'));
            }
        }
        $initialized = true;
    } catch (Exception $e) {
        // Silently fail - let the app handle it
        error_log("Database initialization check failed: " . $e->getMessage());
    }
}

// Call initialization check (but don't block if it fails)
// Only do this in web context, not CLI
if (php_sapi_name() !== 'cli' && !defined('SKIP_AUTO_INIT')) {
    try {
        ensureDatabaseInitialized();
    } catch (Exception $e) {
        // Ignore - initialization will happen on first API call
    }
}
