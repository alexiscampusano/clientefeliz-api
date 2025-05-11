<?php
declare(strict_types=1);

/**
 * User Profile Management Service for API v1
 */
class ProfileService {
    private $userModel;
    private $workExperienceModel;
    private $academicBackgroundModel;

    /**
     * Constructor with dependency injection
     * 
     * @param \App\v1\models\UserModel $userModel User model
     * @param \App\v1\models\WorkExperienceModel $workExperienceModel Work experience model
     * @param \App\v1\models\AcademicBackgroundModel $academicBackgroundModel Academic background model
     */
    public function __construct(
        \App\v1\models\UserModel $userModel,
        \App\v1\models\WorkExperienceModel $workExperienceModel,
        \App\v1\models\AcademicBackgroundModel $academicBackgroundModel
    ) {
        $this->userModel = $userModel;
        $this->workExperienceModel = $workExperienceModel;
        $this->academicBackgroundModel = $academicBackgroundModel;
    }

    /**
     * Get work experience entries for a user
     * 
     * @param int $userId User ID
     * @return array List of work experience entries
     * @throws Exception If there's an error retrieving the data
     */
    public function getWorkExperience(int $userId): array {
        return $this->workExperienceModel->findByUser($userId);
    }

    /**
     * Add a new work experience entry
     * 
     * @param array $data Work experience data
     * @return array Added work experience entry
     * @throws Exception If there's an error adding the entry
     */
    public function addWorkExperience(array $data): array {
        $experienceId = $this->workExperienceModel->save($data);
        
        if (!$experienceId) {
            throw new \Exception('Failed to add work experience');
        }
        
        // Convert ID to integer if it's a string
        $id = is_string($experienceId) ? (int)$experienceId : $experienceId;
        
        // Get the complete work experience data
        $experience = $this->workExperienceModel->findById($id);
        
        return $experience;
    }

    /**
     * Get academic background entries for a user
     * 
     * @param int $userId User ID
     * @return array List of academic background entries
     * @throws Exception If there's an error retrieving the data
     */
    public function getAcademicBackground(int $userId): array {
        return $this->academicBackgroundModel->findByUser($userId);
    }

    /**
     * Add a new academic background entry
     * 
     * @param array $data Academic background data
     * @return array Added academic background entry
     * @throws Exception If there's an error adding the entry
     */
    public function addAcademicBackground(array $data): array {
        $academicId = $this->academicBackgroundModel->save($data);
        
        if (!$academicId) {
            throw new \Exception('Failed to add academic background');
        }
        
        // Convert ID to integer if it's a string
        $id = is_string($academicId) ? (int)$academicId : $academicId;
        
        // Get the complete academic background data
        $academic = $this->academicBackgroundModel->findById($id);
        
        return $academic;
    }

    /**
     * Update user profile information
     * 
     * @param int $userId User ID
     * @param array $data Profile data to update
     * @return array Updated user profile
     * @throws Exception If there's an error updating the profile
     */
    public function updateProfile(int $userId, array $data): array {
        $updated = $this->userModel->update($userId, $data);
        
        if (!$updated) {
            throw new \Exception('Failed to update profile');
        }
        
        $user = $this->userModel->findById($userId);
        
        if (!$user) {
            throw new \Exception('Failed to retrieve updated profile');
        }
        
        return $user;
    }

    /**
     * Update work experience entry
     * 
     * @param int $id Work experience ID
     * @param array $data Updated data
     * @param int $userId User ID (for security check)
     * @return array Updated work experience
     * @throws Exception If there's an error or permission issue
     */
    public function updateWorkExperience(int $id, array $data, int $userId): array {
        // Check if the experience exists and belongs to the user
        $experience = $this->workExperienceModel->findById($id);
        
        if (!$experience) {
            throw new \Exception('Work experience not found', 404);
        }
        
        if ($experience['candidate_id'] != $userId) {
            throw new \Exception('You are not authorized to update this work experience', 403);
        }
        
        // Update the record
        $updated = $this->workExperienceModel->update($id, $data);
        
        if (!$updated) {
            throw new \Exception('Failed to update work experience', 500);
        }
        
        // Return the updated record
        $updatedExperience = $this->workExperienceModel->findById($id);
        
        if (!$updatedExperience) {
            throw new \Exception('Failed to retrieve updated work experience', 500);
        }
        
        return $updatedExperience;
    }

    /**
     * Delete work experience entry
     * 
     * @param int $id Work experience ID
     * @param int $userId User ID (for security check)
     * @return bool Success status
     * @throws Exception If there's an error or permission issue
     */
    public function deleteWorkExperience(int $id, int $userId): bool {
        // Check if the experience exists and belongs to the user
        $experience = $this->workExperienceModel->findById($id);
        
        if (!$experience) {
            throw new \Exception('Work experience not found', 404);
        }
        
        if ($experience['candidate_id'] != $userId) {
            throw new \Exception('You are not authorized to delete this work experience', 403);
        }
        
        // Delete the record
        $deleted = $this->workExperienceModel->delete($id);
        
        if (!$deleted) {
            throw new \Exception('Failed to delete work experience', 500);
        }
        
        return true;
    }

    /**
     * Update academic background entry
     * 
     * @param int $id Academic background ID
     * @param array $data Updated data
     * @param int $userId User ID (for security check)
     * @return array Updated academic background
     * @throws Exception If there's an error or permission issue
     */
    public function updateAcademicBackground(int $id, array $data, int $userId): array {
        // Check if the academic background exists and belongs to the user
        $academic = $this->academicBackgroundModel->findById($id);
        
        if (!$academic) {
            throw new \Exception('Academic background not found', 404);
        }
        
        if ($academic['candidate_id'] != $userId) {
            throw new \Exception('You are not authorized to update this academic background', 403);
        }
        
        // Update the record
        $updated = $this->academicBackgroundModel->update($id, $data);
        
        if (!$updated) {
            throw new \Exception('Failed to update academic background', 500);
        }
        
        // Return the updated record
        $updatedAcademic = $this->academicBackgroundModel->findById($id);
        
        if (!$updatedAcademic) {
            throw new \Exception('Failed to retrieve updated academic background', 500);
        }
        
        return $updatedAcademic;
    }

    /**
     * Delete academic background entry
     * 
     * @param int $id Academic background ID
     * @param int $userId User ID (for security check)
     * @return bool Success status
     * @throws Exception If there's an error or permission issue
     */
    public function deleteAcademicBackground(int $id, int $userId): bool {
        // Check if the academic background exists and belongs to the user
        $academic = $this->academicBackgroundModel->findById($id);
        
        if (!$academic) {
            throw new \Exception('Academic background not found', 404);
        }
        
        if ($academic['candidate_id'] != $userId) {
            throw new \Exception('You are not authorized to delete this academic background', 403);
        }
        
        // Delete the record
        $deleted = $this->academicBackgroundModel->delete($id);
        
        if (!$deleted) {
            throw new \Exception('Failed to delete academic background', 500);
        }
        
        return true;
    }
} 