<?php
/*
 * This class handles activities related to authentication and registration
 * of users. It will interact with the hasher and communicate with the user
 * credential-related portions of the database to allow users to log in and
 * accounts to be created.
 */
class Authenticator {

    public function authenticate(string $email, string $password) {
        $hashedpassword = ""; //TODO: SQL query for hashed password associated with $email

        $hasher = new Hasher();
        return $hasher->verify($password, $hashedpassword);
    }

    public function register(string $email, string $password) {
        $hasher = new Hasher();
        $hashedpassword = $hasher->hash($password);
        //TODO: SQL store email and hashed password
    }
}