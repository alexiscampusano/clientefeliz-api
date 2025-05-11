<?php
declare(strict_types=1);

namespace App\v1\controllers;

use App\v1\models\AcademicBackgroundModel;
use App\v1\models\WorkExperienceModel;
use App\v1\models\UserModel;
use ResponseHandler;

/**
 * Handles user profile operations
 * Implements Single Responsibility Principle by focusing only on profile management
 */
class ProfileController {
    private $profileService;

    public function __construct() {
        $userModel = new \App\v1\models\UserModel();
        $workExperienceModel = new \App\v1\models\WorkExperienceModel();
        $academicBackgroundModel = new \App\v1\models\AcademicBackgroundModel();
        $this->profileService = new \ProfileService(
            $userModel,
            $workExperienceModel,
            $academicBackgroundModel
        );
    }

    /**
     * API endpoint to get academic background data for the current user
     * @param array $data Not used
     * @param array $params Not used
     * @param array $headers Headers with authorization
     * @return void
     */
    public function getAcademicBackground(array $data = [], array $params = [], array $headers = []) {
        // Get authenticated user data from token
        $tokenData = \JwtHandler::validateToken($headers['Authorization'] ?? '');
        if (empty($tokenData) || !isset($tokenData['data']['id'])) {
            ResponseHandler::sendError('Unauthorized', 401);
            return;
        }

        try {
            // Get academic background for the user
            $userId = $tokenData['data']['id'];
            $academics = $this->profileService->getAcademicBackground($userId);
            
            ResponseHandler::sendJson([
                'items' => $academics,
                'total' => count($academics)
            ], 200, 'Academic background entries retrieved successfully');
        } catch (\PDOException $e) {
            // Catch table not existing error
            if (strpos($e->getMessage(), "doesn't exist") !== false) {
                ResponseHandler::sendJson([
                    'items' => [],
                    'total' => 0,
                    'message' => 'The academic background system is currently unavailable. Please try again later.'
                ]);
                return;
            }
            
            // Rethrow other exceptions
            throw $e;
        }
    }

    /**
     * API endpoint to add an academic background entry for the current user
     * @param array $data Academic background data
     * @param array $params Not used
     * @param array $headers Headers with authorization
     * @return void
     */
    public function addAcademicBackground(array $data = [], array $params = [], array $headers = []) {
        // Get authenticated user data from token
        $tokenData = \JwtHandler::validateToken($headers['Authorization'] ?? '');
        if (empty($tokenData) || !isset($tokenData['data']['id'])) {
            ResponseHandler::sendError('Unauthorized', 401);
            return;
        }
        
        // Validate required fields
        $requiredFields = ['institution', 'degree', 'start_year'];
        $errors = [];
        
        foreach ($requiredFields as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                $errors[$field] = ucfirst(str_replace('_', ' ', $field)) . ' is required';
            }
        }
        
        if (!empty($errors)) {
            ResponseHandler::sendError('Validation failed', 400, $errors);
            return;
        }
        
        // Add candidate ID to the record
        $data['candidate_id'] = $tokenData['data']['id'];
        
