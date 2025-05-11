<?php
declare(strict_types=1);

namespace App\v1\controllers;

use App\v1\models\ApplicationModel;
use App\v1\models\JobOfferModel;
use App\v1\models\UserModel;
use ResponseHandler;

/**
 * Handles job application operations
 * Implements Single Responsibility Principle by focusing only on application management
 */
class ApplicationController {
    private $applicationModel;
    private $jobOfferModel;
    private $userModel;

    public function __construct(
        ApplicationModel $applicationModel,
        JobOfferModel $jobOfferModel,
        UserModel $userModel
    ) {
        $this->applicationModel = $applicationModel;
        $this->jobOfferModel = $jobOfferModel;
        $this->userModel = $userModel;
    }

    /**
     * Creates a new job application
     * @param array $data Application data
     * @return array Response with created application
     */
    public function create(array $data) {
        // Validate required fields
        $requiredFields = ['job_offer_id', 'user_id', 'resume_url', 'cover_letter'];
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

        // Validate job offer exists and is active
        $jobOffer = $this->jobOfferModel->findById($data['job_offer_id']);
        if (!$jobOffer) {
            return [
                'status' => 'error',
                'message' => 'Job offer not found',
                'errors' => null
            ];
        }

        if ($jobOffer['status'] !== 'Active') {
            return [
                'status' => 'error',
                'message' => 'Job offer is not active',
                'errors' => null
            ];
        }

        // Validate user exists
        $user = $this->userModel->findById($data['user_id']);
        if (!$user) {
            return [
                'status' => 'error',
                'message' => 'User not found',
                'errors' => null
            ];
        }

        // Check if user already applied
        $existingApplication = $this->applicationModel->findOne([
            'job_offer_id' => $data['job_offer_id'],
            'user_id' => $data['user_id']
        ]);

        if ($existingApplication) {
            return [
                'status' => 'error',
                'message' => 'User has already applied to this job offer',
                'errors' => null
            ];
        }

        $application = $this->applicationModel->save($data);
        if (!$application) {
            return [
                'status' => 'error',
                'message' => 'Failed to create application',
                'errors' => null
            ];
        }

        return [
            'status' => 'success',
            'data' => $application,
            'message' => 'Application created successfully'
        ];
    }

    /**
     * Retrieves an application by ID
     * @param int $id Application ID
     * @return array Response with application data
     */
    public function getById(int $id) {
        $application = $this->applicationModel->findById($id);
        if (!$application) {
            return [
                'status' => 'error',
                'message' => 'Application not found',
                'errors' => null
            ];
        }

        return [
            'status' => 'success',
            'data' => $application,
            'message' => 'Application retrieved successfully'
        ];
    }

    /**
     * Lists applications with optional filters
     * @param array $filters Filter criteria
     * @return array Response with paginated applications
     */
    public function list(array $filters = []) {
        $applications = $this->applicationModel->findAll(null, 'ASC', $filters);
        return [
            'status' => 'success',
            'data' => [
                'items' => $applications,
                'total' => count($applications),
                'page' => $filters['page'] ?? 1,
                'per_page' => $filters['per_page'] ?? 10
            ],
            'message' => 'Applications retrieved successfully'
        ];
    }

    /**
     * Updates application status
     * @param int $id Application ID
     * @param array $data Update data
     * @return array Response with updated application
     */
    public function updateStatus(int $id, array $data) {
        $application = $this->applicationModel->findById($id);
        if (!$application) {
            return [
                'status' => 'error',
                'message' => 'Application not found',
                'errors' => null
            ];
        }

        // Validate status
        $validStatuses = ['Applied', 'Reviewing', 'Psychological Interview', 'Personal Interview', 'Selected', 'Rejected'];
        if (!isset($data['status']) || !in_array($data['status'], $validStatuses)) {
            return [
                'status' => 'error',
                'message' => 'Invalid status',
                'errors' => [
                    'status' => ['Status must be one of: ' . implode(', ', $validStatuses)]
                ]
            ];
        }

        $updatedApplication = $this->applicationModel->update($id, $data);
        if (!$updatedApplication) {
            return [
                'status' => 'error',
                'message' => 'Failed to update application',
                'errors' => null
            ];
        }

        return [
            'status' => 'success',
            'data' => $updatedApplication,
            'message' => 'Application status updated successfully'
        ];
    }

    /**
     * Retrieves applications by user ID
     * @param int $userId User ID
     * @return array Response with user's applications
     */
    public function getByUserId(int $userId) {
        $applications = $this->applicationModel->findAll(null, 'ASC', ['user_id' => $userId]);
        return [
            'status' => 'success',
            'data' => [
                'items' => $applications,
                'total' => count($applications)
            ],
            'message' => 'User applications retrieved successfully'
        ];
    }

