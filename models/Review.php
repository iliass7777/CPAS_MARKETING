<?php

require_once __DIR__ . '/../config/database.php';

class Review {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function getAll() {
        $sql = "SELECT r.*, w.name as website_name, w.url as website_url 
                FROM reviews r 
                LEFT JOIN websites w ON r.website_id = w.id 
                ORDER BY r.created_at DESC";
        $result = $this->db->query($sql);
        $reviews = [];
        
        if ($result && $result->rowCount() > 0) {
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $reviews[] = $row;
            }
        }
        
        return $reviews;
    }

    public function getById($id) {
        $stmt = $this->db->prepare("SELECT r.*, w.name as website_name, w.url as website_url 
                FROM reviews r 
                LEFT JOIN websites w ON r.website_id = w.id 
                WHERE r.id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    }

    public function getByWebsite($websiteId, $status = 'approved') {
        $stmt = $this->db->prepare("SELECT * FROM reviews 
                WHERE website_id = ? AND status = ? 
                ORDER BY created_at DESC");
        $stmt->execute([$websiteId, $status]);
        $reviews = [];
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $reviews[] = $row;
        }
        
        return $reviews;
    }

    public function create($websiteId, $authorName, $authorEmail, $rating, $comment) {
        $rating = (int)$rating;
        
        if ($rating < 1 || $rating > 5) {
            return false;
        }
        
        $stmt = $this->db->prepare("INSERT INTO reviews (website_id, author_name, author_email, rating, comment) 
                VALUES (?, ?, ?, ?, ?)");
        
        if ($stmt->execute([$websiteId, $authorName, $authorEmail, $rating, $comment])) {
            return $this->db->lastInsertId();
        }
        
        return false;
    }

    public function update($id, $authorName, $authorEmail, $rating, $comment, $status = null) {
        $rating = (int)$rating;
        
        if ($rating < 1 || $rating > 5) {
            return false;
        }
        
        if ($status !== null && in_array($status, ['pending', 'approved', 'rejected'])) {
            $stmt = $this->db->prepare("UPDATE reviews SET author_name = ?, author_email = ?, 
                    rating = ?, comment = ?, status = ? WHERE id = ?");
            $result = $stmt->execute([$authorName, $authorEmail, $rating, $comment, $status, $id]);
        } else {
            $stmt = $this->db->prepare("UPDATE reviews SET author_name = ?, author_email = ?, 
                    rating = ?, comment = ? WHERE id = ?");
            $result = $stmt->execute([$authorName, $authorEmail, $rating, $comment, $id]);
        }
        
        if ($result) {
            // Update website rating if status changed to approved or from approved
            if ($status !== null) {
                $review = $this->getById($id);
                if (!empty($review) && isset($review['website_id'])) {
                    $website = new Website();
                    $website->updateRating($review['website_id']);
                }
            }
            return true;
        }
        
        return false;
    }

    public function updateStatus($id, $status) {
        if (!in_array($status, ['pending', 'approved', 'rejected'])) {
            return false;
        }
        
        $stmt = $this->db->prepare("UPDATE reviews SET status = ? WHERE id = ?");
        
        if ($stmt->execute([$status, $id])) {
            // Update website rating when review status changes
            $review = $this->getById($id);
            if (!empty($review) && isset($review['website_id'])) {
                $website = new Website();
                $website->updateRating($review['website_id']);
            }
            return true;
        }
        
        return false;
    }

    public function delete($id) {
        // Get website_id before deleting to update rating
        $review = $this->getById($id);
        $websiteId = !empty($review) && isset($review['website_id']) ? $review['website_id'] : null;
        
        $stmt = $this->db->prepare("DELETE FROM reviews WHERE id = ?");
        
        if ($stmt->execute([$id])) {
            // Update website rating after deletion
            if ($websiteId) {
                $website = new Website();
                $website->updateRating($websiteId);
            }
            return true;
        }
        
        return false;
    }
}

