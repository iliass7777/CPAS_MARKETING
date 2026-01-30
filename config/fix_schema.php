<?php
require_once __DIR__ . '/database.php';

echo "Starting database schema checks...\n";

$db = new Database();
$pdo = $db->getConnection();

// 1. Update Reviews Table
echo "Checking 'reviews' table for 'user_id' column...\n";
try {
    $stmt = $pdo->query("PRAGMA table_info(reviews)");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN, 1);
    
    if (!in_array('user_id', $columns)) {
        echo "Adding 'user_id' column to 'reviews' table...\n";
        $pdo->exec("ALTER TABLE reviews ADD COLUMN user_id INTEGER REFERENCES users(id) ON DELETE SET NULL");
        echo "Column 'user_id' added successfully.\n";
    } else {
        echo "'user_id' column already exists in 'reviews'.\n";
    }
} catch (Exception $e) {
    echo "Error updating reviews table: " . $e->getMessage() . "\n";
}

// 2. Update Users Table (Recreation method for SQLite to update CHECK constraint)
echo "Checking 'users' table schema...\n";
try {
    // Check if we need to update by inspecting sql
    $stmt = $pdo->query("SELECT sql FROM sqlite_master WHERE type='table' AND name='users'");
    $createSql = $stmt->fetchColumn();
    $stmt = null; // Close cursor to release lock
    
    // If the creation SQL doesn't contain 'user' in the CHECK constraint or defaults to editor without user option
    // Matches: role IN ('admin', 'editor') or similar
    if (strpos($createSql, "'user'") === false) {
        echo "Users table needs update to support 'user' role.\n";
        
        $pdo->beginTransaction();
        
        // Rename existing table
        $pdo->exec("ALTER TABLE users RENAME TO users_old");
        
        // Create new table
        $pdo->exec("
            CREATE TABLE users (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                username VARCHAR(255) NOT NULL UNIQUE,
                email VARCHAR(255) NOT NULL UNIQUE,
                password VARCHAR(255) NOT NULL,
                full_name VARCHAR(255),
                role TEXT DEFAULT 'user' CHECK (role IN ('admin', 'editor', 'user')),
                is_active INTEGER DEFAULT 1,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ");
        
        // Copy data
        // We need to list columns to avoid issues if schema matches/mismatches in other ways, 
        // but simple INSERT INTO ... SELECT * usually works if columns allow.
        // Explicitly mapping common columns is safer.
        $pdo->exec("
            INSERT INTO users (id, username, email, password, full_name, role, is_active, created_at, updated_at)
            SELECT id, username, email, password, full_name, role, is_active, created_at, updated_at 
            FROM users_old
        ");
        
        // Update indices
         $pdo->exec("CREATE INDEX IF NOT EXISTS idx_username ON users(username)");
         $pdo->exec("CREATE INDEX IF NOT EXISTS idx_email ON users(email)");
         $pdo->exec("CREATE INDEX IF NOT EXISTS idx_active ON users(is_active)");
        
        // Drop old table
        $pdo->exec("DROP TABLE users_old");
        
        $pdo->commit();
        echo "Users table updated successfully.\n";
    } else {
        echo "Users table already supports 'user' role.\n";
    }
    
} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo "Error updating users table: " . $e->getMessage() . "\n";
}

echo "Database schema check completed.\n";
