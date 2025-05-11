<?php
declare(strict_types=1);

/**
 * Job Offer Management Service for API v1
 */
class JobOfferService {
    private $jobOfferModel;
    private $applicationModel;

    /**
     * Constructor with dependency injection
     * 
     * @param \App\v1\models\JobOfferModel $jobOfferModel Job offer model
     * @param \App\v1\models\ApplicationModel $applicationModel Application model
     */
    public function __construct(
        \App\v1\models\JobOfferModel $jobOfferModel,
        \App\v1\models\ApplicationModel $applicationModel
    ) {
        $this->jobOfferModel = $jobOfferModel;
        $this->applicationModel = $applicationModel;
    }

    /**
     * Get all active job offers
     * 
     * @return array The list of active job offers
     * @throws Exception If there's an error retrieving the offers
     */
    public function getActiveOffers(): array {
        $filters = ['status' => 'Active'];
        
        if (!$this->jobOfferModel->getConnection()) {
            throw new \Exception('Database connection error');
        }
        
        return $this->jobOfferModel->findAll($filters);
    }

    /**
     * Get job offer by ID
     * 
     * @param int $id The job offer ID
     * @return array|null The job offer details or null if not found
     * @throws Exception If there's an error retrieving the offer
     */
    public function getOfferById(int $id): ?array {
        return $this->jobOfferModel->findById($id);
    }

    /**
     * Create a new job offer
     * 
     * @param array $data The job offer data
     * @param int $recruiterId The recruiter's user ID
     * @return array The created job offer
     * @throws Exception If the offer creation fails
     */
    public function createOffer(array $data, int $recruiterId): array {
        $data['recruiter_id'] = $recruiterId;
        
        $offerId = $this->jobOfferModel->save($data);
        
        if (!$offerId) {
            throw new \Exception('Failed to create job offer');
        }
        
        $offerData = $this->jobOfferModel->findById($offerId);
        
        if (!$offerData) {
            throw new \Exception('Failed to retrieve created job offer');
        }
        
        return $offerData;
    }

    /**
     * Update an existing job offer
     * 
     * @param int $id The job offer ID
     * @param array $data The updated data
     * @param int $recruiterId The recruiter's user ID
     * @return array The updated job offer
     * @throws Exception If the update fails or the recruiter doesn't own the offer
     */
    public function updateOffer(int $id, array $data, int $recruiterId): array {
        $offer = $this->jobOfferModel->findById($id);
        
        if (!$offer) {
            throw new \Exception('Job offer not found', 404);
        }
        
        if ($offer['recruiter_id'] != $recruiterId) {
            throw new \Exception('You do not have permission to update this job offer', 403);
        }
        
        $updated = $this->jobOfferModel->update($id, $data);
        
        if (!$updated) {
            throw new \Exception('Failed to update job offer');
        }
        
        $offerData = $this->jobOfferModel->findById($id);
        
        if (!$offerData) {
            throw new \Exception('Failed to retrieve updated job offer');
        }
        
        return $offerData;
    }

    /**
     * Deactivate a job offer
     * 
     * @param int $id The job offer ID
     * @param int $recruiterId The recruiter's user ID
     * @return bool True if successful
     * @throws Exception If the deactivation fails or the recruiter doesn't own the offer
     */
    public function deactivateOffer(int $id, int $recruiterId): bool {
        $offer = $this->jobOfferModel->findById($id);
        
        if (!$offer) {
            throw new \Exception('Job offer not found', 404);
        }
        
        if ($offer['recruiter_id'] != $recruiterId) {
            throw new \Exception('You do not have permission to deactivate this job offer', 403);
        }
        
        $data = ['status' => 'Inactive'];
        $deactivated = $this->jobOfferModel->update($id, $data);
        
        if (!$deactivated) {
            throw new \Exception('Failed to deactivate job offer');
        }
        
        return true;
    }

    /**
     * Get job offers created by a recruiter
     * 
     * @param int $recruiterId The recruiter's user ID
     * @return array The list of job offers created by the recruiter
     * @throws Exception If there's an error retrieving the offers
     */
    public function getOffersByRecruiter(int $recruiterId): array {
        return $this->jobOfferModel->findByRecruiter($recruiterId);
    }

    /**
     * Get applicants for a job offer
     * 
     * @param int $offerId The job offer ID
     * @param int $recruiterId The recruiter's user ID
     * @return array The list of applicants
     * @throws Exception If the retrieval fails or the recruiter doesn't own the offer
     */
    public function getApplicants(int $offerId, int $recruiterId): array {
        $offer = $this->jobOfferModel->findById($offerId);
        
        if (!$offer) {
            throw new \Exception('Job offer not found', 404);
        }
        
        if ($offer['recruiter_id'] != $recruiterId) {
            throw new \Exception('You do not have permission to view applicants for this job offer', 403);
        }
        
        return $this->applicationModel->findByJobOffer($offerId);
    }
    
    /**
     * Generic method to create a job offer (for backward compatibility)
     * 
     * @param array $data The job offer data
     * @return array The created job offer
     */
    public function create(array $data): array {
        $offerId = $this->jobOfferModel->save($data);
        
        if (!$offerId) {
            return [];
        }
        
        $offerData = $this->jobOfferModel->findById($offerId);
        
        if (!$offerData) {
            return [];
        }
        
        return $offerData;
    }
    
    /**
     * Find all job offers with optional filters
     * 
     * @param array $filters Optional filters for the search
     * @return array List of job offers
     */
    public function findAll(array $filters = []): array {
        return $this->jobOfferModel->findAll($filters);
    }
    
    /**
     * Find a job offer by its ID
     * 
     * @param int $id The job offer ID
     * @return array|null The job offer or null if not found
     */
    public function findById(int $id): ?array {
        return $this->jobOfferModel->findById($id);
    }
    
    /**
     * Update a job offer
     * 
     * @param int $id The job offer ID
     * @param array $data The data to update
     * @return bool True if successful
     */
    public function update(int $id, array $data): bool {
        return $this->jobOfferModel->update($id, $data);
    }

    /**
     * Delete a job offer permanently
     * 
     * @param int $id The job offer ID
     * @param int $recruiterId The recruiter's user ID
     * @return bool True if successful
     * @throws Exception If the deletion fails or the recruiter doesn't own the offer
     */
    public function deleteOffer(int $id, int $recruiterId): bool {
        $offer = $this->jobOfferModel->findById($id);
        
        if (!$offer) {
            throw new \Exception('Job offer not found', 404);
        }
        
        if ($offer['recruiter_id'] != $recruiterId) {
            throw new \Exception('You do not have permission to delete this job offer', 403);
        }
        
        $deleted = $this->jobOfferModel->delete($id);
        
        if (!$deleted) {
            throw new \Exception('Failed to delete job offer');
        }
        
        return true;
    }
} 