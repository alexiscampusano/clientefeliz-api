<?php
declare(strict_types=1);

class ResponseHandler {
    /**
     * Sends a JSON response with data
     *
     * @param mixed $data Data to send
     * @param int $statusCode HTTP status code
     * @param string|null $message Optional descriptive message
     * @return void
     */
    public static function sendJson($data, int $statusCode = 200): void {
        header('Content-Type: application/json; charset=utf-8');
        http_response_code($statusCode);
        echo json_encode([
            'success' => true,
            'data' => $data,
            'message' => $message ?? 'Request completed successfully'
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    /**
     * Sends a JSON success response with message
     *
     * @param string $message Success message
     * @param int $statusCode HTTP status code
     * @return void
     */
    public static function sendSuccess(string $message, int $statusCode = 200): void {
        header('Content-Type: application/json; charset=utf-8');
        http_response_code($statusCode);
        echo json_encode([
            'success' => true,
            'data' => null,
            'message' => $message
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    /**
     * Sends a JSON error response
     *
     * @param string $message Error message
     * @param int $statusCode HTTP status code
     * @return void
     */
    public static function sendError(string $message, int $statusCode = 400): void {
        header('Content-Type: application/json; charset=utf-8');
        http_response_code($statusCode);
        echo json_encode([
            'success' => false,
            'data' => null,
            'message' => $message,
            'error' => true
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    /**
     * Sends a JSON validation error response
     *
     * @param array $errors List of validation errors
     * @param int $statusCode HTTP status code
     * @return void
     */
    public static function sendValidationError(array $errors, int $statusCode = 422): void {
        header('Content-Type: application/json; charset=utf-8');
        http_response_code($statusCode);
        echo json_encode([
            'success' => false,
            'data' => null,
            'message' => 'Validation errors',
            'errors' => $errors,
            'error' => true
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }
}