<?php
declare(strict_types=1);

namespace App\v1\models;

use PDO;
use App\v1\models\BaseModel;

/**
 * AcademicBackgroundModel - Handle database operations related to academic backgrounds
 */
class AcademicBackgroundModel extends BaseModel {
    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct('AcademicBackground');
    }
    
    /**
     * Find academic backgrounds by user ID
     * @param int $userId User ID
     * @return array List of academic backgrounds
     */
    public function findByUser(int $userId): array {
        $query = "SELECT * FROM {$this->table} WHERE candidate_id = :candidate_id ORDER BY end_year DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':candidate_id' => $userId]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} 