<?php

namespace App\Services;

class CustomPasswordHasher
{
    /**
     * The secret key used for hashing.
     * 
     * Note: You should generate a unique key and store it securely, e.g., in your .env file.
     * This is just an example. Never hard-code sensitive information.
     */
    private static $secretKey = 'your-secret-key'; // Replace with your secret key or get it from the .env file

    /**
     * Hash the given password using HMAC with SHA-256.
     *
     * @param string $password
     * @return string
     */
    public static function hashPassword(string $password): string
    {
        // Generate a random salt
        $salt = bin2hex(random_bytes(16)); // 16 bytes = 128 bits

        // Combine the password and the salt, and hash them using HMAC with SHA-256
        $hash = hash_hmac('sha256', $password . $salt, self::$secretKey);

        // Return the salt and the hash together, separated by a colon
        return $salt . ':' . $hash;
    }

    /**
     * Verify the given plain password against a hashed password.
     *
     * @param string $plainPassword
     * @param string $hashedPassword
     * @return bool
     */
    public static function verifyPassword(string $plainPassword, string $hashedPassword): bool
    {
         
         list($salt, $originalHash) = explode(':', $hashedPassword);

        // Hash the plain password with the extracted salt and the same secret key
        $hash = hash_hmac('sha256', $plainPassword . $salt, self::$secretKey);

        // Compare the new hash with the original hash
        return hash_equals($originalHash, $hash);
    }
}
