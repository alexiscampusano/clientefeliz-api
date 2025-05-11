<?php
declare(strict_types=1);

namespace App\v1\models;

use PDO;
use App\config\Database;

require_once __DIR__ . '/../../config/Database.php';

/**
 * BaseModel - Abstract database operations for all models
 * 
 * This class implements the Repository Pattern and follows SOLID principles:
 * - Single Responsibility Principle: Handles only database operations
 * - Open/Closed Principle: Can be extended by specific models without modification
 * - Liskov Substitution Principle: Child models can be used wherever BaseModel is expected
 * - Interface Segregation: Provides only the methods needed for data access
 * - Dependency Inversion: High-level modules depend on this abstraction, not specific implementations
 * 
 * @package ClienteFeliz\Models
 */
class BaseModel {
    /**
     * @var PDO Database connection instance
     */
    protected $conn;
    
    /**
     * @var string The name of the table this model represents
     */
    protected $table;
    
    /**
     * Constructor - Initializes database connection and sets table name
     * 
     * @param string $table The database table name
     */
    public function __construct(string $table) {
        $database = new Database();
        $this->conn = $database->getConnection();
        $this->table = $table;
    }
    
    /**
     * Find a record by its ID
     * 
     * @param int $id The record ID
     * @return array|bool The found record as associative array or false if not found
     */
    public function findById(int $id) {
        $query = "SELECT * FROM {$this->table} WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':id' => $id]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Find all records in the table
     * 
     * @param string|null $orderBy Optional column to order by
     * @param string $direction Sorting direction (ASC/DESC)
     * @return array All records as associative array
     */
    public function findAll(?string $orderBy = null, string $direction = 'ASC'): array {
        $query = "SELECT * FROM {$this->table}";
        
        if ($orderBy) {
            $query .= " ORDER BY {$orderBy} {$direction}";
        }
        
        $stmt = $this->conn->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Save a new record to the database
     * 
     * @param array $data Associative array of column => value pairs
     * @return string|bool The last inserted ID or false on failure
     */
    public function save(array $data) {
        $columns = array_keys($data);
        $placeholders = array_map(function($col) {
            return ":{$col}";
        }, $columns);
        
        $columnsStr = implode(", ", $columns);
        $placeholdersStr = implode(", ", $placeholders);
        
        $query = "INSERT INTO {$this->table} ({$columnsStr}) VALUES ({$placeholdersStr})";
        $stmt = $this->conn->prepare($query);
        
        // Create parameters array with sanitized values
        $params = [];
        foreach ($data as $key => $value) {
            $params[":{$key}"] = $this->sanitize($value);
        }
        
        if ($stmt->execute($params)) {
            return $this->conn->lastInsertId();
        }
        
        return false;
    }
    
    /**
     * Update an existing record in the database
     * 
     * @param int $id The ID of the record to update
     * @param array $data Associative array of column => value pairs to update
     * @return bool True on success, false on failure
     */
    public function update(int $id, array $data): bool {
        $updates = [];
        $params = [':id' => $id];
        
        foreach ($data as $key => $value) {
            // Skip the ID field if it exists in data
            if ($key === 'id') continue;
            
            $updates[] = "{$key} = :{$key}";
            $params[":{$key}"] = $this->sanitize($value);
        }
        
        if (empty($updates)) {
            return false;
        }
        
        $updatesStr = implode(", ", $updates);
        $query = "UPDATE {$this->table} SET {$updatesStr} WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        
        return $stmt->execute($params);
    }
    
    /**
     * Delete a record from the database
     * 
     * @param int $id The ID of the record to delete
     * @return bool True on success, false on failure
     */
    public function delete(int $id): bool {
        $query = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        
        return $stmt->execute([':id' => $id]);
    }
    
    /**
     * Sanitize a value to prevent SQL injection
     * 
     * @param mixed $value The value to sanitize
     * @return mixed The sanitized value
     */
    protected function sanitize($value) {
        if (is_string($value)) {
            return htmlspecialchars(strip_tags($value), ENT_QUOTES, 'UTF-8');
        }
        
        return $value;
    }
    
    /**
     * Closes the database connection
     */
    public function __destruct() {
        if ($this->conn instanceof PDO) {
            $this->conn = null;
        }
    }
} 