    /**
     * Retrieves applications by job offer ID
     * @param int $jobOfferId Job offer ID
     * @return array Response with job offer applications
     */
    public function getByJobOfferId(int $jobOfferId) {
        $applications = $this->applicationModel->findAll(null, 'ASC', ['job_offer_id' => $jobOfferId]);
        return [
            'status' => 'success',
            'data' => [
                'items' => $applications,
                'total' => count($applications)
            ],
            'message' => 'Job offer applications retrieved successfully'
        ];
    }

    /**
     * API endpoint for candidates to apply to a job offer
     * @param array $data Application data
     * @param array $params Not used
     * @param array $headers Headers with authorization
     * @return void
     */
    public function apply(array $data = [], array $params = [], array $headers = []) {
        // Get authenticated user data from token
        $tokenData = \JwtHandler::validateToken($headers['Authorization'] ?? '');
        if (empty($tokenData) || !isset($tokenData['data']['id'])) {
            ResponseHandler::sendError('Unauthorized', 401);
            return;
        }
        
        // Validate required fields
        if (!isset($data['job_offer_id']) || empty($data['job_offer_id'])) {
            ResponseHandler::sendError('Job offer ID is required', 400);
            return;
        }
        
        // Verify that the job offer exists and is active
        $jobOfferId = (int)$data['job_offer_id'];
        $jobOffer = $this->jobOfferModel->findById($jobOfferId);
        
        if (!$jobOffer) {
            ResponseHandler::sendError('Job offer not found', 404);
            return;
        }
        
        if (isset($jobOffer['status']) && $jobOffer['status'] !== 'Active') {
            ResponseHandler::sendError('Job offer is not active', 400);
            return;
        }
        
        try {
            // Check if the user has already applied
            $existingApplication = $this->applicationModel->findByUserAndJobOffer(
                $tokenData['data']['id'], 
                $jobOfferId
            );
            
            if ($existingApplication) {
                ResponseHandler::sendError('You have already applied to this job offer', 400);
                return;
            }
            
            // Prepare data to save the application
            $applicationData = [
                'job_offer_id' => $jobOfferId,
                'candidate_id' => $tokenData['data']['id'],
                'cover_letter' => $data['cover_letter'] ?? null,
                'status' => 'Applied',
                'created_at' => date('Y-m-d H:i:s')
            ];
            
            // Save the application
            $applicationId = $this->applicationModel->save($applicationData);
            
            if (!$applicationId) {
                ResponseHandler::sendError('Failed to create application', 500);
                return;
            }
            
            // Get the complete application data
            $application = $this->applicationModel->findById($applicationId);
            
            ResponseHandler::sendJson($application, 201, 'Application submitted successfully');
        } catch (\PDOException $e) {
            // Catch table not existing error
            if (strpos($e->getMessage(), "Table 'cliente_feliz.applications' doesn't exist") !== false) {
                ResponseHandler::sendError(
                    'The applications system is currently unavailable. Please try again later.',
                    500
                );
                return;
            }
            
            // Rethrow other exceptions
            throw $e;
        }
    }

    /**
     * API endpoint to get current user's applications (Candidate only)
     * @param array $data Not used
     * @param array $params Not used
     * @param array $headers Headers with authorization
     * @return void
     */
    public function getMyApplications(array $data = [], array $params = [], array $headers = []) {
        // Get authenticated user data from token
        $tokenData = \JwtHandler::validateToken($headers['Authorization'] ?? '');
        if (empty($tokenData) || !isset($tokenData['data']['id']) || !isset($tokenData['data']['role'])) {
            ResponseHandler::sendError('Unauthorized', 401);
            return;
        }

        // Verify user is a candidate
        if ($tokenData['data']['role'] !== 'Candidate') {
            ResponseHandler::sendError('Access denied. This endpoint is only for candidates', 403);
            return;
        }

        try {
            $userId = $tokenData['data']['id'];
            
            // Get candidate's applications
            $applications = $this->applicationModel->findByCandidate($userId);
            
            ResponseHandler::sendJson([
                'items' => $applications,
                'total' => count($applications)
            ]);
        } catch (\PDOException $e) {
            // Catch table not existing error
            if (strpos($e->getMessage(), "Table 'cliente_feliz.applications' doesn't exist") !== false) {
                ResponseHandler::sendJson([
                    'items' => [],
                    'total' => 0,
                    'message' => 'The applications system is currently unavailable. Please try again later.'
                ]);
                return;
            }
            
            // Rethrow other exceptions
            throw $e;
        }
    }

