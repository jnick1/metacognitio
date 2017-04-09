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
     * @param string $streetAddress
     * @param string $city
     * @param string $province
     * @param int $zip
     * @param int $phone
     * @param string $gradSemester
     * @param int $gradYear
     * @param string $password
     * @param bool $isActive
     * @return bool
     * @throws Exception
     */
    public static function register(string $fName, string $lName, string $email, string $altEmail, string $streetAddress, string $city, string $province, int $zip, int $phone, string $gradSemester, int $gradYear, string $password, bool $isActive)
    {
        if (isset($_SESSION["user"])) {
            throw new Exception("Authenticator::register($fName, $lName, $email, $altEmail, $streetAddress, $city, $province, $zip, $phone, $gradSemester, $gradYear, $password, $isActive) - Cannot create account when already signed in");
        } else {
            try {
                $user = new User($fName, $lName, $email, $altEmail, $streetAddress, $city, $province, $zip, $phone, $gradSemester, $gradYear, $password, $isActive);
                $user->addPermission(new Permission(Permission::PERMISSION_AUTHOR));
            } catch (Exception $exception) {
                return $exception->getMessage();
            }
            if (!self::userExists($user)) {
                $success = $user->updateToDatabase();
                if ($success) {
                    $_SESSION["user"] = $user;
                    return true;
                }
            }
            return false;
        }
    }

    /**
     * @param User $user
     * @return bool
     * @throws TypeError
     */
    public static function userExists(User $user)
    {
        if ($user instanceof User) {
            $loadedUser = User::load($user->getEmail());
            return isset($loadedUser);
        } else {
            throw new TypeError("expected User: got " . (gettype($user) == "object" ? get_class($user) : gettype($user)));
        }
    }
}
