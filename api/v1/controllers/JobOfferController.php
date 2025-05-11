<?php
declare(strict_types=1);

namespace App\v1\controllers;

use App\v1\models\JobOfferModel;
use App\v1\models\UserModel;
use App\v1\utils\ResponseHandler;

/**
 * Handles job offer operations
 * Implements Single Responsibility Principle by focusing only on job offer management
 */
class JobOfferController {
    private $jobOfferService;

    public function __construct() {
        $jobOfferModel = new \App\v1\models\JobOfferModel();
        $applicationModel = new \App\v1\models\ApplicationModel();
        $this->jobOfferService = new \JobOfferService($jobOfferModel, $applicationModel);
    }

    /**
     * Creates a new job offer
     * @param array $data Job offer data
     * @return array Response with created job offer
     */
    public function create(array $data) {
        // Validate required fields
        $requiredFields = ['title', 'description', 'location', 'salary', 'contract_type'];
        $errors = [];
        
        foreach ($requiredFields as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                $errors[$field] = [ucfirst($field) . ' is required'];
            }
        }

        if (!empty($errors)) {
            return [
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $errors
            ];
        }

        // Validate data types
        if (!is_numeric($data['salary']) || $data['salary'] <= 0) {
            $errors['salary'] = ['Salary must be a positive number'];
        }

        if (!empty($errors)) {
            return [
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $errors
            ];
        }

        $jobOffer = $this->jobOfferService->create($data);
        if (!$jobOffer) {
            return [
                'status' => 'error',
                'message' => 'Failed to create job offer',
                'errors' => null
            ];
        }

