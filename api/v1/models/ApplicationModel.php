<?php
declare(strict_types=1);

namespace App\v1\models;

use PDO;
use App\config\Database;
use App\v1\models\BaseModel;

/**
 * Handles application data operations
 */
class ApplicationModel extends BaseModel {
    public function __construct() {
        parent::__construct('Application');
    }

    /**
     * Finds a single application matching the given criteria
     * @param array $criteria Search criteria
     * @return array|null Application data or null if not found
     */
    public function findOne(array $criteria): ?array {
        $sql = "SELECT * FROM {$this->table} WHERE ";
        $conditions = [];
        $params = [];

        foreach ($criteria as $field => $value) {
            $conditions[] = "$field = :$field";
            $params[":$field"] = $value;
        }

        $sql .= implode(' AND ', $conditions);
        $sql .= " LIMIT 1";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result ?: null;
    }

    /**
     * Finds all applications matching the given criteria
     * @param string $orderBy Order by field
     * @param string $direction Order direction
     * @param array $filters Search filters
     * @return array List of applications
     */
    public function findAll(?string $orderBy = null, string $direction = 'ASC', array $filters = []): array {
        $sql = "SELECT * FROM {$this->table}";
        $params = [];
        if (!empty($filters)) {
            $sql .= " WHERE ";
            $conditions = [];
            foreach ($filters as $field => $value) {
                $conditions[] = "$field = ?";
                $params[] = $value;
            }
            $sql .= implode(' AND ', $conditions);
        }
        if ($orderBy) {
            $sql .= " ORDER BY {$orderBy} {$direction}";
        }
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Finds applications by candidate ID
     * @param int $candidateId Candidate ID
     * @return array List of applications with job offer details
     */
    public function findByCandidate(int $candidateId): array {
        $sql = "
            SELECT a.*, jo.title, jo.description, jo.location, jo.salary 
            FROM {$this->table} a
            JOIN JobOffer jo ON a.job_offer_id = jo.id
            WHERE a.candidate_id = :candidate_id
            ORDER BY a.application_date DESC
        ";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':candidate_id' => $candidateId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Finds applications by job offer ID
     * @param int $jobOfferId Job offer ID
     * @return array List of applications with user details
     */
    public function findByJobOffer(int $jobOfferId): array {
        $sql = "
            SELECT a.*, u.first_name, u.last_name, u.email 
            FROM {$this->table} a
            JOIN User u ON a.candidate_id = u.id
            WHERE a.job_offer_id = :job_offer_id
            ORDER BY a.application_date DESC
        ";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':job_offer_id' => $jobOfferId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Finds application by job offer and candidate IDs
     * @param int $jobOfferId Job offer ID
     * @param int $candidateId Candidate ID
     * @return array|null Application data or null if not found
     */
    public function findByJobOfferAndCandidate(int $jobOfferId, int $candidateId): ?array {
        return $this->findOne([
            'job_offer_id' => $jobOfferId,
            'candidate_id' => $candidateId
        ]);
    }

    /**
     * Find application by user and job offer
     * Alias for findByJobOfferAndCandidate
     */
    public function findByUserAndJobOffer(int $userId, int $jobOfferId): ?array {
        return $this->findByJobOfferAndCandidate($jobOfferId, $userId);
    }

    /**
     * Saves a new application
     * @param array $data Application data
     * @return int|false New application ID or false on failure
     */
    public function save(array $data): int|false {
        $sql = "
            INSERT INTO {$this->table} 
            (candidate_id, job_offer_id, application_status, comment) 
            VALUES (?, ?, ?, ?)
        ";
        
        $stmt = $this->conn->prepare($sql);
        $success = $stmt->execute([
            $data['candidate_id'],
            $data['job_offer_id'],
            $data['status'] ?? 'Applied',
            $data['cover_letter'] ?? null
        ]);
        
        return $success ? (int)$this->conn->lastInsertId() : false;
    }

    /**
     * Updates application status
     * @param int $id Application ID
     * @param string $status New status
     * @param string|null $comment Optional comment
     * @return bool Success status
     */
    public function updateStatus(int $id, string $status, ?string $comment = null): bool {
        return $this->update($id, [
            'application_status' => $status,
            'comment' => $comment
        ]);
    }

    /**
     * Updates application data
     * @param int $id Application ID
     * @param array $data Update data
     * @return bool Success status
     */
    public function update(int $id, array $data): bool {
        $fields = [];
        $values = [];

        if (isset($data['application_status'])) {
            $fields[] = "application_status = ?";
            $values[] = $data['application_status'];
        }

        if (isset($data['comment'])) {
            $fields[] = "comment = ?";
            $values[] = $data['comment'];
        }

        if (empty($fields)) {
            return false;
        }

        $values[] = $id;
        $sql = "UPDATE {$this->table} SET " . implode(", ", $fields) . " WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        
        return $stmt->execute($values);
    }

    /**
     * Deletes an application
     * @param int $id Application ID
     * @return bool Success status
     */
    public function delete(int $id): bool {
        $sql = "DELETE FROM {$this->table} WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$id]);
    }
} 