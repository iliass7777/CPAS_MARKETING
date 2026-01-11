<?php
require_once (__DIR__ . '/constants.php');

class Database {
    private $pdo;

    public function __construct() {
        $this->connection();
        $this->createTables();
    
    }
    public function connection(){
        $dbDir = __DIR__ . '/../db';
        $path = $dbDir . '/' . DB_NAME . '.sqlite';
        mkdir($dbDir, 0755, true);
        if (!is_dir($dbDir)) {
            mkdir($dbDir, 0755, true);
        }
        try{
            $this->pdo = new PDO('sqlite:' . $path);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $this->pdo;
        }catch(PDOException $e){
             throw new Exception('Database connection failed: ' . $e->getMessage());
        }
    }
    public function createTables(){
        // Activer les foreign keys pour SQLite
        $this->pdo->exec("PRAGMA foreign_keys = ON");
        
        $sql = "
        -- Categories table
        CREATE TABLE IF NOT EXISTS categories (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name VARCHAR(255) NOT NULL UNIQUE,
            slug VARCHAR(255) NOT NULL UNIQUE,
            description TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );

        -- Websites table (leaders)
        CREATE TABLE IF NOT EXISTS websites (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            category_id INTEGER NOT NULL,
            name VARCHAR(255) NOT NULL,
            url VARCHAR(500) NOT NULL,
            description TEXT,
            rating DECIMAL(3,2) DEFAULT 0.00,
            total_ratings INTEGER DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
        );

        -- Reviews table
        CREATE TABLE IF NOT EXISTS reviews (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            website_id INTEGER NOT NULL,
            author_name VARCHAR(255) NOT NULL,
            author_email VARCHAR(255),
            rating INTEGER NOT NULL CHECK (rating >= 1 AND rating <= 5),
            comment TEXT,
            status TEXT DEFAULT 'pending' CHECK (status IN ('pending', 'approved', 'rejected')),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (website_id) REFERENCES websites(id) ON DELETE CASCADE
        );

        -- Users table (for back office only)
        CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username VARCHAR(255) NOT NULL UNIQUE,
            email VARCHAR(255) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            full_name VARCHAR(255),
            role TEXT DEFAULT 'editor' CHECK (role IN ('admin', 'editor')),
            is_active INTEGER DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );

        -- Indexes pour améliorer les performances
        CREATE INDEX IF NOT EXISTS idx_category ON websites(category_id);
        CREATE INDEX IF NOT EXISTS idx_rating ON websites(rating);
        CREATE INDEX IF NOT EXISTS idx_website ON reviews(website_id);
        CREATE INDEX IF NOT EXISTS idx_status ON reviews(status);
        CREATE INDEX IF NOT EXISTS idx_username ON users(username);
        CREATE INDEX IF NOT EXISTS idx_email ON users(email);
        CREATE INDEX IF NOT EXISTS idx_active ON users(is_active);
        ";

        try {
            $this->pdo->exec($sql);
            // Insérer les données initiales si les tables sont vides
            $this->insertInitialData();
        } catch(PDOException $e) {
            throw new Exception('Failed to create tables: ' . $e->getMessage());
        }
    }

    private function insertInitialData() {
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM categories");
        $count = $stmt->fetchColumn();
        
        if ($count == 0) {
            $stmt = $this->pdo->prepare("INSERT INTO categories (name, slug, description) VALUES (?, ?, ?)");
            $stmt->execute(['Coding', 'coding', 'Best coding resources and tutorials']);
            $stmt->execute(['General Culture', 'general-culture', 'General knowledge and cultural resources']);
        }
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM users WHERE username = 'admin'");
        $count = $stmt->fetchColumn();
        
        if ($count == 0) {
            $hashedPassword = password_hash(ADMIN_PASSWORD, PASSWORD_DEFAULT);
            $stmt = $this->pdo->prepare("INSERT INTO users (username, email, password, full_name, role, is_active) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([ADMIN_USERNAME, ADMIN_EMAIL, $hashedPassword, 'Administrator', 'admin', 1]);
        }
    }

    public function getConnection() {
        return $this->pdo;
    }

}