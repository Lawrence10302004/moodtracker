<?php
/**
 * Database connection test script
 * Use this to verify your database connection and driver installation
 */

require_once __DIR__ . '/../config/db.php';

header('Content-Type: application/json');

$results = [
    'timestamp' => date('Y-m-d H:i:s'),
    'environment' => [],
    'database' => [],
    'errors' => []
];

// Check environment variables
$results['environment']['DATABASE_URL'] = !empty($_ENV['DATABASE_URL']) ? 'Set (hidden)' : 'Not set';
$results['environment']['PGHOST'] = !empty($_ENV['PGHOST']) ? 'Set' : 'Not set';
$results['environment']['PGDATABASE'] = !empty($_ENV['PGDATABASE']) ? 'Set' : 'Not set';
$results['environment']['RAILWAY_ENVIRONMENT'] = !empty($_ENV['RAILWAY_ENVIRONMENT']) ? 'Set' : 'Not set';

// Check PHP extensions
$results['php_extensions'] = [
    'pdo' => extension_loaded('pdo') ? 'Loaded' : 'NOT LOADED',
    'pdo_pgsql' => extension_loaded('pdo_pgsql') ? 'Loaded' : 'NOT LOADED',
    'pdo_mysql' => extension_loaded('pdo_mysql') ? 'Loaded' : 'NOT LOADED',
    'pgsql' => extension_loaded('pgsql') ? 'Loaded' : 'NOT LOADED'
];

// Check database type
$results['database']['type'] = getDbType();
$results['database']['is_postgresql'] = isPostgreSQL();

// Try to connect
try {
    $pdo = getPDO();
    $results['database']['connection'] = 'Success';
    $results['database']['server_version'] = $pdo->getAttribute(PDO::ATTR_SERVER_VERSION);
    
    // Try a simple query
    $stmt = $pdo->query("SELECT 1 as test");
    $test = $stmt->fetch();
    $results['database']['query_test'] = $test ? 'Success' : 'Failed';
    
    // Check if tables exist
    if (isPostgreSQL()) {
        $stmt = $pdo->query("SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = 'public'");
    } else {
        $stmt = $pdo->query("SHOW TABLES");
    }
    $tables = $stmt->fetchAll();
    $results['database']['tables_count'] = count($tables);
    $results['database']['tables_exist'] = count($tables) > 0 ? 'Yes' : 'No';
    
} catch (PDOException $e) {
    $results['database']['connection'] = 'Failed';
    $results['errors'][] = [
        'type' => 'PDOException',
        'message' => $e->getMessage(),
        'code' => $e->getCode()
    ];
    
    // Check if it's a driver error
    if (strpos($e->getMessage(), 'could not find driver') !== false) {
        $results['errors'][] = [
            'type' => 'Driver Missing',
            'message' => 'The required PDO driver is not installed. Please check your Railway build configuration.',
            'required_driver' => isPostgreSQL() ? 'pdo_pgsql' : 'pdo_mysql'
        ];
    }
} catch (Exception $e) {
    $results['database']['connection'] = 'Failed';
    $results['errors'][] = [
        'type' => 'Exception',
        'message' => $e->getMessage()
    ];
}

// Overall status
$results['status'] = empty($results['errors']) && $results['database']['connection'] === 'Success' ? 'OK' : 'ERROR';

echo json_encode($results, JSON_PRETTY_PRINT);

