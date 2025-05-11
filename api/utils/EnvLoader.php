<?php
declare(strict_types=1);

/**
 * Simple class to load environment variables from a .env file
 */
class EnvLoader {
    /**
     * Loads environment variables from a .env file
     * 
     * @param string $path Path to the .env file
     * @return bool True if loaded successfully, false otherwise
     */
    public static function load(string $path): bool {
        if (!file_exists($path)) {
            return false;
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        
        foreach ($lines as $line) {
            // Ignore comments
            if (strpos(trim($line), '#') === 0) {
                continue;
            }
            
            // Look for lines in KEY=VALUE format
            if (strpos($line, '=') !== false) {
                list($name, $value) = explode('=', $line, 2);
                $name = trim($name);
                $value = trim($value);
                
                // Remove quotes if they exist
                if (preg_match('/^["\'](.*)["\']\s*$/', $value, $matches)) {
                    $value = $matches[1];
                }
                
                // Set the environment variable
                if (!empty($name)) {
                    putenv("$name=$value");
                    $_ENV[$name] = $value;
                    $_SERVER[$name] = $value;
                }
            }
        }
        
        return true;
    }
} 