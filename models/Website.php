<?php

require_once __DIR__ . '/../config/database.php';

class Website {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function getAll($limit = null, $offset = 0) {
        $sql = "SELECT w.*, c.name as category_name, c.slug as category_slug 
                FROM websites w 
                LEFT JOIN categories c ON w.category_id = c.id 
                ORDER BY w.rating DESC, w.name ASC";

        $params = [];
        if ($limit !== null) {
            $limit = max(1, (int)$limit);
            $offset = max(0, (int)$offset);
            $sql .= " LIMIT :limit OFFSET :offset";
            $params = [
                ':limit' => $limit,
                ':offset' => $offset,
            ];
        }

        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            return [];
        }

        $stmt->execute($params);
        $websites = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $websites[] = $row;
        }

        return $websites;
    }

    public function getById($id) {
        $stmt = $this->db->prepare("SELECT w.*, c.name as category_name, c.slug as category_slug 
                FROM websites w 
                LEFT JOIN categories c ON w.category_id = c.id 
                WHERE w.id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    }

    public function getByCategory($categoryId) {
        $stmt = $this->db->prepare("SELECT w.*, c.name as category_name, c.slug as category_slug 
                FROM websites w 
                LEFT JOIN categories c ON w.category_id = c.id 
                WHERE w.category_id = ? 
                ORDER BY w.rating DESC, w.name ASC");
        $stmt->execute([$categoryId]);
        $websites = [];
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $websites[] = $row;
        }
        
        return $websites;
    }

    public function create($categoryId, $name, $url, $description = '') {
        $stmt = $this->db->prepare("INSERT INTO websites (category_id, name, url, description) 
                VALUES (?, ?, ?, ?)");
        
        if ($stmt->execute([$categoryId, $name, $url, $description])) {
            return $this->db->lastInsertId();
        }
        
        return false;
    }

    public function update($id, $categoryId, $name, $url, $description = '') {
        $stmt = $this->db->prepare("UPDATE websites SET category_id = ?, name = ?, 
                url = ?, description = ? WHERE id = ?");
        return $stmt->execute([$categoryId, $name, $url, $description, $id]);
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM websites WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function updateRating($id) {
        // Calculate average rating from approved reviews
        $stmt = $this->db->prepare("SELECT AVG(rating) as avg_rating, COUNT(*) as total_ratings 
                FROM reviews 
                WHERE website_id = ? AND status = 'approved'");
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($row) {
            $avgRating = $row['avg_rating'] ? round($row['avg_rating'], 2) : 0.00;
            $totalRatings = $row['total_ratings'] ? (int)$row['total_ratings'] : 0;
            
            $updateStmt = $this->db->prepare("UPDATE websites SET rating = ?, total_ratings = ? WHERE id = ?");
            return $updateStmt->execute([$avgRating, $totalRatings, $id]);
        }
        
        return false;
    }

    public function search($searchTerm, $limit = null, $offset = 0) {
        // Sécuriser les valeurs LIMIT et OFFSET
        $limit = $limit !== null ? max(1, (int)$limit) : 999999;
        $offset = max(0, (int)$offset);
        
        $searchTerm = '%' . $searchTerm . '%';
        
        $sql = "SELECT w.*, c.name as category_name, c.slug as category_slug 
                FROM websites w 
                LEFT JOIN categories c ON w.category_id = c.id 
                WHERE w.name LIKE ? OR w.description LIKE ?
                ORDER BY w.rating DESC, w.name ASC 
                LIMIT {$limit} OFFSET {$offset}";
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$searchTerm, $searchTerm]);
            
            $websites = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $websites[] = $row;
            }
            
            return $websites;
        } catch (PDOException $e) {
            error_log("Error in Website::search(): " . $e->getMessage());
            return [];
        }
    }
}

