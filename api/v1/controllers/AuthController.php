<?php
declare(strict_types=1);

namespace App\v1\controllers;

use App\v1\models\UserModel;
require_once __DIR__ . '/../../utils/JwtHandler.php';
require_once __DIR__ . '/../../utils/TokenBlacklist.php';
use ResponseHandler;

/**
 * Handles user authentication operations
 * Implements Single Responsibility Principle by focusing only on auth operations
 */
class AuthController {
    private $authService;

    public function __construct() {
        $userModel = new \App\v1\models\UserModel();
        $this->authService = new \AuthService($userModel);
    }

    /**
     * Authenticates a user and returns a JWT token
     * @param array $data Login credentials
     * @param array $params Not used
     * @param array $headers Not used
     * @return void
     */
    public function login(array $data, array $params = [], array $headers = []) {
        if (!isset($data['email']) || !isset($data['password'])) {
            \ResponseHandler::sendError('Email and password are required', 400);
            return;
        }

        try {
            $result = $this->authService->login($data['email'], $data['password']);
            \ResponseHandler::sendJson($result, 200, 'User authenticated successfully');
        } catch (\Exception $e) {
            \ResponseHandler::sendError($e->getMessage(), $e->getCode() ?: 401);
        }
    }

    /**
     * Registers a new user
     * @param array $data User registration data
     * @param array $params Not used
     * @param array $headers Not used
     * @return void
     */
    public function register(array $data, array $params = [], array $headers = []) {
        // Validate required fields
        $requiredFields = ['email', 'password'];
        $errors = [];
        
        foreach ($requiredFields as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                $errors[$field] = [ucfirst($field) . ' is required'];
            }
        }

        if (!empty($errors)) {
            \ResponseHandler::sendValidationError($errors);
            return;
        }

        // Validate email format
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            \ResponseHandler::sendValidationError(['email' => ['Invalid email format']]);
            return;
        }

        // Set default role as "candidate" if not provided
        if (!isset($data['role']) || empty($data['role'])) {
            $data['role'] = 'candidate';
        }

        try {
            $result = $this->authService->register($data);
            \ResponseHandler::sendJson($result, 201, 'User registered successfully');
        } catch (\Exception $e) {
            if (strpos($e->getMessage(), 'already in use') !== false) {
                \ResponseHandler::sendValidationError(['email' => ['This email is already in use']]);
            } else {
                \ResponseHandler::sendError($e->getMessage(), $e->getCode() ?: 500);
            }
        }
    }

    /**
     * Retrieves current user information
     * @param array $data Not used
     * @param array $params Not used
     * @param array $headers Headers containing the JWT token
     * @return void
     */
    public function getCurrentUser(array $data = [], array $params = [], array $headers = []) {
        if (!isset($headers['Authorization'])) {
            \ResponseHandler::sendError('Authorization token required', 401);
            return;
        }
        
        try {
            $user = $this->authService->validateToken($headers['Authorization']);
            \ResponseHandler::sendJson($user, 200, 'Current user information retrieved successfully');
        } catch (\Exception $e) {
            \ResponseHandler::sendError($e->getMessage(), $e->getCode() ?: 401);
        }
    }

    /**
     * Logs out a user by adding their token to the blacklist
     * @param array $data Not used
     * @param array $params Not used
     * @param array $headers Headers containing the JWT token
     * @return void
     */
    public function logout(array $data = [], array $params = [], array $headers = []) {
        if (!isset($headers['Authorization'])) {
            \ResponseHandler::sendError('Authorization token required', 401);
            return;
        }
        
        $token = str_replace('Bearer ', '', $headers['Authorization']);
        
        // Get token expiration time
        $expiration = \JwtHandler::getTokenExpiration($token);
        
        if (!$expiration) {
            \ResponseHandler::sendError('Invalid token', 401);
            return;
        }
        
        // Add token to blacklist
        if (\TokenBlacklist::addToken($token, $expiration)) {
            \ResponseHandler::sendJson(null, 200, 'Logout successful');
        } else {
            \ResponseHandler::sendError('Failed to logout', 500);
        }
    }
} 