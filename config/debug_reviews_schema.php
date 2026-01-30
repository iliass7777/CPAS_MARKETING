<?php
require_once __DIR__ . '/../config/database.php';

try {
    $db = new Database();
    $pdo = $db->getConnection();
    
    // Get the CREATE statement for the reviews table
    $stmt = $pdo->prepare("SELECT sql FROM sqlite_master WHERE type='table' AND name='reviews'");
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result) {
        echo "Current Schema for 'reviews':\n";
        echo $result['sql'] . "\n\n";
        
        if (strpos($result['sql'], 'REFERENCES "users_old"') !== false || strpos($result['sql'], 'REFERENCES users_old') !== false) {
            echo "Found reference to 'users_old'. Fixing...\n";
            
            $pdo->beginTransaction();
            
            // 1. Rename current table
            $pdo->exec("ALTER TABLE reviews RENAME TO reviews_old");
            
            // 2. Create new table
            $sql = "CREATE TABLE reviews (
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
            $pdo->exec($sql);
            
            // 3. Copy data
            $pdo->exec("INSERT INTO reviews (id, website_id, user_id, author_name, author_email, rating, comment, status, created_at, updated_at)
                        SELECT id, website_id, user_id, author_name, author_email, rating, comment, status, created_at, updated_at FROM reviews_old");
            
            // 4. Drop old table
            $pdo->exec("DROP TABLE reviews_old");
            
            $pdo->commit();
            echo "Successfully fixed 'reviews' table schema.\n";
        } else {
            echo "No reference to 'users_old' found. Table seems fine.\n";
        }
    } else {
        echo "Table 'reviews' not found.\n";
    }
    
} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo "Error: " . $e->getMessage() . "\n";
}
