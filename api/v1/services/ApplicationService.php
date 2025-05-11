<?php
declare(strict_types=1);

/**
 * Application management service for API v1
 */
class ApplicationService {
    private $applicationModel;
    private $jobOfferModel;

    /**
     * Constructor with dependency injection
     * 
     * @param \App\v1\models\ApplicationModel $applicationModel Application model
     * @param \App\v1\models\JobOfferModel $jobOfferModel Job offer model
     */
    public function __construct(
        \App\v1\models\ApplicationModel $applicationModel,
        \App\v1\models\JobOfferModel $jobOfferModel
    ) {
        $this->applicationModel = $applicationModel;
        $this->jobOfferModel = $jobOfferModel;
    }

    /**
     * Apply for a job offer
     * 
     * @param int $candidateId Candidate ID
     * @param int $jobOfferId Job offer ID
     * @param string $message Application message
     * @return array Created application data
     * @throws Exception If there is an error applying
     */
    public function apply(int $candidateId, int $jobOfferId, string $message = ''): array {
        // Check if there is already an application for this candidate and offer
        $existing = $this->applicationModel->findByUserAndJobOffer($candidateId, $jobOfferId);
        
        if ($existing) {
            throw new \Exception('You have already applied to this job offer');
        }
        
        // Prepare application data
        $applicationData = [
            'candidate_id' => $candidateId,
            'job_offer_id' => $jobOfferId,
            'status' => 'Applied',
            'message' => $message
        ];
        
        // Save the application
        $applicationId = $this->applicationModel->save($applicationData);
        
        if (!$applicationId) {
            throw new \Exception('Error saving the application');
        }
        
        // Return the created application data
        return [
            'id' => $applicationId,
            'candidate_id' => $candidateId,
            'job_offer_id' => $jobOfferId,
            'status' => 'Applied',
            'application_date' => date('Y-m-d H:i:s'),
            'message' => $message
        ];
    }

    /**
     * Update application status
     * 
     * @param int $applicationId The application ID
     * @param string $status The new status
     * @param string|null $comment Optional comment about the status change
     * @param int $recruiterId The recruiter's user ID
     * @return array The updated application
     * @throws Exception If the update fails or the recruiter doesn't own the offer
     */
    public function updateStatus(int $applicationId, string $status, ?string $comment, int $recruiterId): array {
        // Get the application
        $application = $this->applicationModel->findById($applicationId);
        
        if (!$application) {
            throw new \Exception('Application not found', 404);
        }
        
        // Check if the recruiter owns the job offer
        $jobOffer = $this->jobOfferModel->findById($application['job_offer_id']);
        
        if (!$jobOffer) {
            throw new \Exception('Job offer not found', 404);
        }
        
        if ($jobOffer['recruiter_id'] != $recruiterId) {
            throw new \Exception('You do not have permission to update this application', 403);
        }
        
        // Validate the status
        $validStatuses = ['Applied', 'Reviewing', 'Psychological Interview', 'Personal Interview', 'Selected', 'Rejected'];
        
        if (!in_array($status, $validStatuses)) {
            throw new \Exception('Invalid status', 400);
        }
        
        // Update the application
        $data = [
            'application_status' => $status
        ];
        
        if ($comment !== null) {
            $data['comment'] = $comment;
        }
        
        $updated = $this->applicationModel->update($applicationId, $data);
        
        if (!$updated) {
            throw new \Exception('Failed to update application status');
        }
        
        $application = $this->applicationModel->findById($applicationId);
        
        if (!$application) {
            throw new \Exception('Failed to retrieve updated application');
        }
        
        return $application;
    }

    /**
     * Get applications by candidate
     * 
     * @param int $candidateId The candidate's user ID
     * @return array The list of applications
     * @throws Exception If there's an error retrieving the applications
     */
    public function getApplicationsByCandidate(int $candidateId): array {
        return $this->applicationModel->findByCandidate($candidateId);
    }

    /**
     * Get application details
     * 
     * @param int $applicationId The application ID
     * @param int $userId The user ID (candidate or recruiter)
     * @param string $role The user role ('Candidate' or 'Recruiter')
     * @return array The application details
     * @throws Exception If the retrieval fails or the user doesn't have permission
     */
    public function getApplicationDetails(int $applicationId, int $userId, string $role): array {
        $application = $this->applicationModel->findById($applicationId);
        
        if (!$application) {
            throw new \Exception('Application not found', 404);
        }
        
        // Check permissions: candidate can only view their own applications
        if ($role === 'Candidate' && $application['candidate_id'] != $userId) {
            throw new \Exception('You do not have permission to view this application', 403);
        }
        
        // Recruiters can only view applications for their job offers
        if ($role === 'Recruiter') {
            $jobOffer = $this->jobOfferModel->findById($application['job_offer_id']);
            
            if (!$jobOffer || $jobOffer['recruiter_id'] != $userId) {
                throw new \Exception('You do not have permission to view this application', 403);
            }
        }
        
        return $application;
    }
} 