    /**
     * API endpoint to get application details
     * @param array $data Not used
     * @param array $params URL parameters, including application ID
     * @param array $headers Headers with authorization
     * @return void
     */
    public function getApplicationDetails(array $data = [], array $params = [], array $headers = []) {
        if (!isset($params['id']) || !is_numeric($params['id'])) {
            ResponseHandler::sendError('Invalid application ID', 400);
            return;
        }
        
        // Get authenticated user data from token
        $tokenData = \JwtHandler::validateToken($headers['Authorization'] ?? '');
        if (empty($tokenData) || !isset($tokenData['data']['id'])) {
            ResponseHandler::sendError('Unauthorized', 401);
            return;
        }
        
        $applicationId = (int)$params['id'];
        
        try {
            // Get the application details
            $application = $this->applicationModel->findById($applicationId);
            
            if (!$application) {
                ResponseHandler::sendError('Application not found', 404);
                return;
            }
            
            // Verify that the user is either the candidate or the recruiter of the offer
            $isCandidate = isset($application['candidate_id']) && $application['candidate_id'] == $tokenData['data']['id'];
            
            // Find the job offer to verify if the user is the recruiter
            $jobOffer = $this->jobOfferModel->findById($application['job_offer_id'] ?? 0);
            $isRecruiter = $jobOffer && isset($jobOffer['recruiter_id']) && $jobOffer['recruiter_id'] == $tokenData['data']['id'];
            
            if (!$isCandidate && !$isRecruiter) {
                ResponseHandler::sendError('You are not authorized to view this application', 403);
                return;
            }
            
            // Include additional details
            if ($jobOffer) {
                $application['job_offer'] = [
                    'id' => $jobOffer['id'],
                    'title' => $jobOffer['title'],
                    'description' => $jobOffer['description'],
                    'location' => $jobOffer['location'],
                    'salary' => $jobOffer['salary']
                ];
            }
            
            ResponseHandler::sendJson($application);
        } catch (\PDOException $e) {
            // Catch table not existing error
            if (strpos($e->getMessage(), "Table 'cliente_feliz.applications' doesn't exist") !== false) {
                ResponseHandler::sendError(
                    'The applications system is currently unavailable. Please try again later.',
                    500
                );
                return;
            }
            
            // Rethrow other exceptions
            throw $e;
        }
    }

    /**
     * API endpoint to update application status
     * @param array $data Update data
     * @param array $params URL parameters, including application ID
     * @param array $headers Headers with authorization
     * @return void
     */
    public function updateApplicationStatus(array $data = [], array $params = [], array $headers = []) {
        if (!isset($params['id']) || !is_numeric($params['id'])) {
            ResponseHandler::sendError('Invalid application ID', 400);
            return;
        }
        
        // Get authenticated user data from token
        $tokenData = \JwtHandler::validateToken($headers['Authorization'] ?? '');
        if (empty($tokenData) || !isset($tokenData['data']['id'])) {
            ResponseHandler::sendError('Unauthorized', 401);
            return;
        }
        
        // Validate the new status
        if (!isset($data['status']) || empty($data['status'])) {
            ResponseHandler::sendError('Status is required', 400);
            return;
        }
        
        $applicationId = (int)$params['id'];
        $newStatus = $data['status'];
        
        // Validate that the status is valid
        $validStatuses = ['Applied', 'Reviewing', 'Psychological Interview', 'Personal Interview', 'Selected', 'Rejected'];
        if (!in_array($newStatus, $validStatuses)) {
            ResponseHandler::sendError('Invalid status. Must be one of: ' . implode(', ', $validStatuses), 400);
            return;
        }
        
        try {
            // Get the application
            $application = $this->applicationModel->findById($applicationId);
            
            if (!$application) {
                ResponseHandler::sendError('Application not found', 404);
                return;
            }
            
            // Find the job offer to verify if the user is the recruiter
            $jobOffer = $this->jobOfferModel->findById($application['job_offer_id'] ?? 0);
            $isRecruiter = $jobOffer && isset($jobOffer['recruiter_id']) && $jobOffer['recruiter_id'] == $tokenData['data']['id'];
            
            if (!$isRecruiter) {
                ResponseHandler::sendError('You are not authorized to update this application', 403);
                return;
            }
            
            // Update the status
            $updateData = [
                 'application_status' => $newStatus,
                'comment' => $data['feedback'] ?? null
            ];
            
            $updated = $this->applicationModel->update($applicationId, $updateData);
            
            if (!$updated) {
                ResponseHandler::sendError('Failed to update application status', 500);
                return;
            }
            
            // Get the updated application
            $updatedApplication = $this->applicationModel->findById($applicationId);
            
            ResponseHandler::sendJson($updatedApplication, 200, 'Application status updated successfully');
        } catch (\PDOException $e) {
            // Catch table not existing error
            if (strpos($e->getMessage(), "Table 'cliente_feliz.applications' doesn't exist") !== false) {
                ResponseHandler::sendError(
                    'The applications system is currently unavailable. Please try again later.',
                    500
                );
                return;
            }
            
            // Rethrow other exceptions
            throw $e;
        }
    }
} 