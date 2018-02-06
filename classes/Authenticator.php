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
     * Returns true on success, false on failure. Exceptions may be thrown on occasion.
     *
     * @param string $email
     * @param string $password
     * @return bool
     * @throws Exception|LogicException
     */
    public static function authenticate(string $email, string $password): bool
    {
        if (Controller::isUserLoggedIn()) {
            return false;
        } else {
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
                    Controller::setLoggedInUser($user);
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
     * Returns true on success, false on failure.
     *
     * @param string $firstName
     * @param string $lastName
     * @param string $email
     * @param string $altEmail
     * @param string $streetAddress
     * @param string $city
     * @param string $province
     * @param int $postalCode
     * @param int $phone
     * @param string $gradSemester
     * @param int $gradYear
     * @param string $password
     * @param bool $active
     * @return bool
     */
    public static function register(string $firstName, string $lastName, string $email, string $altEmail, string $streetAddress, string $city, string $province, int $postalCode, int $phone, string $gradSemester, int $gradYear, string $password, bool $active): bool
    {
        if (Controller::isUserLoggedIn()) {
            return false;
        } else {
            $user = new User($firstName, $lastName, $email, $altEmail, $streetAddress, $city, $province, $postalCode, $phone, $gradSemester, $gradYear, $password, $active);
            $user->addPermission(new Permission(Permission::PERMISSION_AUTHOR));
            if (self::userExists($user)) {
                return false;
            } else {
                $success = $user->updateToDatabase();
                if ($success) {
                    Controller::setLoggedInUser($user);
                    return true;
                } else{
                    return false;
                }
            }
        }
    }

    /**
     * Checks whether the given user is registered in the system.
     * Returns true if the user is registered, false otherwise.
     *
     * @param User $user
     * @return bool
     */
    public static function userExists(User $user): bool
    {
        $loadedUser = User::load($user->getEmail());
        return isset($loadedUser);
    }
}
