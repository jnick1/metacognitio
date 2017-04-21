<?php

/*
 * A class with the purpose of hashing a given input. Implementation details
 * consist of calls to a hashing library.
 */

class Hasher
{

    /**
     * Generates a new salt and salted hash for a given string.
     *
     * @param string $string
     * @return array
     */
    public static function cryptographicHash(string $string): array
    {
        $salt = self::randomSalt();
        $salt1 = substr($salt, 0, strlen($salt) / 2);
        $salt2 = substr($salt, strlen($salt) / 2, strlen($salt));
        $hash = hash("sha256", $salt1 . $string . $salt2);
        return array("salt" => $salt, "hash" => $hash);
    }

    /**
     * Generates a new random hash based on SHA-256.
     *
     * @return string
     */
    public static function randomHash(): string
    {
        return hash("sha256", self::randomSalt());
    }

    /**
     * Generates a new random salt based on a cryptographically securely generated random string of bytes.
     *
     * @return string
     */
    public static function randomSalt(): string
    {
        return random_bytes(16);
    }

    /**
     * Verifies that $string hashed is equivalent to $hash via the SHA256 hashing algorithm.
     *
     * @param string $string
     * @param string $hash
     * @return bool
     */
    public static function verifyHash(string $string, string $hash)
    {
        return hash("sha256", $string) == $hash;
    }

    /**
     * Verifies that a salted $string is equivalent to $hash via the SHA256 hashing algorithm.
     *
     * @param string $string
     * @param string $salt
     * @param string $hash
     * @return bool
     */
    public static function verifySaltedHash(string $string, string $salt, string $hash)
    {
        $salt1 = substr($salt, 0, strlen($salt) / 2);
        $salt2 = substr($salt, strlen($salt) / 2, strlen($salt));
        return hash("sha256", $salt1 . $string . $salt2) == $hash;
    }
}
