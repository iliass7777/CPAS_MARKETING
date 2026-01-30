<?php
// Fix schema by working on a copy to avoid locks
$dbDir = __DIR__ . '/../db';
$originalDb = $dbDir . '/ratings_platform.sqlite';
$offlineDb = $dbDir . '/ratings_platform_fixed.sqlite';
$backupDb = $dbDir . '/ratings_platform_backup_' . time() . '.sqlite';

try {
    echo "1. Copying database to offline file...\n";
    if (!copy($originalDb, $offlineDb)) {
        throw new Exception("Failed to copy database.");
    }
    
    // Preserve permissions
    chmod($offlineDb, fileperms($originalDb));
    
    echo "2. Fixing schema on offline copy...\n";
    $pdo = new PDO('sqlite:' . $offlineDb);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Disable FKs for schema change
    $pdo->exec("PRAGMA foreign_keys = OFF;");
    
    // Check schema
    $stmt = $pdo->query("SELECT sql FROM sqlite_master WHERE type='table' AND name='reviews'");
    $schema = $stmt->fetchColumn();
    
    if (strpos($schema, 'users_old') !== false) {
        echo "   Broken schema detected. Applying fix...\n";
        
        $pdo->beginTransaction();
        
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
        
        // Copy data
        $pdo->exec("INSERT INTO reviews_new (id, website_id, user_id, author_name, author_email, rating, comment, status, created_at, updated_at)
                    SELECT id, website_id, user_id, author_name, author_email, rating, comment, status, created_at, updated_at FROM reviews");
        
        $pdo->exec("DROP TABLE reviews");
        $pdo->exec("ALTER TABLE reviews_new RENAME TO reviews");
        $pdo->exec("CREATE INDEX IF NOT EXISTS idx_website ON reviews(website_id)");
        $pdo->exec("CREATE INDEX IF NOT EXISTS idx_status ON reviews(status)");
        
        $pdo->commit();
        echo "   Schema fixed successfully.\n";
        
        // Re-enable FKs to verify
        $pdo->exec("PRAGMA foreign_keys = ON;");
        
        // Close connection to unlock offline file
        $pdo = null;
        
        echo "3. Swapping database files...\n";
        // Rename original to backup
        if (!rename($originalDb, $backupDb)) {
            throw new Exception("Failed to backup original database.");
        }
        
        // Rename fixed to original
        if (!rename($offlineDb, $originalDb)) {
            // Try to restore backup if swap fails
            rename($backupDb, $originalDb);
            throw new Exception("Failed to swap database files.");
        }
        
        echo "SUCCESS: Database fixed and swapped. Backup saved to " . basename($backupDb) . "\n";
        
    } else {
        echo "   Schema seems fine. No fix needed.\n";
        unlink($offlineDb); // Cleanup
    }
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    if (file_exists($offlineDb)) {
        unlink($offlineDb);
    }
}
