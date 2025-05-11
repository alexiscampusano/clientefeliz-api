<?php
declare(strict_types=1);

namespace App\v1\models;

use App\config\Database;
use PDO;

class UserModel {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function findAll() {
        $stmt = $this->db->query("SELECT * FROM User");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findById($id) {
        $stmt = $this->db->prepare("SELECT * FROM User WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function findByEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM User WHERE email = :email");
        $stmt->execute([':email' => $email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function findByRol($role) {
        $stmt = $this->db->prepare("SELECT * FROM User WHERE role = :role");
        $stmt->execute([':role' => $role]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function save($data) {
        $stmt = $this->db->prepare("INSERT INTO User (first_name, last_name, email, password, birth_date, phone, address, role)
                                   VALUES (:first_name, :last_name, :email, :password, :birth_date, :phone, :address, :role)");
        
        $stmt->execute([
            ':first_name' => htmlspecialchars(strip_tags($data['name'] ?? '')),
            ':last_name' => htmlspecialchars(strip_tags($data['lastname'] ?? '')),
            ':email' => htmlspecialchars(strip_tags($data['email'])),
            ':password' => password_hash($data['password'], PASSWORD_DEFAULT),
            ':birth_date' => $data['birth_date'] ?? null,
            ':phone' => htmlspecialchars(strip_tags($data['phone'] ?? '')),
            ':address' => htmlspecialchars(strip_tags($data['address'] ?? '')),
            ':role' => htmlspecialchars(strip_tags($data['role']))
        ]);
        
        if ($stmt->rowCount()) {
            $id = $this->db->lastInsertId();
            return $this->findById($id);
        }
        
        return false;
    }

    public function update($id, $data) {
        $fields = [];
        $values = [':id' => $id];

        if (isset($data['first_name'])) {
            $fields[] = "first_name = :first_name";
            $values[':first_name'] = htmlspecialchars(strip_tags($data['first_name']));
        }

        if (isset($data['last_name'])) {
            $fields[] = "last_name = :last_name";
            $values[':last_name'] = htmlspecialchars(strip_tags($data['last_name']));
        }

        if (isset($data['email'])) {
            $fields[] = "email = :email";
            $values[':email'] = htmlspecialchars(strip_tags($data['email']));
        }

        if (isset($data['password'])) {
            $fields[] = "password = :password";
            $values[':password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }

        if (isset($data['birth_date'])) {
            $fields[] = "birth_date = :birth_date";
            $values[':birth_date'] = $data['birth_date'];
        }

        if (isset($data['phone'])) {
            $fields[] = "phone = :phone";
            $values[':phone'] = htmlspecialchars(strip_tags($data['phone']));
        }

        if (isset($data['address'])) {
            $fields[] = "address = :address";
            $values[':address'] = htmlspecialchars(strip_tags($data['address']));
        }

        if (isset($data['role'])) {
            $fields[] = "role = :role";
            $values[':role'] = htmlspecialchars(strip_tags($data['role']));
        }

        if (isset($data['status'])) {
            $fields[] = "status = :status";
            $values[':status'] = htmlspecialchars(strip_tags($data['status']));
        }

        if (empty($fields)) {
            return false;
        }

        $sql = "UPDATE User SET " . implode(", ", $fields) . " WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        
        return $stmt->execute($values);
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM User WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    public function authenticate($email, $password) {
        $user = $this->findByEmail($email);
        
        if (!$user) {
            return false;
        }
        
        return password_verify($password, $user['password']) ? $user : false;
    }
} 