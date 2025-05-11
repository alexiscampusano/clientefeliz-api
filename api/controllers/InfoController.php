<?php
declare(strict_types=1);

namespace App\controllers;

use ResponseHandler;

/**
 * Controller for basic API information routes
 */
class InfoController {
    /**
     * Home page that automatically redirects to /api
     */
    public function home() {
        // Redirect to /api
        header('Location: /api', true, 302);
        exit;
    }
    
    /**
     * Default route with API information
     */
    public function index() {
        // Prepare detailed information about the API
        $apiInfo = [
            'api' => 'Cliente Feliz API',
            'version' => 'v1',
            'description' => 'RESTful API for the Cliente Feliz personnel selection system',
            'status' => 'active',
            'repository' => 'https://github.com/alexiscampusano/clientefeliz-api',
            'endpoints' => [
                'auth' => [
                    'login' => '/api/v1/auth/login',
                    'register' => '/api/v1/auth/register',
                    'user' => '/api/v1/auth/user'
                ],
                'job_offers' => [
                    'list' => '/api/v1/job-offers',
                    'create' => '/api/v1/job-offers',
                    'get' => '/api/v1/job-offers/{id}',
                    'update' => '/api/v1/job-offers/{id}',
                    'deactivate' => '/api/v1/job-offers/{id}/deactivate'
                ],
                'applications' => [
                    'create' => '/api/v1/applications',
                    'list' => '/api/v1/applications',
                    'get' => '/api/v1/applications/{id}',
                    'update_status' => '/api/v1/applications/{id}/status',
                    'by_user' => '/api/v1/applications/user/{userId}',
                    'by_job_offer' => '/api/v1/applications/job-offer/{jobOfferId}'
                ]
            ]
        ];
        
        ResponseHandler::sendJson($apiInfo, 200, 'API information retrieved successfully');
    }
} 