        return [
            'status' => 'success',
            'data' => $jobOffer,
            'message' => 'Job offer created successfully'
        ];
    }

    /**
     * Creates a new job offer via API endpoint
     * @param array $data Job offer data from request
     * @param array $params Not used
     * @param array $headers Headers with authorization
     * @return void
     */
    public function createOffer(array $data = [], array $params = [], array $headers = []) {
        // Get authenticated user data from token
        $tokenData = \JwtHandler::validateToken($headers['Authorization'] ?? '');
        if (empty($tokenData) || !isset($tokenData['data']['id'])) {
            \ResponseHandler::sendError('Unauthorized', 401);
            return;
        }
        
        // Verificar que el usuario sea un reclutador
        if (!isset($tokenData['data']['role']) || $tokenData['data']['role'] !== 'Recruiter') {
            \ResponseHandler::sendError('Access denied. Only recruiters can create job offers.', 403);
            return;
        }
        
        // Set publication date
        $data['publication_date'] = date('Y-m-d');
        
        // Set status as active
        $data['status'] = 'Active';
        
        try {
            // Use the service to create the offer
            $jobOffer = $this->jobOfferService->createOffer($data, $tokenData['data']['id']);
            \ResponseHandler::sendJson($jobOffer, 201, 'Job offer created successfully');
        } catch (\Exception $e) {
            \ResponseHandler::sendError($e->getMessage(), $e->getCode() ?: 400);
        }
    }

    /**
     * Retrieves a job offer by ID
     * @param array $data Not used
     * @param array $params URL parameters, including ID
     * @param array $headers Not used
     * @return void
     */
    public function getOfferById(array $data = [], array $params = [], array $headers = []) {
        if (!isset($params['id']) || !is_numeric($params['id'])) {
            \ResponseHandler::sendError('Invalid job offer ID', 400);
            return;
        }
        
        try {
            $jobOffer = $this->jobOfferService->getOfferById((int)$params['id']);
            if (!$jobOffer) {
                \ResponseHandler::sendError('Job offer not found', 404);
                return;
            }
            \ResponseHandler::sendJson($jobOffer, 200, 'Job offer details retrieved successfully');
        } catch (\Exception $e) {
            \ResponseHandler::sendError($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    /**
     * Lists job offers with optional filters
     * @param array $filters Filter criteria
     * @return array Response with paginated job offers
     */
    public function list(array $filters = []) {
        $jobOffers = $this->jobOfferService->findAll($filters);
        return [
            'status' => 'success',
            'data' => [
                'items' => $jobOffers,
                'total' => count($jobOffers),
                'page' => $filters['page'] ?? 1,
                'per_page' => $filters['per_page'] ?? 10
            ],
            'message' => 'Job offers retrieved successfully'
        ];
    }

    /**
     * Updates a job offer
     * @param int $id Job offer ID
     * @param array $data Update data
     * @return array Response with updated job offer
     */
    public function update(int $id, array $data) {
        $jobOffer = $this->jobOfferService->findById($id);
        if (!$jobOffer) {
            return [
                'status' => 'error',
                'message' => 'Job offer not found',
                'errors' => null
            ];
        }

        // Validate data types if provided
        $errors = [];
        if (isset($data['salary']) && (!is_numeric($data['salary']) || $data['salary'] <= 0)) {
            $errors['salary'] = ['Salary must be a positive number'];
        }

        if (!empty($errors)) {
            return [
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $errors
            ];
        }

        $updatedJobOffer = $this->jobOfferService->update($id, $data);
        if (!$updatedJobOffer) {
            return [
                'status' => 'error',
                'message' => 'Failed to update job offer',
                'errors' => null
            ];
        }

        return [
            'status' => 'success',
            'data' => $updatedJobOffer,
            'message' => 'Job offer updated successfully'
        ];
    }

    /**
     * Deactivates a job offer
     * @param int $id Job offer ID
     * @return array Response with operation result
     */
    public function deactivate(int $id) {
        $jobOffer = $this->jobOfferService->findById($id);
        if (!$jobOffer) {
            return [
                'status' => 'error',
                'message' => 'Job offer not found',
                'errors' => null
            ];
        }

        $result = $this->jobOfferService->update($id, ['status' => 'Inactive']);
        if (!$result) {
            return [
                'status' => 'error',
                'message' => 'Failed to deactivate job offer',
                'errors' => null
            ];
        }

        return [
            'status' => 'success',
            'data' => null,
            'message' => 'Job offer deactivated successfully'
        ];
    }

    /**
     * Lists active job offers
     * @param array $data Not used
     * @param array $params Not used
     * @param array $headers Not used
     * @return void
     */
    public function getActiveOffers($data = [], $params = [], $headers = []) {
        try {
            $activeOffers = $this->jobOfferService->getActiveOffers();
            \ResponseHandler::sendJson([
                'items' => $activeOffers,
                'total' => count($activeOffers)
            ], 200, 'Active job offers retrieved successfully');
        } catch (\Exception $e) {
            \ResponseHandler::sendError($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    /**
     * Updates an existing job offer via API endpoint
     * @param array $data Update data
     * @param array $params URL parameters, including ID
     * @param array $headers Headers with authorization
     * @return void
     */
    public function updateOffer(array $data = [], array $params = [], array $headers = []) {
        if (!isset($params['id']) || !is_numeric($params['id'])) {
            \ResponseHandler::sendError('Invalid job offer ID', 400);
            return;
        }
        
        // Get authenticated user data from token
        $tokenData = \JwtHandler::validateToken($headers['Authorization'] ?? '');
        if (empty($tokenData) || !isset($tokenData['data']['id'])) {
            \ResponseHandler::sendError('Unauthorized', 401);
            return;
        }
        
        // Verificar que el usuario sea un reclutador
        if (!isset($tokenData['data']['role']) || $tokenData['data']['role'] !== 'Recruiter') {
            \ResponseHandler::sendError('Access denied. Only recruiters can update job offers.', 403);
            return;
        }
        
        try {
            $updatedOffer = $this->jobOfferService->updateOffer(
                (int)$params['id'],
                $data,
                $tokenData['data']['id']
            );
            \ResponseHandler::sendJson($updatedOffer, 200, 'Job offer updated successfully');
        } catch (\Exception $e) {
            \ResponseHandler::sendError($e->getMessage(), $e->getCode() ?: 400);
        }
    }

    /**
     * Deactivates a job offer via API endpoint
     * @param array $data Not used
     * @param array $params URL parameters, including ID
     * @param array $headers Headers with authorization
     * @return void
     */
    public function deactivateOffer(array $data = [], array $params = [], array $headers = []) {
        if (!isset($params['id']) || !is_numeric($params['id'])) {
            \ResponseHandler::sendError('Invalid job offer ID', 400);
            return;
        }
        
        // Get authenticated user data from token
        $tokenData = \JwtHandler::validateToken($headers['Authorization'] ?? '');
        if (empty($tokenData) || !isset($tokenData['data']['id'])) {
            \ResponseHandler::sendError('Unauthorized', 401);
            return;
        }
        
        // Verificar que el usuario sea un reclutador
        if (!isset($tokenData['data']['role']) || $tokenData['data']['role'] !== 'Recruiter') {
            \ResponseHandler::sendError('Access denied. Only recruiters can deactivate job offers.', 403);
            return;
        }
        
        try {
            $success = $this->jobOfferService->deactivateOffer(
                (int)$params['id'],
                $tokenData['data']['id']
            );
            
            if ($success) {
                \ResponseHandler::sendJson(null, 200, 'Job offer deactivated successfully');
            } else {
                \ResponseHandler::sendError('Failed to deactivate job offer', 500);
            }
        } catch (\Exception $e) {
            \ResponseHandler::sendError($e->getMessage(), $e->getCode() ?: 400);
        }
    }

    /**
     * Gets job offers for the current recruiter
     * @param array $data Not used
     * @param array $params Not used
     * @param array $headers Headers with authorization
     * @return void
     */
    public function getMyOffers(array $data = [], array $params = [], array $headers = []) {
        // Get authenticated user data from token
        $tokenData = \JwtHandler::validateToken($headers['Authorization'] ?? '');
        if (empty($tokenData) || !isset($tokenData['data']['id'])) {
            \ResponseHandler::sendError('Unauthorized', 401);
            return;
        }
        
        // Verificar que el usuario sea un reclutador
        if (!isset($tokenData['data']['role']) || $tokenData['data']['role'] !== 'Recruiter') {
            \ResponseHandler::sendError('Access denied. Only recruiters can view their job offers.', 403);
            return;
        }
        
        try {
            $myOffers = $this->jobOfferService->getOffersByRecruiter($tokenData['data']['id']);
            \ResponseHandler::sendJson([
                'items' => $myOffers,
                'total' => count($myOffers)
            ]);
        } catch (\Exception $e) {
            \ResponseHandler::sendError($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    /**
     * Gets applicants for a specific job offer
     * @param array $data Not used
     * @param array $params URL parameters, including job offer ID
     * @param array $headers Headers with authorization
     * @return void
     */
    public function getApplicants(array $data = [], array $params = [], array $headers = []) {
        if (!isset($params['id']) || !is_numeric($params['id'])) {
            \ResponseHandler::sendError('Invalid job offer ID', 400);
            return;
        }
        
        // Get authenticated user data from token
        $tokenData = \JwtHandler::validateToken($headers['Authorization'] ?? '');
        if (empty($tokenData) || !isset($tokenData['data']['id'])) {
            \ResponseHandler::sendError('Unauthorized', 401);
            return;
        }
        
        // Verificar que el usuario sea un reclutador
        if (!isset($tokenData['data']['role']) || $tokenData['data']['role'] !== 'Recruiter') {
            \ResponseHandler::sendError('Access denied. Only recruiters can view applicants.', 403);
            return;
        }
        
        try {
            $applicants = $this->jobOfferService->getApplicants(
                (int)$params['id'], 
                $tokenData['data']['id']
            );
            
            \ResponseHandler::sendJson([
                'items' => $applicants,
                'total' => count($applicants)
            ]);
        } catch (\PDOException $e) {
            // Catch error for non-existent table
            if (strpos($e->getMessage(), "Table 'cliente_feliz.applications' doesn't exist") !== false) {
                \ResponseHandler::sendJson([
                    'items' => [],
                    'total' => 0,
                    'message' => 'The applications table does not exist in the database. Database update required.'
                ]);
            } else {
                \ResponseHandler::sendError($e->getMessage(), 500);
            }
        } catch (\Exception $e) {
            \ResponseHandler::sendError($e->getMessage(), $e->getCode() ?: 400);
        }
    }

    /**
     * Permanently deletes a job offer via API endpoint
     * @param array $data Not used
     * @param array $params URL parameters, including ID
     * @param array $headers Headers with authorization
     * @return void
     */
    public function deleteOffer(array $data = [], array $params = [], array $headers = []) {
        if (!isset($params['id']) || !is_numeric($params['id'])) {
            \ResponseHandler::sendError('Invalid job offer ID', 400);
            return;
        }
        
        // Get authenticated user data from token
        $tokenData = \JwtHandler::validateToken($headers['Authorization'] ?? '');
        if (empty($tokenData) || !isset($tokenData['data']['id'])) {
            \ResponseHandler::sendError('Unauthorized', 401);
            return;
        }
        
        // Verificar que el usuario sea un reclutador
        if (!isset($tokenData['data']['role']) || $tokenData['data']['role'] !== 'Recruiter') {
            \ResponseHandler::sendError('Access denied. Only recruiters can delete job offers.', 403);
            return;
        }
        
        try {
            $success = $this->jobOfferService->deleteOffer(
                (int)$params['id'],
                $tokenData['data']['id']
            );
            
            if ($success) {
                \ResponseHandler::sendJson(null, 200, 'Job offer permanently deleted');
            } else {
                \ResponseHandler::sendError('Failed to delete job offer', 500);
            }
        } catch (\Exception $e) {
            \ResponseHandler::sendError($e->getMessage(), $e->getCode() ?: 400);
        }
    }
} 