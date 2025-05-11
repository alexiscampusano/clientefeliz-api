<?php
declare(strict_types=1);

/**
 * Token blacklist handler
 * Manages storage and verification of invalidated tokens
 */
class TokenBlacklist {
    /**
     * File where invalidated tokens are stored
     * @var string
     */
    private static $blacklistFile = __DIR__ . '/../data/token_blacklist.json';
    
    /**
     * Adds a token to the blacklist
     * 
     * @param string $token Token to invalidate
     * @param int $expiration Token expiration timestamp
     * @return bool True if added successfully, false otherwise
     */
    public static function addToken(string $token, int $expiration): bool {
        // Make sure the directory exists
        $directory = dirname(self::$blacklistFile);
        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }
        
        // Load current list
        $blacklist = self::getBlacklist();
        
        // Clean expired tokens
        self::cleanExpiredTokens($blacklist);
        
        // Add the new token
        $tokenHash = hash('sha256', $token); // Store only the hash for security
        $blacklist[$tokenHash] = $expiration;
        
        // Save the updated list
        return file_put_contents(self::$blacklistFile, json_encode($blacklist)) !== false;
    }
    
    /**
     * Checks if a token is in the blacklist
     * 
     * @param string $token Token to verify
     * @return bool True if the token is in the blacklist, false otherwise
     */
    public static function isBlacklisted(string $token): bool {
        // Load list
        $blacklist = self::getBlacklist();
        
        // Clean expired tokens
        self::cleanExpiredTokens($blacklist);
        
        // Check if the token is in the list
        $tokenHash = hash('sha256', $token);
        return isset($blacklist[$tokenHash]);
    }
    
    /**
     * Gets the token blacklist
     * 
     * @return array List of invalidated tokens
     */
    private static function getBlacklist(): array {
        if (file_exists(self::$blacklistFile)) {
            $content = file_get_contents(self::$blacklistFile);
            return json_decode($content, true) ?: [];
        }
        
        return [];
    }
    
    /**
     * Cleans expired tokens from the blacklist
     * 
     * @param array &$blacklist Blacklist to clean
     */
    private static function cleanExpiredTokens(array &$blacklist): void {
        $now = time();
        foreach ($blacklist as $token => $expiration) {
            if ($expiration < $now) {
                unset($blacklist[$token]);
            }
        }
        
        // Save the clean list
        file_put_contents(self::$blacklistFile, json_encode($blacklist));
    }
} 