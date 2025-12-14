<?php
/**
 * Database initialization script
 * Creates all necessary tables for both MySQL and PostgreSQL
 */

require_once __DIR__ . '/../config/db.php';

// Set content type only if not already set (for CLI usage)
if (!headers_sent()) {
    header('Content-Type: application/json');
}

function init_db() {
    $pdo = getPDO();
    $isPostgres = isPostgreSQL();
    
    try {
        // Users table
        if ($isPostgres) {
            $pdo->exec("
                CREATE TABLE IF NOT EXISTS users (
                    id SERIAL PRIMARY KEY,
                    username VARCHAR(100) UNIQUE NOT NULL,
                    password_hash VARCHAR(255) NOT NULL,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                )
            ");
        } else {
            $pdo->exec("
                CREATE TABLE IF NOT EXISTS users (
                    id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                    username VARCHAR(100) UNIQUE NOT NULL,
                    password_hash VARCHAR(255) NOT NULL,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
            ");
        }
        
        // Diary entries table
        if ($isPostgres) {
            $pdo->exec("
                CREATE TABLE IF NOT EXISTS diary_entries (
                    id SERIAL PRIMARY KEY,
                    user_id INTEGER NOT NULL,
                    date DATE NOT NULL,
                    time TIME NOT NULL,
                    content TEXT,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    UNIQUE(user_id, date),
                    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
                )
            ");
        } else {
            $pdo->exec("
                CREATE TABLE IF NOT EXISTS diary_entries (
                    id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                    user_id INT(11) NOT NULL,
                    date DATE NOT NULL,
                    time TIME NOT NULL,
                    content TEXT,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    UNIQUE KEY unique_daily (user_id, date),
                    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
            ");
        }
        
        // Mood logs table
        if ($isPostgres) {
            $pdo->exec("
                CREATE TABLE IF NOT EXISTS mood_logs (
                    id SERIAL PRIMARY KEY,
                    user_id INTEGER,
                    date DATE NOT NULL,
                    time TIME NOT NULL,
                    face_emotion VARCHAR(64),
                    face_confidence REAL,
                    audio_emotion VARCHAR(64),
                    audio_score REAL,
                    combined_score INTEGER NOT NULL,
                    diary_id INTEGER,
                    meta JSONB,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    FOREIGN KEY (diary_id) REFERENCES diary_entries(id) ON DELETE SET NULL
                )
            ");
        } else {
            $pdo->exec("
                CREATE TABLE IF NOT EXISTS mood_logs (
                    id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                    user_id INT(11) DEFAULT NULL,
                    date DATE NOT NULL,
                    time TIME NOT NULL,
                    face_emotion VARCHAR(64) DEFAULT NULL,
                    face_confidence FLOAT DEFAULT NULL,
                    audio_emotion VARCHAR(64) DEFAULT NULL,
                    audio_score FLOAT DEFAULT NULL,
                    combined_score INT(11) NOT NULL,
                    diary_id INT(11) DEFAULT NULL,
                    meta LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    KEY diary_id (diary_id),
                    FOREIGN KEY (diary_id) REFERENCES diary_entries(id) ON DELETE SET NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
            ");
        }
        
        // Mood tags table
        if ($isPostgres) {
            $pdo->exec("
                CREATE TABLE IF NOT EXISTS mood_tags (
                    id SERIAL PRIMARY KEY,
                    user_id INTEGER NOT NULL,
                    mood_id INTEGER,
                    date DATE NOT NULL,
                    tag_name VARCHAR(50) NOT NULL,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                    FOREIGN KEY (mood_id) REFERENCES mood_logs(id) ON DELETE SET NULL
                )
            ");
        } else {
            $pdo->exec("
                CREATE TABLE IF NOT EXISTS mood_tags (
                    id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                    user_id INT(11) NOT NULL,
                    mood_id INT(11) DEFAULT NULL,
                    date DATE NOT NULL,
                    tag_name VARCHAR(50) NOT NULL,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    KEY user_id (user_id),
                    KEY mood_id (mood_id),
                    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                    FOREIGN KEY (mood_id) REFERENCES mood_logs(id) ON DELETE SET NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
            ");
        }
        
        // Media uploads table
        if ($isPostgres) {
            $pdo->exec("
                CREATE TABLE IF NOT EXISTS media_uploads (
                    id SERIAL PRIMARY KEY,
                    user_id INTEGER NOT NULL,
                    diary_id INTEGER,
                    date DATE NOT NULL,
                    media_type VARCHAR(10) NOT NULL CHECK (media_type IN ('photo', 'video')),
                    file_path VARCHAR(255) NOT NULL,
                    file_size INTEGER,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                    FOREIGN KEY (diary_id) REFERENCES diary_entries(id) ON DELETE SET NULL
                )
            ");
        } else {
            $pdo->exec("
                CREATE TABLE IF NOT EXISTS media_uploads (
                    id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                    user_id INT(11) NOT NULL,
                    diary_id INT(11) DEFAULT NULL,
                    date DATE NOT NULL,
                    media_type ENUM('photo', 'video') NOT NULL,
                    file_path VARCHAR(255) NOT NULL,
                    file_size INT(11) DEFAULT NULL,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    KEY user_id (user_id),
                    KEY diary_id (diary_id),
                    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                    FOREIGN KEY (diary_id) REFERENCES diary_entries(id) ON DELETE SET NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
            ");
        }
        
        return [
            'success' => true,
            'message' => 'Database initialized successfully',
            'database_type' => getDbType()
        ];
    } catch (Exception $e) {
        error_log("Database initialization error: " . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Database initialization failed: ' . $e->getMessage(),
            'database_type' => getDbType()
        ];
    }
}

$result = init_db();
echo json_encode($result, JSON_PRETTY_PRINT);

