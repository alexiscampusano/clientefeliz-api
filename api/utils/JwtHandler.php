<?php
declare(strict_types=1);

/**
 * JWT Handler (JSON Web Tokens)
 * Manages generation and validation of authentication tokens
 */
class JwtHandler {
    /**
     * Secret key to sign tokens
     * @var string
     */
    private static $secretKey = 'QJ4AnExYM9hF3LX6Rk8dBvTgKwPzC2Zs';
    
    /**
     * Token expiration time in seconds (1 hour by default)
     * @var int
     */
    private static $expirationTime = 3600;
    
    /**
     * Generates a new JWT token
     * 
     * @param array $userData User data to encode in the token
     * @return string Generated JWT token
     */
    public static function generateToken(array $userData): string {
        // Create header
        $header = [
            'typ' => 'JWT',
            'alg' => 'HS256'
        ];
        
        // Create payload
        $payload = [
            'iat' => time(),
            'exp' => time() + self::$expirationTime,
            'data' => $userData
        ];
        
        // Encode header and payload
        $headerEncoded = self::base64UrlEncode(json_encode($header));
        $payloadEncoded = self::base64UrlEncode(json_encode($payload));
        
        // Generate signature
        $signature = self::generateSignature($headerEncoded, $payloadEncoded);
        
        // Build token
        return $headerEncoded . '.' . $payloadEncoded . '.' . $signature;
    }
    
    /**
     * Validates a JWT token and returns user data if valid
     * 
     * @param string $token JWT token to validate
     * @return array|false User data if token is valid, false otherwise
     */
    public static function validateToken(string $token) {
        // Clean token from possible prefixes
        $token = str_replace('Bearer ', '', $token);
        
        // Check if the token is blacklisted
        if (TokenBlacklist::isBlacklisted($token)) {
            return false;
        }
        
        // Split token parts
        $tokenParts = explode('.', $token);
        
        // Verify token has 3 parts
        if (count($tokenParts) !== 3) {
            return false;
        }
        
        // Extract token parts
        list($headerEncoded, $payloadEncoded, $signatureReceived) = $tokenParts;
        
        // Verify signature
        $signatureCalculated = self::generateSignature($headerEncoded, $payloadEncoded);
        
        if ($signatureCalculated !== $signatureReceived) {
            return false;
        }
        
        // Decode payload
        $payload = json_decode(self::base64UrlDecode($payloadEncoded), true);
        
        // Verify expiration
        if (!isset($payload['exp']) || $payload['exp'] < time()) {
            return false;
        }
        
        // Return user data
        return $payload;
    }
    
    /**
     * Extracts expiration from a token
     * 
     * @param string $token JWT token
     * @return int|false Expiration timestamp or false if it cannot be extracted
     */
    public static function getTokenExpiration(string $token): ?int {
        // Clean token from possible prefixes
        $token = str_replace('Bearer ', '', $token);
        
        // Split token parts
        $tokenParts = explode('.', $token);
        
        // Verify token has 3 parts
        if (count($tokenParts) !== 3) {
            return null;
        }
        
        // Get payload
        $payloadEncoded = $tokenParts[1];
        $payload = json_decode(self::base64UrlDecode($payloadEncoded), true);
        
        // Return expiration
        return isset($payload['exp']) ? (int)$payload['exp'] : null;
    }
    
    /**
     * Generates a signature for a JWT
     * 
     * @param string $headerEncoded Encoded header
     * @param string $payloadEncoded Encoded payload
     * @return string Generated signature
     */
    private static function generateSignature(string $headerEncoded, string $payloadEncoded): string {
        // Generate signature
        $signature = hash_hmac('sha256', $headerEncoded . '.' . $payloadEncoded, self::$secretKey, true);
        
        // Encode signature
        return self::base64UrlEncode($signature);
    }
    
    /**
     * Encodes a string in base64url (JWT compatible)
     * 
     * @param string $data Data to encode
     * @return string Encoded data
     */
    private static function base64UrlEncode(string $data): string {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
    
    /**
     * Decodes a base64url string (JWT compatible)
     * 
     * @param string $data Data to decode
     * @return string Decoded data
     */
    private static function base64UrlDecode(string $data): string {
        return base64_decode(strtr($data, '-_', '+/') . str_repeat('=', 3 - (3 + strlen($data)) % 4));
    }
}
