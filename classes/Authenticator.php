<?php

/*
 * This class handles activities related to authentication and registration
 * of users. It will interact with the hasher and communicate with the user
 * credential-related portions of the database to allow users to log in and
 * accounts to be created.
 */

class Authenticator
{

    /**
     * @param string $email
     * @param string $password
     * @return bool
     */
    public static function authenticate(string $email, string $password)
    {
        $user = User::load($email);
        if (isset($user)) {
            return Hasher::verifySaltedHash($password, $user->getSalt(), $user->getHash());
        } else {
            return false;
        }
    }

    /**
     * @param string $fName
     * @param string $lName
     * @param string $email
     * @param string $altEmail
     * @param string $addr
     * @param string $city
     * @param string $province
     * @param int $zip
     * @param int $phone
     * @param string $gradSemester
     * @param int $gradYear
     * @param string $password
     * @param bool $isActive
     * @return bool
     */
    public static function register(string $fName, string $lName, string $email, string $altEmail, string $addr, string $city, string $province, int $zip, int $phone, string $gradSemester, int $gradYear, string $password, bool $isActive)
    {
        $user = new User($fName, $lName, $email, $altEmail, $addr, $city, $province, $zip, $phone, $gradSemester, $gradYear, $password, $isActive);
        if (isset($user)) {
            return $user->updateDatabase();;
        }
        return false;
    }
}
