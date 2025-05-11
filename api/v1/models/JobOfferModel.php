<?php
declare(strict_types=1);

namespace App\v1\models;

use App\config\Database;
use PDO;

class JobOfferModel {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    /**
     * Checks if the database connection is valid
     * @return bool
     */
    public function getConnection() {
        return $this->db !== null;
    }

    public function findAll($filters = []) {
        $whereClause = [];
        $params = [];
        
        if (!empty($filters['status'])) {
            $whereClause[] = "j.status = :status";
            $params[':status'] = $filters['status'];
        }
        
        if (!empty($filters['location'])) {
            $whereClause[] = "j.location LIKE :location";
            $params[':location'] = "%" . $filters['location'] . "%";
        }
        
        if (!empty($filters['title'])) {
            $whereClause[] = "j.title LIKE :title";
            $params[':title'] = "%" . $filters['title'] . "%";
        }
        
        $sql = "SELECT j.*, u.first_name as recruiter_name, u.last_name as recruiter_lastname
                FROM JobOffer j
                JOIN User u ON j.recruiter_id = u.id";
        
        if (!empty($whereClause)) {
            $sql .= " WHERE " . implode(" AND ", $whereClause);
        }
        
        $sql .= " ORDER BY j.publication_date DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findById($id) {
        $stmt = $this->db->prepare("
            SELECT j.*, u.first_name as recruiter_name, u.last_name as recruiter_lastname
            FROM JobOffer j
            JOIN User u ON j.recruiter_id = u.id
            WHERE j.id = :id
        ");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function findByRecruiter($recruiterId) {
        $stmt = $this->db->prepare("
            SELECT * FROM JobOffer
            WHERE recruiter_id = :recruiter_id
            ORDER BY publication_date DESC
        ");
        $stmt->execute([':recruiter_id' => $recruiterId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Alias for findByRecruiter
     * @param int $recruiterId Recruiter ID
     * @return array Job offers by the recruiter
     */
    public function findByRecruiterId($recruiterId) {
        return $this->findByRecruiter($recruiterId);
    }

    public function save($data) {
        $stmt = $this->db->prepare("
            INSERT INTO JobOffer (
                title, description, location, salary,
                contract_type, publication_date, closing_date, status, recruiter_id
            ) VALUES (
                :title, :description, :location, :salary,
                :contract_type, :publication_date, :closing_date, :status, :recruiter_id
            )
        ");
        
        $publicationDate = $data['publication_date'] ?? date('Y-m-d');
        
        $success = $stmt->execute([
            ':title' => htmlspecialchars(strip_tags($data['title'])),
            ':description' => htmlspecialchars(strip_tags($data['description'] ?? '')),
            ':location' => htmlspecialchars(strip_tags($data['location'] ?? '')),
            ':salary' => $data['salary'] ?? null,
            ':contract_type' => htmlspecialchars(strip_tags($data['contract_type'] ?? 'Indefinite')),
            ':publication_date' => $publicationDate,
            ':closing_date' => $data['closing_date'] ?? null,
            ':status' => htmlspecialchars(strip_tags($data['status'] ?? 'Active')),
            ':recruiter_id' => $data['recruiter_id']
        ]);
        
        return $success ? $this->db->lastInsertId() : false;
    }

    public function update($id, $data) {
        $fields = [];
        $values = [':id' => $id];

        if (isset($data['title'])) {
            $fields[] = "title = :title";
            $values[':title'] = htmlspecialchars(strip_tags($data['title']));
        }

        if (isset($data['description'])) {
            $fields[] = "description = :description";
            $values[':description'] = htmlspecialchars(strip_tags($data['description']));
        }

        if (isset($data['location'])) {
            $fields[] = "location = :location";
            $values[':location'] = htmlspecialchars(strip_tags($data['location']));
        }

        if (isset($data['salary'])) {
            $fields[] = "salary = :salary";
            $values[':salary'] = $data['salary'];
        }

        if (isset($data['contract_type'])) {
            $fields[] = "contract_type = :contract_type";
            $values[':contract_type'] = htmlspecialchars(strip_tags($data['contract_type']));
        }

        if (isset($data['closing_date'])) {
            $fields[] = "closing_date = :closing_date";
            $values[':closing_date'] = $data['closing_date'];
        }

        if (isset($data['status'])) {
            $fields[] = "status = :status";
            $values[':status'] = htmlspecialchars(strip_tags($data['status']));
        }

        if (empty($fields)) {
            return false;
        }

        $sql = "UPDATE JobOffer SET " . implode(", ", $fields) . " WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        
        return $stmt->execute($values);
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM JobOffer WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }
} 