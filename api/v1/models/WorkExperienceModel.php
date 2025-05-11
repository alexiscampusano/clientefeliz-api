<?php
declare(strict_types=1);

namespace App\v1\models;

use PDO;
use App\v1\models\BaseModel;

/**
 * WorkExperienceModel - Handle database operations related to work experiences
 */
class WorkExperienceModel extends BaseModel {
    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct('WorkExperience');
    }
    
    /**
     * Find work experiences by user ID
     * @param int $userId User ID
     * @return array List of work experiences
     */
    public function findByUser(int $userId): array {
        $query = "SELECT * FROM {$this->table} WHERE candidate_id = :candidate_id ORDER BY end_date DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':candidate_id' => $userId]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} 