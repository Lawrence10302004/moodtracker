<?php
// api/register.php
declare(strict_types=1);
header('Content-Type: application/json');
require_once __DIR__ . '/../config/db.php';
if (session_status() === PHP_SESSION_NONE) session_start();

// Accept JSON or form-encoded POST
$raw = file_get_contents('php://input');
if ($raw && strpos($_SERVER['CONTENT_TYPE'] ?? '', 'application/json') !== false) {
    $data = json_decode($raw, true);
} else {
    $data = $_POST;
}

$username = isset($data['username']) ? trim($data['username']) : '';
$password = isset($data['password']) ? $data['password'] : '';
$name = isset($data['name']) ? trim($data['name']) : null;

// basic username validation: 3-100 chars
if (strlen($username) < 3 || strlen($username) > 100 || strlen($password) < 6) {
    if (strpos($_SERVER['CONTENT_TYPE'] ?? '', 'application/json') !== false) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid input']);
        exit;
    }
    header('Location: ../register.php?error=1');
    exit;
}

try {
    $pdo = getPDO();
    // check existing username
    $stmt = $pdo->prepare('SELECT id FROM users WHERE username = :username LIMIT 1');
    $stmt->execute([':username' => $username]);
    if ($stmt->fetch()) {
        if (strpos($_SERVER['CONTENT_TYPE'] ?? '', 'application/json') !== false) {
            http_response_code(409);
            echo json_encode(['error' => 'Username already exists']);
            exit;
        }
        header('Location: ../register.php?error=exists');
        exit;
    }

    $hash = password_hash($password, PASSWORD_DEFAULT);
    $currentTimestamp = sqlCurrentTimestamp();
    $ins = $pdo->prepare("INSERT INTO users (username, password_hash, created_at) VALUES (:username, :ph, {$currentTimestamp})");
    $ins->execute([':username' => $username, ':ph' => $hash]);
    $userId = (int)$pdo->lastInsertId();

    if (strpos($_SERVER['CONTENT_TYPE'] ?? '', 'application/json') !== false) {
        echo json_encode(['ok' => true, 'id' => $userId, 'message' => 'Registration successful. Please log in.']);
    } else {
        header('Location: ../login.php?msg=registered');
    }
} catch (PDOException $e) {
    http_response_code(500);
    $errorMsg = $e->getMessage();
    error_log("Database error: " . $errorMsg);
    
    // Check for driver error
    if (strpos($errorMsg, 'could not find driver') !== false) {
        $driverNeeded = isPostgreSQL() ? 'pdo_pgsql' : 'pdo_mysql';
        error_log("Missing PDO driver: {$driverNeeded}");
        if (strpos($_SERVER['CONTENT_TYPE'] ?? '', 'application/json') !== false) {
            echo json_encode([
                'error' => 'Database configuration error',
                'message' => "The {$driverNeeded} PHP extension is not installed. Please configure your Railway environment to include PostgreSQL extensions."
            ]);
        } else {
            header('Location: ../register.php?error=driver');
        }
    } else {
        if (strpos($_SERVER['CONTENT_TYPE'] ?? '', 'application/json') !== false) {
            echo json_encode(['error' => 'Server error', 'message' => $errorMsg]);
        } else {
            header('Location: ../register.php?error=server');
        }
    }
} catch (Exception $e) {
    http_response_code(500);
    error_log($e->getMessage());
    if (strpos($_SERVER['CONTENT_TYPE'] ?? '', 'application/json') !== false) {
        echo json_encode(['error' => 'Server error']);
    } else {
        header('Location: ../register.php?error=server');
    }
}
