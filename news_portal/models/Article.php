<?php
// models/Article.php
class Article {
    private $conn;
    private $table_name = "articles";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getLatest($limit = 10) {
        $query = "SELECT a.*, u.username as author_name, c.name as category_name, c.slug as category_slug 
                  FROM " . $this->table_name . " a 
                  LEFT JOIN users u ON a.author_id = u.id 
                  LEFT JOIN categories c ON a.category_id = c.id 
                  ORDER BY a.published_at DESC 
                  LIMIT ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByCategory($category_slug, $limit = 10) {
        $query = "SELECT a.*, u.username as author_name, c.name as category_name, c.slug as category_slug 
                  FROM " . $this->table_name . " a 
                  LEFT JOIN users u ON a.author_id = u.id 
                  LEFT JOIN categories c ON a.category_id = c.id 
                  WHERE c.slug = ? 
                  ORDER BY a.published_at DESC 
                  LIMIT ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $category_slug);
        $stmt->bindParam(2, $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByAuthor($author_id, $limit = 10) {
        $query = "SELECT a.*, u.username as author_name, c.name as category_name, c.slug as category_slug 
                  FROM " . $this->table_name . " a 
                  LEFT JOIN users u ON a.author_id = u.id 
                  LEFT JOIN categories c ON a.category_id = c.id 
                  WHERE a.author_id = ? 
                  ORDER BY a.published_at DESC 
                  LIMIT ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $author_id, PDO::PARAM_INT);
        $stmt->bindParam(2, $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $query = "SELECT a.*, u.username as author_name, c.name as category_name, c.slug as category_slug 
                  FROM " . $this->table_name . " a 
                  LEFT JOIN users u ON a.author_id = u.id 
                  LEFT JOIN categories c ON a.category_id = c.id 
                  WHERE a.id = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $query = "INSERT INTO " . $this->table_name . " 
                  (title, content, excerpt, author_id, category_id, image_path) 
                  VALUES (?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($query);
        
        return $stmt->execute([
            $data['title'],
            $data['content'],
            $data['excerpt'],
            $data['author_id'],
            $data['category_id'],
            $data['image_path'] ?? null
        ]);
    }

    public function update($id, $data) {
        $query = "UPDATE " . $this->table_name . " 
                  SET title = ?, content = ?, excerpt = ?, category_id = ?, image_path = ? 
                  WHERE id = ?";
        
        $stmt = $this->conn->prepare($query);
        
        return $stmt->execute([
            $data['title'],
            $data['content'],
            $data['excerpt'],
            $data['category_id'],
            $data['image_path'],
            $id
        ]);
    }

    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$id]);
    }
}
?>