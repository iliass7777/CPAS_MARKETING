<?php

require_once __DIR__ . '/../config/database.php';

class Category {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function getAll() {
        $sql = "SELECT * FROM categories ORDER BY name ASC";
        $result = $this->db->query($sql);
        $categories = [];
        if ($result) {
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $categories[] = $row;
            }
        }
        
        return $categories;
    }

    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM categories WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    }

    public function getBySlug($slug) {
        $stmt = $this->db->prepare("SELECT * FROM categories WHERE slug = ?");
        $stmt->execute([$slug]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
   
    }

    public function create($name, $slug, $description = '') {
        $stmt = $this->db->prepare("INSERT INTO categories (name, slug, description) VALUES (?, ?, ?)");
        if ($stmt->execute([$name, $slug, $description])) {
            return $this->db->lastInsertId();
        }
        return false;
    }

    public function update($id, $name, $slug, $description = '') {
        $stmt = $this->db->prepare("UPDATE categories SET name = ?, slug = ?, description = ? WHERE id = ?");
        return $stmt->execute([$name, $slug, $description, $id]);
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM categories WHERE id = ?");
        return $stmt->execute([$id]);
    }
}

