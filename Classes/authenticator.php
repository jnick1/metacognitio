<?php
/*
 * This class handles activities related to authentication and registration
 * of users. It will interact with the hasher and communicate with the user
 * credential-related portions of the database to allow users to log in and
 * accounts to be created.
 */
class Authenticator {

    /**
     * @param $email
     * @param $password
     * @return bool
     */
    public static function authenticate($email, $password)
    {
        $user = User::load($email);
        if(isset($user)) {
            return Hasher::verifySaltedHash($password,$user->getSalt(),$user->getHash());
        } else {
            return false;
        }
    }

    /**
     * @param $fName
     * @param $lName
     * @param $email
     * @param $altEmail
     * @param $addr
     * @param $city
     * @param $province
     * @param $zip
     * @param $phone
     * @param $gradSemester
     * @param $gradYear
     * @param $password
     * @param $isActive
     * @return bool
     */
    public static function register($fName, $lName, $email, $altEmail, $addr, $city, $province, $zip, $phone, $gradSemester, $gradYear, $password, $isActive)
    {
        $user = new User($fName, $lName, $email, $altEmail, $addr, $city, $province, $zip, $phone, $gradSemester, $gradYear, $password, $isActive);
        if(isset($user)) {
            return $user->updateDatabase();;
        }
        return false;
    }
}