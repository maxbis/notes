<?php
// Database connection and operations

class Database {
    private static $instance = null;
    private $pdo;
    
    private function __construct() {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
            $this->pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
            ]);
        } catch (PDOException $e) {
            if (defined('DEBUG_MODE') && DEBUG_MODE) {
                throw new Exception("Database connection failed: " . $e->getMessage());
            } else {
                throw new Exception("Database connection failed");
            }
        }
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function getConnection() {
        return $this->pdo;
    }
    
    // Generate a unique hash ID for notes
    public function generateHashId() {
        do {
            $bytes = random_bytes(16);
            $hash = rtrim(strtr(base64_encode($bytes), '+/', '-_'), '=');
            $hash = substr($hash, 0, 22); // Ensure 22 characters
            
            // Check if hash already exists
            $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM notes WHERE hash_id = ?");
            $stmt->execute([$hash]);
        } while ($stmt->fetchColumn() > 0);
        
        return $hash;
    }
    
    // Get notes with pagination and search
    public function getNotes($search = '', $sort = 'updated', $offset = 0, $limit = 20) {
        $whereClause = '';
        $params = [];
        
        if (!empty($search)) {
            $whereClause = "WHERE title LIKE ? OR content LIKE ?";
            $searchTerm = "%{$search}%";
            $params = [$searchTerm, $searchTerm];
        }
        
        $orderBy = $sort === 'created' ? 'created_at DESC' : 'updated_at DESC';
        
        $sql = "SELECT * FROM notes {$whereClause} ORDER BY {$orderBy} LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchAll();
    }
    
    // Get total count for pagination
    public function getNotesCount($search = '') {
        $whereClause = '';
        $params = [];
        
        if (!empty($search)) {
            $whereClause = "WHERE title LIKE ? OR content LIKE ?";
            $searchTerm = "%{$search}%";
            $params = [$searchTerm, $searchTerm];
        }
        
        $sql = "SELECT COUNT(*) FROM notes {$whereClause}";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchColumn();
    }
    
    // Get note by hash ID
    public function getNoteByHash($hashId) {
        $stmt = $this->pdo->prepare("SELECT * FROM notes WHERE hash_id = ?");
        $stmt->execute([$hashId]);
        return $stmt->fetch();
    }
    
    // Create new note
    public function createNote($title, $content) {
        $hashId = $this->generateHashId();
        
        $stmt = $this->pdo->prepare("INSERT INTO notes (hash_id, title, content) VALUES (?, ?, ?)");
        $stmt->execute([$hashId, $title, $content]);
        
        return $hashId;
    }
    
    // Update existing note
    public function updateNote($hashId, $title, $content) {
        $stmt = $this->pdo->prepare("UPDATE notes SET title = ?, content = ?, updated_at = CURRENT_TIMESTAMP WHERE hash_id = ?");
        return $stmt->execute([$title, $content, $hashId]);
    }
    
    // Delete note
    public function deleteNote($hashId) {
        $stmt = $this->pdo->prepare("DELETE FROM notes WHERE hash_id = ?");
        return $stmt->execute([$hashId]);
    }
    
    // Check if note exists
    public function noteExists($hashId) {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM notes WHERE hash_id = ?");
        $stmt->execute([$hashId]);
        return $stmt->fetchColumn() > 0;
    }
}
