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
     * @throws Exception
     */
    public static function authenticate(string $email, string $password): bool
    {
        if(Controller::isUserLoggedIn()) {
            throw new LogicException("Authenticator::authenticate($email, $password) - Unable to log in multiple times");
        }
        if(isset($_SESSION["loginLockout"])) {
            if(time()-$_SESSION["loginLockout"]>=60) {
                unset($_SESSION["loginLockout"]);
                unset($_SESSION["loginFails"]);
            } else {
                throw new Exception("Authenticator::authenticate($email,$password) - Login failed too many times; wait 60 seconds to try again");
            }
        }
        $user = User::load($email);
        if (isset($user)) {
            $goodPass = Hasher::verifySaltedHash($password, $user->getSalt(), $user->getHash());

            if ($goodPass) {
                try{
                    Controller::setLoggedInUser($user);
                } catch (Exception $exception) {
                    return $exception->getMessage();
                }
                unset($_SESSION["loginFails"]);
                unset($_SESSION["loginLockout"]);
                return true;
            } else {
                if(isset($_SESSION["loginFails"])) {
                    if($_SESSION["loginFails"]>=3) {
                        $_SESSION["loginLockout"] = time();
                    } else {
                        $_SESSION["loginFails"]++;
                    }
                } else {
                    $_SESSION["loginFails"] = 1;
                }

                throw new Exception("Authenticator:authenticate($email,$password) - User credentials incorrect; bad password");
            }
        } else {
            throw new Exception("Authenticator::authenticate($email,$password) - User credentials incorrect; unable to find email address");
        }
    }

    /**
     * @return bool
     */
    public static function logout(): bool
    {
        if(Controller::isUserLoggedIn()) {
            Controller::setLoggedInUser();
            return true;
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
    public static function register(string $fName, string $lName, string $email, string $altEmail, string $streetAddress, string $city, string $province, int $zip, int $phone, string $gradSemester, int $gradYear, string $password, bool $isActive): bool
    {
        if (Controller::isUserLoggedIn()) {
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
                    Controller::setLoggedInUser($user);
                    return true;
                }
            }
            throw new Exception("Authenticator::register($fName, $lName, $email, $altEmail, $streetAddress, $city, $province, $zip, $phone, $gradSemester, $gradYear, $password, $isActive) - User already exists in database; unable to re-register");
        }
    }

    /**
     * @param User $user
     * @return bool
     * @throws TypeError
     */
    public static function userExists(User $user): bool
    {
        if ($user instanceof User) {
            $loadedUser = User::load($user->getEmail());
            return isset($loadedUser);
        } else {
            throw new InvalidArgumentException("Authenticator::userExists($user) - expected User: got " . (gettype($user) == "object" ? get_class($user) : gettype($user)));
        }
    }
}
