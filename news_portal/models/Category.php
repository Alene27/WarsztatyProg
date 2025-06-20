<?php
// models/Category.php
class Category {
    private $conn;
    private $table_name = "categories";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAll() {
        $query = "SELECT c.*, u.username as author_name 
                  FROM " . $this->table_name . " c 
                  LEFT JOIN users u ON c.author_id = u.id 
                  ORDER BY c.name";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getBySlug($slug) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE slug = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$slug]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $query = "INSERT INTO " . $this->table_name . " 
                  (name, slug, description, author_id) 
                  VALUES (?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($query);
        
        return $stmt->execute([
            $data['name'],
            $data['slug'],
            $data['description'],
            $data['author_id']
        ]);
    }
}
?>