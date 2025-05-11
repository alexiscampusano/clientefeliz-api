<?php
declare(strict_types=1);

use App\v1\controllers\AuthController;
use App\v1\controllers\JobOfferController;
use App\v1\controllers\ApplicationController;
use App\v1\controllers\ProfileController;
use App\v1\models\ApplicationModel;
use App\v1\models\JobOfferModel;
use App\v1\models\UserModel;

// Instanciar los modelos necesarios para el ApplicationController
$applicationModel = new ApplicationModel();
$jobOfferModel = new JobOfferModel();
$userModel = new UserModel();

// Crear una instancia del ApplicationController con las dependencias
$applicationController = new ApplicationController($applicationModel, $jobOfferModel, $userModel);

return [
    // Authentication routes
    [
        'method' => 'POST',
        'path' => '/api/v1/auth/login',
        'handler' => [new AuthController(), 'login'],
        'protected' => false
    ],
    [
        'method' => 'POST',
        'path' => '/api/v1/auth/register',
        'handler' => [new AuthController(), 'register'],
        'protected' => false
    ],
    [
        'method' => 'GET',
        'path' => '/api/v1/auth/user',
        'handler' => [new AuthController(), 'getCurrentUser'],
        'protected' => true
    ],
    [
        'method' => 'POST',
        'path' => '/api/v1/auth/logout',
        'handler' => [new AuthController(), 'logout'],
        'protected' => true
    ],

    // Job offers routes (public)
    [
        'method' => 'GET',
        'path' => '/api/v1/job-offers',
        'handler' => [new JobOfferController(), 'getActiveOffers'],
        'protected' => false
    ],
    [
        'method' => 'GET',
        'path' => '/api/v1/job-offers/{id}',
        'handler' => [new JobOfferController(), 'getOfferById'],
        'protected' => false
    ],

    // Job offers routes (recruiters only)
    [
        'method' => 'POST',
        'path' => '/api/v1/job-offers',
        'handler' => [new JobOfferController(), 'createOffer'],
        'protected' => true
    ],
    [
        'method' => 'PUT',
        'path' => '/api/v1/job-offers/{id}',
        'handler' => [new JobOfferController(), 'updateOffer'],
        'protected' => true
    ],
    [
        'method' => 'PATCH',
        'path' => '/api/v1/job-offers/{id}/deactivate',
        'handler' => [new JobOfferController(), 'deactivateOffer'],
        'protected' => true
    ],
    [
        'method' => 'DELETE',
        'path' => '/api/v1/job-offers/{id}',
        'handler' => [new JobOfferController(), 'deleteOffer'],
        'protected' => true
    ],
    [
        'method' => 'GET',
        'path' => '/api/v1/job-offers/my-offers',
        'handler' => [new JobOfferController(), 'getMyOffers'],
        'protected' => true
    ],
    [
        'method' => 'GET',
        'path' => '/api/v1/job-offers/{id}/applicants',
        'handler' => [new JobOfferController(), 'getApplicants'],
        'protected' => true
    ],

    // Applications routes
    [
        'method' => 'POST',
        'path' => '/api/v1/applications',
        'handler' => [$applicationController, 'apply'],
        'protected' => true
    ],
    [
        'method' => 'PUT',
        'path' => '/api/v1/applications/{id}/status',
        'handler' => [$applicationController, 'updateApplicationStatus'],
        'protected' => true
    ],
    [
        'method' => 'GET',
        'path' => '/api/v1/applications/my-applications',
        'handler' => [$applicationController, 'getMyApplications'],
        'protected' => true
    ],
    [
        'method' => 'GET',
        'path' => '/api/v1/applications/{id}',
        'handler' => [$applicationController, 'getApplicationDetails'],
        'protected' => true
    ],

    // Profile routes - Work Experience
    [
        'method' => 'GET',
        'path' => '/api/v1/profile/work-experience',
        'handler' => [new ProfileController(), 'getWorkExperience'],
        'protected' => true
    ],
    [
        'method' => 'POST',
        'path' => '/api/v1/profile/work-experience',
        'handler' => [new ProfileController(), 'addWorkExperience'],
        'protected' => true
    ],
    [
        'method' => 'PUT',
        'path' => '/api/v1/profile/work-experience/{id}',
        'handler' => [new ProfileController(), 'updateWorkExperience'],
        'protected' => true
    ],
    [
        'method' => 'DELETE',
        'path' => '/api/v1/profile/work-experience/{id}',
        'handler' => [new ProfileController(), 'deleteWorkExperience'],
        'protected' => true
    ],

    // Profile routes - Academic Background
    [
        'method' => 'GET',
        'path' => '/api/v1/profile/academic-background',
        'handler' => [new ProfileController(), 'getAcademicBackground'],
        'protected' => true
    ],
    [
        'method' => 'POST',
        'path' => '/api/v1/profile/academic-background',
        'handler' => [new ProfileController(), 'addAcademicBackground'],
        'protected' => true
    ],
    [
        'method' => 'PUT',
        'path' => '/api/v1/profile/academic-background/{id}',
        'handler' => [new ProfileController(), 'updateAcademicBackground'],
        'protected' => true
    ],
    [
        'method' => 'DELETE',
        'path' => '/api/v1/profile/academic-background/{id}',
        'handler' => [new ProfileController(), 'deleteAcademicBackground'],
        'protected' => true
    ]
]; 