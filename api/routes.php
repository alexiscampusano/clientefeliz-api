<?php
declare(strict_types=1);

use App\controllers\InfoController;
use App\v1\controllers\AuthController;
use App\v1\controllers\JobOfferController;
use App\v1\controllers\ApplicationController;

// Define common non-versioned routes
$commonRoutes = [
    // Root route
    [
        'method' => 'GET',
        'path' => '/',
        'handler' => [new InfoController(), 'home'],
        'protected' => false
    ],
    
    // Default API info route
    [
        'method' => 'GET',
        'path' => '/api',
        'handler' => [new InfoController(), 'index'],
        'protected' => false
    ]
];

// Load routes from v1 version
$v1Routes = [];
$v1RoutesFile = __DIR__ . '/v1/routes/routes.php';
if (file_exists($v1RoutesFile)) {
    $v1Routes = require $v1RoutesFile;
}

// Merge all routes
return array_merge($commonRoutes, $v1Routes);