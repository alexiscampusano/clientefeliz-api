<?php
declare(strict_types=1);

// Load environment variables utility
require_once __DIR__ . '/utils/EnvLoader.php';

// Load environment variables from .env
$envFile = dirname(__DIR__) . '/.env';
if (file_exists($envFile)) {
    EnvLoader::load($envFile);
} else {
    // Try with .env.example if .env doesn't exist
    $envExample = dirname(__DIR__) . '/.env.example';
    if (file_exists($envExample)) {
        EnvLoader::load($envExample);
    }
}

// Load required classes
require_once __DIR__ . '/config/Database.php';
require_once __DIR__ . '/utils/ResponseHandler.php';
require_once __DIR__ . '/utils/Validator.php';
require_once __DIR__ . '/utils/JwtHandler.php';
require_once __DIR__ . '/utils/TokenBlacklist.php';

// Load models
require_once __DIR__ . '/v1/models/BaseModel.php';
require_once __DIR__ . '/v1/models/UserModel.php';
require_once __DIR__ . '/v1/models/JobOfferModel.php';
require_once __DIR__ . '/v1/models/ApplicationModel.php';
require_once __DIR__ . '/v1/models/AcademicBackgroundModel.php';
require_once __DIR__ . '/v1/models/WorkExperienceModel.php';

// Load common controllers
require_once __DIR__ . '/controllers/InfoController.php';

// Load v1 controllers and services
require_once __DIR__ . '/v1/controllers/AuthController.php';
require_once __DIR__ . '/v1/controllers/JobOfferController.php';
require_once __DIR__ . '/v1/controllers/ApplicationController.php';
require_once __DIR__ . '/v1/controllers/ProfileController.php';
require_once __DIR__ . '/v1/services/JobOfferService.php';
require_once __DIR__ . '/v1/services/ApplicationService.php';
require_once __DIR__ . '/v1/services/ProfileService.php';
require_once __DIR__ . '/v1/services/AuthService.php';

// Load routes
$routes = require_once __DIR__ . '/routes.php';

// Configure CORS headers
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
header('Content-Type: application/json; charset=UTF-8');

// If OPTIONS request (preflight), terminate execution
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// Function to get route parameters like {id}
function getRouteParams($routePath, $requestUri) {
    $routeParts = explode('/', trim($routePath, '/'));
    $requestParts = explode('/', trim(parse_url($requestUri, PHP_URL_PATH), '/'));
    
    // Check if parts have the same length, excluding parameters
    if (count($routeParts) !== count($requestParts)) {
        return null;
    }
    
    $params = [];
    for ($i = 0; $i < count($routeParts); $i++) {
        if (preg_match('/\{([a-zA-Z0-9_]+)\}/', $routeParts[$i], $matches)) {
            // This is a parameter like {id}, extract the value
            $params[$matches[1]] = $requestParts[$i];
        } elseif ($routeParts[$i] !== $requestParts[$i]) {
            // Not a parameter and doesn't match, invalid route
            return null;
        }
    }
    
    return $params;
}

// Function to find the appropriate route for the current request
function findRoute($routes, $method, $uri) {
    $path = parse_url($uri, PHP_URL_PATH);
    
    // First try to find exact matches (without parameters)
    foreach ($routes as $route) {
        if ($route['method'] !== $method) {
            continue;
        }
        
        if ($path === $route['path']) {
            return ['route' => $route, 'params' => []];
        }
    }
    
    // Then try to find routes with parameters
    foreach ($routes as $route) {
        if ($route['method'] !== $method) {
            continue;
        }
        
        $routePath = $route['path'];
        
        // Check if the route contains parameters like {id}
        if (strpos($routePath, '{') !== false) {
            $params = getRouteParams($routePath, $path);
            if ($params !== null) {
                return ['route' => $route, 'params' => $params];
            }
        }
    }
    
    return null;
}

// Handle the request
function handleRequest($routes) {
    $method = $_SERVER['REQUEST_METHOD'];
    $uri = $_SERVER['REQUEST_URI'];
    
    // Find the corresponding route
    $result = findRoute($routes, $method, $uri);
    
    // If route not found, return 404 error
    if ($result === null) {
        ResponseHandler::sendError('Route not found', 404);
        return;
    }
    
    $route = $result['route'];
    $params = $result['params'];
    
    // Get HTTP headers
    $headers = getallheaders();
    
    // Verify route protection
    if ($route['protected'] === true) {
        $token = isset($headers['Authorization']) ? $headers['Authorization'] : '';
        
        if (empty($token) || !JwtHandler::validateToken($token)) {
            ResponseHandler::sendError('Unauthorized', 401);
            return;
        }
    }
    
    // Get JSON data if POST, PUT or PATCH
    $data = [];
    if ($method === 'POST' || $method === 'PUT' || $method === 'PATCH') {
        $input = file_get_contents('php://input');
        if (!empty($input)) {
            $data = json_decode($input, true) ?? [];
        }
    } elseif ($method === 'GET' && !empty($_GET)) {
        $data = $_GET;
    }
    
    // Execute the controller
    $handler = $route['handler'];
    
    if (is_array($handler) && count($handler) === 2) {
        $controller = $handler[0];
        $action = $handler[1];
        
        if (method_exists($controller, $action)) {
            // Pass the parameters to the controller method in the correct order
            $controller->$action($data, $params, $headers);
        } else {
            ResponseHandler::sendError('Method not found in controller', 500);
        }
    } else {
        ResponseHandler::sendError('Invalid controller configuration', 500);
    }
}

// Execute the request handler
handleRequest($routes); 