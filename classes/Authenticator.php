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
     * Verifies whether the supplied credentials represent a valid user in the system.
     * Returns true on success, throws an exception on failure.
     *
     * @param string $email
     * @param string $password
     * @return bool
     * @throws Exception|LogicException
     */
    public static function authenticate(string $email, string $password): bool
    {
        if (Controller::isUserLoggedIn()) {
            throw new LogicException("Authenticator::authenticate($email, $password) - Unable to log in multiple times");
        }
        /*
         * Determines whether the client connected to the current session should be locked out
         * of logging in to the system due to too many repeated log in attempts. This helps to
         * prevent dictionary attacks against the system by forcing a 60 second wait period after
         * 3 failed attempts to log in by the client.
         */
        if (Controller::getLoginLockout() !== false) {
            if (time() - Controller::getLoginLockout() >= 60) {
                Controller::setLoginFails();
                Controller::setLoginLockout();
            } else {
                throw new Exception("Authenticator::authenticate($email,$password) - Login failed too many times; wait 60 seconds to try again");
            }
        }
        $user = User::load($email);
        if (isset($user)) {
            $goodPass = Hasher::verifySaltedHash($password, $user->getSalt(), $user->getHash());

            if ($goodPass) {
                try {
                    Controller::setLoggedInUser($user);
                } catch (Exception $exception) {
                    return $exception->getMessage();
                }
                Controller::setLoginLockout();
                Controller::setLoginFails();
                return true;
            } else {
                if (Controller::getLoginFails() !== false) {
                    if (Controller::getLoginFails() > 3) {
                        Controller::setLoginLockout(time());
                    } else {
                        Controller::setLoginFails(Controller::getLoginFails() + 1);
                    }
                } else {
                    Controller::setLoginFails(1);
                }
                throw new Exception("Authenticator:authenticate($email,$password) - User credentials incorrect; bad password");
            }
        } else {
            throw new Exception("Authenticator::authenticate($email,$password) - User credentials incorrect; unable to find email address");
        }
    }

    /**
     * Logs out the currently logged in user, if there is one.
     * Returns true on success, false on failure.
     *
     * @return bool
     */
    public static function logout(): bool
    {
        if (Controller::isUserLoggedIn()) {
            Controller::setLoggedInUser();
            return true;
        } else {
            return false;
        }
    }

    /**
     * Takes a new user's information and registers the user within the system,
     * saving their information to the database.
     * Returns true on success, throws an exception on failure.
     *
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
     * Checks whether the given user is registered in the system.
     * Returns true if the user is registered, false otherwise.
     *
     * @param User $user
     * @return bool
     * @throws TypeError
     */
    public static function userExists(User $user): bool
    {
        $loadedUser = User::load($user->getEmail());
        return isset($loadedUser);
    }
}
