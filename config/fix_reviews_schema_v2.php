<?php
// Direct connection to avoid Database class overhead/locking
$dbPath = __DIR__ . '/../db/ratings_platform.sqlite';

try {
    $pdo = new PDO('sqlite:' . $dbPath);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec("PRAGMA busy_timeout = 5000;"); // Wait up to 5s if locked
    
    echo "Connecting to database...\n";
    
    // Disable foreign keys to allow schema changes even if constraints are violated
    $pdo->exec("PRAGMA foreign_keys = OFF;");
    
    // Check if we need to fix
    $stmt = $pdo->query("SELECT sql FROM sqlite_master WHERE type='table' AND name='reviews'");
    $schema = $stmt->fetchColumn();
    $stmt->closeCursor();
    
    if (strpos($schema, 'users_old') !== false) {
        echo "Detected broken schema referencing 'users_old'. Fixing...\n";
        
        $maxRetries = 5;
        $retryCount = 0;
        $success = false;
        
        while ($retryCount < $maxRetries && !$success) {
            try {
                $pdo->beginTransaction();
                
                // 1. Create new table with correct schema
                $createSql = "CREATE TABLE reviews_new (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    website_id INTEGER NOT NULL,
                    user_id INTEGER,
                    author_name VARCHAR(255) NOT NULL,
                    author_email VARCHAR(255),
                    rating INTEGER NOT NULL CHECK (rating >= 1 AND rating <= 5),
                    comment TEXT,
                    status TEXT DEFAULT 'pending' CHECK (status IN ('pending', 'approved', 'rejected')),
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    FOREIGN KEY (website_id) REFERENCES websites(id) ON DELETE CASCADE,
                    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
                )";
                $pdo->exec($createSql);
                
                // 2. Copy data
                $pdo->exec("INSERT INTO reviews_new (id, website_id, user_id, author_name, author_email, rating, comment, status, created_at, updated_at)
                            SELECT id, website_id, user_id, author_name, author_email, rating, comment, status, created_at, updated_at FROM reviews");
                
                // 3. Drop old table
                $pdo->exec("DROP TABLE reviews");
                
                // 4. Rename new table
                $pdo->exec("ALTER TABLE reviews_new RENAME TO reviews");
                
                // 5. Create indexes
                $pdo->exec("CREATE INDEX IF NOT EXISTS idx_website ON reviews(website_id)");
                $pdo->exec("CREATE INDEX IF NOT EXISTS idx_status ON reviews(status)");
                
                $pdo->commit();
                $success = true;
                echo "Fixed 'reviews' table successfully.\n";
                
            } catch (PDOException $e) {
                if ($pdo->inTransaction()) {
                    $pdo->rollBack();
                }
                if (strpos($e->getMessage(), 'locked') !== false) {
                    $retryCount++;
                    echo "Database locked. Retrying ($retryCount/$maxRetries)...\n";
                    sleep(2);
                } else {
                    throw $e;
                }
            }
        }
        
        if (!$success) {
            echo "Failed to fix table after $maxRetries attempts due to lock.\n";
        }
        
    } else {
        echo "Table 'reviews' does not reference 'users_old'. No fix needed.\n";
        echo "Current schema: " . $schema . "\n";
    }
    
    // Re-enable foreign keys
    $pdo->exec("PRAGMA foreign_keys = ON;");
    
} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo "Error: " . $e->getMessage() . "\n";
}