        try {
            // Add the academic background using the service
            $academic = $this->profileService->addAcademicBackground($data);
            ResponseHandler::sendJson($academic, 201, 'Academic background added successfully');
        } catch (\PDOException $e) {
            // Catch table not existing error
            if (strpos($e->getMessage(), "doesn't exist") !== false) {
                ResponseHandler::sendError(
                    'The academic background system is currently unavailable. Please try again later.',
                    500
                );
                return;
            }
            
            // Rethrow other exceptions
            throw $e;
        } catch (\Exception $e) {
            ResponseHandler::sendError($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    /**
     * API endpoint to get work experience data for the current user
     * @param array $data Not used
     * @param array $params Not used
     * @param array $headers Headers with authorization
     * @return void
     */
    public function getWorkExperience(array $data = [], array $params = [], array $headers = []) {
        // Get authenticated user data from token
        $tokenData = \JwtHandler::validateToken($headers['Authorization'] ?? '');
        if (empty($tokenData) || !isset($tokenData['data']['id'])) {
            ResponseHandler::sendError('Unauthorized', 401);
            return;
        }

        try {
            // Get work experience for the user
            $userId = $tokenData['data']['id'];
            $experiences = $this->profileService->getWorkExperience($userId);
            
            ResponseHandler::sendJson([
                'items' => $experiences,
                'total' => count($experiences)
            ], 200, 'Work experience entries retrieved successfully');
        } catch (\PDOException $e) {
            // Catch table not existing error
            if (strpos($e->getMessage(), "doesn't exist") !== false) {
                ResponseHandler::sendJson([
                    'items' => [],
                    'total' => 0,
                    'message' => 'The work experience system is currently unavailable. Please try again later.'
                ]);
                return;
            }
            
            // Rethrow other exceptions
            throw $e;
        }
    }

    /**
     * API endpoint to add a work experience entry for the current user
     * @param array $data Work experience data
     * @param array $params Not used
     * @param array $headers Headers with authorization
     * @return void
     */
    public function addWorkExperience(array $data = [], array $params = [], array $headers = []) {
        // Get authenticated user data from token
        $tokenData = \JwtHandler::validateToken($headers['Authorization'] ?? '');
        if (empty($tokenData) || !isset($tokenData['data']['id'])) {
            ResponseHandler::sendError('Unauthorized', 401);
            return;
        }
        
        // Validate required fields
        $requiredFields = ['company', 'position', 'start_date'];
        $errors = [];
        
        foreach ($requiredFields as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                $errors[$field] = ucfirst(str_replace('_', ' ', $field)) . ' is required';
            }
        }
        
        if (!empty($errors)) {
            ResponseHandler::sendError('Validation failed', 400, $errors);
            return;
        }
        
        // Add candidate ID to the record
        $data['candidate_id'] = $tokenData['data']['id'];
        
        try {
            // Add the work experience using the service
            $experience = $this->profileService->addWorkExperience($data);
            ResponseHandler::sendJson($experience, 201, 'Work experience added successfully');
        } catch (\PDOException $e) {
            // Catch table not existing error
            if (strpos($e->getMessage(), "doesn't exist") !== false) {
                ResponseHandler::sendError(
                    'The work experience system is currently unavailable. Please try again later.',
                    500
                );
                return;
            }
            
            // Rethrow other exceptions
            throw $e;
        } catch (\Exception $e) {
            ResponseHandler::sendError($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    /**
     * API endpoint to update a work experience entry
     * @param array $data Work experience data to update
     * @param array $params URL parameters, including work experience ID
     * @param array $headers Headers with authorization
     * @return void
     */
    public function updateWorkExperience(array $data = [], array $params = [], array $headers = []) {
        // Check if ID parameter exists
        if (!isset($params['id']) || !is_numeric($params['id'])) {
            ResponseHandler::sendError('Invalid work experience ID', 400);
            return;
        }
        
        // Get authenticated user data from token
        $tokenData = \JwtHandler::validateToken($headers['Authorization'] ?? '');
        if (empty($tokenData) || !isset($tokenData['data']['id'])) {
            ResponseHandler::sendError('Unauthorized', 401);
            return;
        }
        
        $workExperienceId = (int)$params['id'];
        $userId = $tokenData['data']['id'];
        
        try {
            // Update work experience using the service
            $experience = $this->profileService->updateWorkExperience($workExperienceId, $data, $userId);
            ResponseHandler::sendJson($experience, 200, 'Work experience updated successfully');
        } catch (\PDOException $e) {
            // Catch table not existing error
            if (strpos($e->getMessage(), "doesn't exist") !== false) {
                ResponseHandler::sendError(
                    'The work experience system is currently unavailable. Please try again later.',
                    500
                );
                return;
            }
            
            // Rethrow other exceptions
            throw $e;
        } catch (\Exception $e) {
            ResponseHandler::sendError($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    /**
     * API endpoint to delete a work experience entry
     * @param array $data Not used
     * @param array $params URL parameters, including work experience ID
     * @param array $headers Headers with authorization
     * @return void
     */
    public function deleteWorkExperience(array $data = [], array $params = [], array $headers = []) {
        // Check if ID parameter exists
        if (!isset($params['id']) || !is_numeric($params['id'])) {
            ResponseHandler::sendError('Invalid work experience ID', 400);
            return;
        }
        
        // Get authenticated user data from token
        $tokenData = \JwtHandler::validateToken($headers['Authorization'] ?? '');
        if (empty($tokenData) || !isset($tokenData['data']['id'])) {
            ResponseHandler::sendError('Unauthorized', 401);
            return;
        }
        
        $workExperienceId = (int)$params['id'];
        $userId = $tokenData['data']['id'];
        
        try {
            // Delete work experience using the service
            $result = $this->profileService->deleteWorkExperience($workExperienceId, $userId);
            ResponseHandler::sendJson(null, 200, 'Work experience deleted successfully');
        } catch (\PDOException $e) {
            // Catch table not existing error
            if (strpos($e->getMessage(), "doesn't exist") !== false) {
                ResponseHandler::sendError(
                    'The work experience system is currently unavailable. Please try again later.',
                    500
                );
                return;
            }
            
            // Rethrow other exceptions
            throw $e;
        } catch (\Exception $e) {
            ResponseHandler::sendError($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    /**
     * API endpoint to update an academic background entry
     * @param array $data Academic background data to update
     * @param array $params URL parameters, including academic background ID
     * @param array $headers Headers with authorization
     * @return void
     */
    public function updateAcademicBackground(array $data = [], array $params = [], array $headers = []) {
        // Check if ID parameter exists
        if (!isset($params['id']) || !is_numeric($params['id'])) {
            ResponseHandler::sendError('Invalid academic background ID', 400);
            return;
        }
        
        // Get authenticated user data from token
        $tokenData = \JwtHandler::validateToken($headers['Authorization'] ?? '');
        if (empty($tokenData) || !isset($tokenData['data']['id'])) {
            ResponseHandler::sendError('Unauthorized', 401);
            return;
        }
        
        $academicBackgroundId = (int)$params['id'];
        $userId = $tokenData['data']['id'];
        
        try {
            // Update academic background using the service
            $academic = $this->profileService->updateAcademicBackground($academicBackgroundId, $data, $userId);
            ResponseHandler::sendJson($academic, 200, 'Academic background updated successfully');
        } catch (\PDOException $e) {
            // Catch table not existing error
            if (strpos($e->getMessage(), "doesn't exist") !== false) {
                ResponseHandler::sendError(
                    'The academic background system is currently unavailable. Please try again later.',
                    500
                );
                return;
            }
            
            // Rethrow other exceptions
            throw $e;
        } catch (\Exception $e) {
            ResponseHandler::sendError($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    /**
     * API endpoint to delete an academic background entry
     * @param array $data Not used
     * @param array $params URL parameters, including academic background ID
     * @param array $headers Headers with authorization
     * @return void
     */
    public function deleteAcademicBackground(array $data = [], array $params = [], array $headers = []) {
        // Check if ID parameter exists
        if (!isset($params['id']) || !is_numeric($params['id'])) {
            ResponseHandler::sendError('Invalid academic background ID', 400);
            return;
        }
        
        // Get authenticated user data from token
        $tokenData = \JwtHandler::validateToken($headers['Authorization'] ?? '');
        if (empty($tokenData) || !isset($tokenData['data']['id'])) {
            ResponseHandler::sendError('Unauthorized', 401);
            return;
        }
        
        $academicBackgroundId = (int)$params['id'];
        $userId = $tokenData['data']['id'];
        
        try {
            // Delete academic background using the service
            $result = $this->profileService->deleteAcademicBackground($academicBackgroundId, $userId);
            ResponseHandler::sendJson(null, 200, 'Academic background deleted successfully');
        } catch (\PDOException $e) {
            // Catch table not existing error
            if (strpos($e->getMessage(), "doesn't exist") !== false) {
                ResponseHandler::sendError(
                    'The academic background system is currently unavailable. Please try again later.',
                    500
                );
                return;
            }
            
            // Rethrow other exceptions
            throw $e;
        } catch (\Exception $e) {
            ResponseHandler::sendError($e->getMessage(), $e->getCode() ?: 500);
        }
    }
} 