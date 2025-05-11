<?php
declare(strict_types=1);

/**
 * Authentication Service for API v1
 */
class AuthService {
    private $userModel;

    /**
     * Constructor with optional dependency injection for testing
     * @param \App\v1\models\UserModel|null $userModel Optional model for dependency injection
     */
    public function __construct(\App\v1\models\UserModel $userModel) {
        $this->userModel = $userModel;
    }

    /**
     * Authenticate user and return user data with token
     * 
     * @param string $email User email
     * @param string $password User password
     * @return array User data with JWT token
     * @throws Exception If authentication fails
     */
    public function login(string $email, string $password): array {
        $user = $this->userModel->findByEmail($email);
        
        if (!$user || !password_verify($password, $user['password'])) {
            throw new \Exception('Invalid credentials', 401);
        }
        
        $tokenData = [
            'id' => $user['id'],
            'email' => $user['email'],
            'role' => $user['role']
        ];
        
        $token = \JwtHandler::generateToken($tokenData);
        
        return [
            'token' => $token,
            'user' => $tokenData
        ];
    }

    /**
     * Register a new user
     * 
     * @param array $userData User registration data
     * @return array New user data with JWT token
     * @throws Exception If registration fails
     */
    public function register(array $userData): array {
        // Check if email already exists
        if ($this->userModel->findByEmail($userData['email'])) {
            throw new \Exception('This email is already in use', 400);
        }
        
        // UserModel already hashes the password, no need to do it here
        
        // Create user
        $user = $this->userModel->save($userData);
        
        if (!$user) {
            throw new \Exception('Failed to create user', 500);
        }
        
        $tokenData = [
            'id' => $user['id'],
            'email' => $user['email'],
            'role' => $user['role']
        ];
        
        $token = \JwtHandler::generateToken($tokenData);
        
        return [
            'token' => $token,
            'user' => $tokenData
        ];
    }

    /**
     * Validate authentication token
     * 
     * @param string $token JWT token
     * @return array User data from token
     * @throws Exception If token is invalid
     */
    public function validateToken(string $token): array {
        $userData = \JwtHandler::validateToken($token);
        
        if (!$userData || !isset($userData['data'])) {
            throw new \Exception('Invalid token', 401);
        }
        
        return $userData['data'];
    }
} 