<?php

/*
* A user class to keep track of everything of importance regarding a
* user's information.
*/

class User
{
    /**
     * Constants used during setProvince and setCountry to increase input identification speed and accuracy
     */
    const MODE_DBID = 1;
    const MODE_ISO = 2;
    const MODE_ISO_SHORT = 3;
    const MODE_NAME = 4;
    const MODE_PHONE = 5;

    /**
     * Stores a user's alternative email address for contact purposes only. A user cannot log in using their
     * alternate email address.
     *
     * @var string|null
     */
    private $altEmail;
    /**
     * Stores the city of the shipping address for a user.
     *
     * @var string
     */
    private $city;
    /**
     * Stores information about the country of the shipping address for a user. This includes an internal identifier,
     * the country's ISO code (ISO 3166-1), its name, and its international phone extension.
     *
     * @var array ["pkCountryID"=>int, "idISO"=>string,    "nmName"=>string,   "idPhoneCode"=>int]
     */
    private $country;
    /**
     * Stores the main email for a user. This is the also the username that users use to log in to their account.
     *
     * @var string
     */
    private $email;
    /**
     * Stores a user's first name.
     *
     * @var string
     */
    private $fName;
    /**
     * Stores the expected graduation semester of a user.
     *
     * @var string
     */
    private $gradSemester;
    /**
     * Stores the 4-digit expected graduation year of a user.
     *
     * @var int
     */
    private $gradYear;
    /**
     * Stores the salted hash of a user's password.
     *
     * @var string
     */
    private $hash;
    /**
     * Stores a boolean indicating whether or not a user is permitted to log in.
     *
     * @var bool
     */
    private $isActive;
    /**
     * Stores a boolean indicating whether or not a user's information is stored in the database (true), or whether
     * the current User object only exists within the current session.
     *
     * @var bool
     */
    private $isInDatabase;
    /**
     * Stores a user's last name.
     *
     * @var string
     */
    private $lName;
    /**
     * Stores an array of Permission objects indicating what permissions the a user has.
     *
     * @var array [Permission]
     */
    private $permissions;
    /**
     * Stores a user's phone number. This must be a valid phone number within the current User object's indicated
     * shipping address country.
     *
     * @var int
     */
    private $phone;
    /**
     * Stores the postal code for a user's shipping address. May be null if a user's shipping address' country does not
     * use postal codes, otherwise, it must have a value. Allows only arabic numerals 0-9 and ISO basic latin alphabet
     * characters.
     *
     * @var string|null
     */
    private $postalCode;
    /**
     * Stores information about the province of the shipping address for a user. This includes an internal identifier,
     * the province's ISO code (ISO 3166-2), and its name.
     *
     * @var array ["pkStateID"=>int,   "idISO"=>string,    "nmName"=>string]
     */
    private $province;
    /**
     * Stores the salt for a user's salted hash of their password.
     *
     * @var string
     */
    private $salt;
    /**
     * Stores the street address of the shipping address for a user.
     *
     * @var string
     */
    private $streetAddress;
    /**
     * Stores the internal identifier for a user. Can only be changed if the user is not saved to the database.
     *
     * @var int
     */
    private $userID;

    /**
     * User constructor.
     */
    public function __construct()
    {
        //This segment of code originally written by rayro@gmx.de
        //http://php.net/manual/en/language.oop5.decon.php
        $a = func_get_args();
        $i = func_num_args();
        if (method_exists($this, $f = '__construct' . $i)) {
            call_user_func_array(array($this, $f), $a);
        }
    }

    /**
     * Loads a user from the database.
     *
     * @param string|int $identifier May be either user email or ID
     * @param int $mode
     * @return null|User
     */
    public static function load($identifier, int $mode = self::MODE_NAME)
    {
        try {
            return new User($identifier, $mode);
        } catch (InvalidArgumentException $iae) {
            return null;
        }
    }

    /**
     * User constructor (13 arguments).
     *
     * @param string $fName
     * @param string $lName
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
     * @param bool $isActive
     * @throws Exception
     */
    public function __construct13(string $fName, string $lName, string $email, string $altEmail, string $streetAddress, string $city, string $province, int $postalCode, int $phone, string $gradSemester, int $gradYear, string $password, bool $isActive)
    {
        $dbc = new DatabaseConnection();
        $params = ["s", $province];
        $provinceResult = $dbc->query("select", "SELECT * FROM `province` WHERE `idISO`=?", $params);
        if ($provinceResult) {
            $params = ["i", $provinceResult["fkCountryID"]];
            $country = $dbc->query("select", "SELECT * FROM `country` WHERE `pkCountryID`=?", $params);

            if ($country) {
                $result = [
                    $this->setFName($fName),
                    $this->setLName($lName),
                    $this->setEmail($email),
                    $this->setAltEmail($altEmail),
                    $this->setStreetAddress($streetAddress),
                    $this->setCity($city),
                    $this->setProvince($provinceResult["idISO"], self::MODE_ISO),
                    $this->setCountry($country["idISO"]),
                    $this->setPostalCode($postalCode),
                    $this->setPhone($phone),
                    $this->setGradsemester($gradSemester),
                    $this->setGradYear($gradYear),
                    $this->updatePassword($password),
                    $this->setIsActive($isActive),
                ];
                if (in_array(false, $result, true)) {
                    throw new Exception("User->__construct13($fName, $lName, $email, $altEmail, $streetAddress, $city, $province, $postalCode, $phone, $gradSemester, $gradYear, $password, $isActive) - Unable to construct User object; variable assignment failure - (" . implode(" ", array_keys($result, false, true)) . ")");
                }
                $this->permissions = [];
                $this->isInDatabase = false;
            }
        } else {
            throw new InvalidArgumentException("User->__construct13($fName, $lName, $email, $altEmail, $streetAddress, $city, $province, $postalCode, $phone, $gradSemester, $gradYear, $password, $isActive) - Unable to construct User object; Invalid province");
        }
    }

    /**
     * User Constructor (2 arguments).
     *
     * @param $identifier
     * @param int $mode
     * @throws Exception
     */
    public function __construct2($identifier, int $mode = self::MODE_NAME)
    {
        $dbc = new DatabaseConnection();
        if ($mode === self::MODE_DBID) {
            $params = ["i", $identifier];
            $user = $dbc->query("select", "SELECT * FROM `user` WHERE `pkUserID`=?", $params);
        } else {
            $params = ["s", $identifier];
            $user = $dbc->query("select", "SELECT * FROM `user` WHERE `txEmail`=?", $params);
        }

        if ($user) {
            $params = ["i", $user["fkProvinceID"]];
            $province = $dbc->query("select", "SELECT * FROM `province` WHERE `pkStateID`=?", $params);
            if ($province) {
                $params = ["i", $province["fkCountryID"]];
                $country = $dbc->query("select", "SELECT * FROM `country` WHERE `pkCountryID`=?", $params);

                if ($country) {
                    $result = [
                        $this->setUserID($user["pkUserID"]),
                        $this->setFName($user["nmFirst"]),
                        $this->setLName($user["nmLast"]),
                        $this->setEmail($user["txEmail"]),
                        $this->setAltEmail($user["txEmailAlt"]),
                        $this->setStreetAddress($user["txStreetAddress"]),
                        $this->setCity($user["txCity"]),
                        $this->setProvince($province["idISO"], self::MODE_ISO),
                        $this->setPostalCode($user["txPostalCode"]),
                        $this->setPhone($user["nPhone"]),
                        $this->setGradsemester($user["enGradSemester"]),
                        $this->setGradYear($user["dtGradYear"]),
                        $this->setSalt($user["blSalt"]),
                        $this->setHash($user["txHash"]),
                        $this->setIsActive($user["isActive"]),
                    ];
                    if (in_array(false, $result, true)) {
                        throw new Exception("User->__construct2($identifier, $mode) - Unable to construct User object; variable assignment failure - (" . implode(" ", array_keys($result, false, true)) . ")");
                    }
                    $this->isInDatabase = true;
                    $this->removeAllPermissions();
                    $params = ["i", $user["pkUserID"]];
                    $permissions = $dbc->query("select multiple", "SELECT `fkPermissionID` FROM `userpermissions` WHERE `fkUserID` = ?", $params);
                    if ($permissions) {
                        foreach ($permissions as $permission) {
                            $this->addPermission(new Permission($permission["fkPermissionID"]));
                        }
                    }
                } else {
                    throw new Exception("User->__construct2($identifier, $mode) - Unable to select from database");
                }
            } else {
                throw new Exception("User->__construct2($identifier, $mode) - Unable to select from database");
            }
        } else {
            throw new InvalidArgumentException("User->__construct2($identifier, $mode) - User not found");
        }
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return "{" . implode(" ", [$this->getFName(), $this->getLName(), $this->getEmail(), $this->getUserID(), $this->isInDatabase(), $this->getIsActive()]) . "}";
    }

    /**
     * Adds a permission to the user's permissions.
     *
     * @param Permission $permission
     * @return bool|int
     * @throws InvalidArgumentException()
     */
    public function addPermission(Permission $permission)
    {
        if (in_array($permission, $this->getPermissions())) {
            return false;
        } else {
            return array_push($this->permissions, $permission);
        }
    }

    /**
     * @return string
     */
    public function getAltEmail(): string
    {
        if ($this->altEmail === null) {
            return "";
        } else {
            return $this->altEmail;
        }
    }

    /**
     * @return string
     */
    public function getCity(): string
    {
        return $this->city;
    }

    /**
     * @param int $mode
     * @return int|string
     */
    public function getCountry(int $mode=self::MODE_NAME)
    {
        switch ($mode) {
            case self::MODE_DBID:
                return $this->country["pkCountryID"];
            case self::MODE_ISO:
            case self::MODE_ISO_SHORT:
                return $this->country["idISO"];
            case self::MODE_PHONE:
                return $this->country["idPhoneCode"];
            default:
                return $this->country["nmName"];
        }
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getFName(): string
    {
        return $this->fName;
    }

    /**
     * @return string
     */
    public function getGradSemester(): string
    {
        return $this->gradSemester;
    }

    /**
     * @return int
     */
    public function getGradYear(): int
    {
        return $this->gradYear;
    }

    /**
     * @return string
     */
    public function getHash(): string
    {
        return $this->hash;
    }

    /**
     * @return bool
     */
    public function getIsActive(): bool
    {
        return $this->isActive;
    }

    /**
     * @return string
     */
    public function getLName(): string
    {
        return $this->lName;
    }

    /**
     * @return array
     */
    public function getPermissions(): array
    {
        return $this->permissions;
    }

    /**
     * @return int
     */
    public function getPhone(): int
    {
        return $this->phone;
    }

    /**
     * @return string|null
     */
    public function getPostalCode(): string
    {
        return $this->postalCode;
    }

    /**
     * @param int $identifier
     * @return string|int
     */
    public function getProvince(int $identifier = self::MODE_NAME)
    {
        $identifier = strtolower($identifier);
        switch ($identifier) {
            case self::MODE_ISO:
                return $this->province["idISO"];
            case self::MODE_ISO_SHORT:
                return str_replace($this->getCountry(self::MODE_ISO) . "-", "", $this->province["idISO"]);
            case self::MODE_DBID:
                return $this->province["pkStateID"];
            default:
                return $this->province["nmName"];
        }
    }

    /**
     * @return string
     */
    public function getSalt(): string
    {
        return $this->salt;
    }

    /**
     * @return string
     */
    public function getStreetAddress(): string
    {
        return $this->streetAddress;
    }

    /**
     * @return int
     */
    public function getUserID(): int
    {
        return $this->userID;
    }

    /**
     * @param Permission $permission
     * @return bool
     * @throws InvalidArgumentException()
     */
    public function hasPermission(Permission $permission): bool
    {
        if ($permission instanceof Permission) {
            return in_array($permission, $this->getPermissions());
        } else {
            throw new InvalidArgumentException("User->hasPermission(" . $permission . ") - expected Permission: got " . (gettype($permission) == "object" ? get_class($permission) : gettype($permission)));
        }
    }

    /**
     * @return bool
     */
    public function isInDatabase(): bool
    {
        if(isset($this->isInDatabase)) {
            return $this->isInDatabase;
        } else {
            return false;
        }
    }

    /**
     * @return bool
     */
    public function removeAllPermissions(): bool
    {
        $this->permissions = [];
        return true;
    }

    /**
     * @param Permission $permission
     * @return bool
     * @throws InvalidArgumentException()
     */
    public function removePermission(Permission $permission): bool
    {
        if ($permission instanceof Permission) {
            if (($key = array_search($permission, $this->getPermissions(), true)) !== false) {
                unset($this->permissions[$key]);
                return true;
            } else {
                return false;
            }
        } else {
            throw new InvalidArgumentException("User->removePermission(" . $permission . ") - expected Permission: got " . (gettype($permission) == "object" ? get_class($permission) : gettype($permission)));
        }
    }

    /**
     * @param string $email
     * @return bool
     */
    public function setAltEmail(string $email = null): bool
    {
        if ($email === null or $email === "") {
            $this->altEmail = null;
            return true;
        }
        $dbc = new DatabaseConnection();
        if (strlen($email) <= $dbc->getMaximumLength("user", "txEmailAlt") and $filtered = filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->altEmail = filter_var($filtered, FILTER_SANITIZE_EMAIL);
            return true;
        }
        return false;
    }

    /**
     * @param string $city
     * @return bool
     */
    public function setCity(string $city): bool
    {
        $dbc = new DatabaseConnection();
        if (strlen($city) <= $dbc->getMaximumLength("user", "txCity")) {
            $this->city = $city;
            return true;
        }
        return false;
    }

    /**
     * Sets the country of residence of the current User. $country may either be a country's ISO code, its name,
     * or the value of its primary key in the database. $mode may be one of self::MODE_ISO, self::MODE_NAME, or
     * self::MODE_DBID.
     * Returns true on success, false otherwise.
     *
     * @param string|int
     * @param int $mode
     * @return bool
     */
    public function setCountry($country, int $mode = self::MODE_ISO): bool
    {
        if (gettype($country) == "string" or gettype($country) == "integer") {
            $dbc = new DatabaseConnection();
            if ($mode === self::MODE_NAME or $mode === self::MODE_ISO) {
                $country = strtoupper($country);
                $params = ["s", $country];
                if ($mode === self::MODE_ISO) {
                    $result = $dbc->query("select", "SELECT * FROM `country` WHERE `idISO`=?", $params);
                } else {
                    $result = $dbc->query("select", "SELECT * FROM `country` WHERE UPPER(`nmName`)=?", $params);
                }
            } else {
                $params = ["i", $country];
                $result = $dbc->query("select", "SELECT * FROM `country` WHERE `pkCountryID`=?", $params);
            }

            if ($result) {
                if (isset($this->province)) {
                    $params = ["i", $this->getProvince(self::MODE_DBID)];
                    $result2 = $dbc->query("select", "SELECT * FROM `province` WHERE `pkStateID`=?", $params);
                    if ($result2) {
                        $params = ["i", $result2["fkCountryID"]];
                        $result3 = $dbc->query("select", "SELECT * FROM `country` WHERE `pkCountryID`=?", $params);
                        if ($result3) {
                            if ($result3["pkCountryID"] != $result["pkCountryID"]) {
                                unset($this->province);
                            }
                        }
                    }
                }
                $this->country["pkCountryID"] = $result["pkCountryID"];
                $this->country["idISO"] = $result["idISO"];
                $this->country["nmName"] = $result["nmName"];
                $this->country["idPhoneCode"] = $result["idPhoneCode"];
                return true;
            }
        }
        return false;
    }

    /**
     * @param string $email
     * @return bool
     */
    public function setEmail(string $email): bool
    {
        if ($email === null) {
            $this->email = null;
            return true;
        }
        $dbc = new DatabaseConnection();
        if (strlen($email) <= $dbc->getMaximumLength("user", "txEmail") and $filtered = filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->email = filter_var($filtered, FILTER_SANITIZE_EMAIL);
            return true;
        }
        return false;
    }

    /**
     * @param string $fName
     * @return bool
     */
    public function setFName(string $fName): bool
    {
        $dbc = new DatabaseConnection();
        if (strlen($fName) <= $dbc->getMaximumLength("user", "nmFirst")) {
            $this->fName = $fName;
            return true;
        }
        return false;
    }

    /**
     * @param string $gradSemester
     * @return bool
     */
    public function setGradSemester(string $gradSemester): bool
    {
        $dbc = new DatabaseConnection();
        $result = $dbc->query("select", "SELECT SUBSTRING(COLUMN_TYPE,5) AS `enum`
                                                        FROM `information_schema`.`COLUMNS`
                                                        WHERE `TABLE_SCHEMA` = '" . $dbc->getDatabaseName() . "' 
                                                            AND `TABLE_NAME` = 'user'
                                                            AND `COLUMN_NAME` = 'enGradSemester'");
        $value = trim($result["enum"], "()");
        $values = explode(",", $value);
        $values = array_map("trim", $values, array_fill(0, count($values), "'"));
        if (in_array($gradSemester, $values)) {
            $this->gradSemester = $gradSemester;
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param int $gradYear
     * @return bool
     */
    public function setGradYear(int $gradYear): bool
    {
        $options = [
            "options" => [
                "min_range" => 1970,
                "max_range" => 3000
            ]
        ];
        if ($filtered = filter_var($gradYear, FILTER_VALIDATE_INT, $options)) {
            $this->gradYear = $filtered;
            return true;
        }
        return false;
    }

    /**
     * @param bool $isActive
     * @return bool
     */
    public function setIsActive(bool $isActive): bool
    {
        $this->isActive = $isActive;
        return true;
    }

    /**
     * @param string $lName
     * @return bool
     */
    public function setLName(string $lName): bool
    {
        $dbc = new DatabaseConnection();
        if (strlen($lName) <= $dbc->getMaximumLength("user", "nmLast")) {
            $this->lName = $lName;
            return true;
        }
        return false;
    }

    /**
     * @param int $phone
     * @return bool
     */
    public function setPhone(int $phone): bool
    {
        $phoneNumberUtil = libphonenumber\PhoneNumberUtil::getInstance();
        $phoneNumberObject = $phoneNumberUtil->parse($phone, $this->getCountry(self::MODE_ISO));
        $isValid = $phoneNumberUtil->isValidNumberForRegion($phoneNumberObject, $this->getCountry(self::MODE_ISO));

        if ($isValid) {
            $this->phone = $phoneNumberUtil->format($phoneNumberObject, \libphonenumber\PhoneNumberFormat::E164);
            return true;
        }
        return false;
    }

    /**
     * @param int $postalCode
     * @return bool
     */
    public function setPostalCode(int $postalCode = null): bool
    {
        if (isset($postalCode)) {
            $postalCode = strtoupper($postalCode);
            $postalCode = preg_replace("/[^0-9A-Z]/", "", $postalCode);
            $dbc = new DatabaseConnection();
            if (strlen($postalCode) <= $dbc->getMaximumLength("user", "txPostalCode")) {
                $this->postalCode = $postalCode;
                return true;
            }
            return false;
        } else {
            $this->postalCode = null;
            return true;
        }
    }

    /**
     * @param string $province ISO code or province name
     * @param int $mode Indicates input types, and must be either MODE_ISO or MODE_NAME
     * @return bool
     */
    public function setProvince(string $province, int $mode): bool
    {
        $province = strtoupper($province);
        $dbc = new DatabaseConnection();
        $params = ["s", $province];
        if ($mode === self::MODE_ISO) {
            $result = $dbc->query("select", "SELECT * FROM `province` WHERE `idISO`=?", $params);
        } else {
            $result = $dbc->query("select", "SELECT * FROM `province` WHERE UPPER(`nmName`)=?", $params);
        }

        if ($result) {
            $this->setCountry($result["fkCountryID"], self::MODE_DBID);
            $this->province["pkStateID"] = $result["pkStateID"];
            $this->province["idISO"] = $result["idISO"];
            $this->province["nmName"] = $result["nmName"];
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param string $streetAddress
     * @return bool
     */
    public function setStreetAddress(string $streetAddress): bool
    {
        $dbc = new DatabaseConnection();
        if (strlen($streetAddress) <= $dbc->getMaximumLength("user", "txStreetAddress")) {
            $this->streetAddress = $streetAddress;
            return true;
        }
        return false;
    }

    /**
     * @return bool
     * @throws Exception
     *
     * Pulls data stored in the database to the current User instance.
     */
    public function updateFromDatabase(): bool
    {
        if ($this->isInDatabase()) {
            $this->__construct2($this->getUserID(), self::MODE_DBID);
            return true;
        } else {
            throw new LogicException("User->updateFromDatabase() - Unable to pull from database when User instance is not stored in database");
        }
    }

    /**
     * @param string $password
     * @return bool
     */
    public function updatePassword(string $password): bool
    {
        $saltedHash = Hasher::cryptographicHash($password);
        if (is_array($saltedHash)) {
            $r1 = $this->setSalt($saltedHash["salt"]);
            $r2 = $this->setHash($saltedHash["hash"]);
            return $r1 and $r2;
        }
        return false;
    }

    /**
     * Pushes data stored in current User instance to the database.
     * Returns true if the update was completed successfully, false otherwise.
     *
     * @return bool
     */
    public function updateToDatabase(): bool
    {
        $dbc = new DatabaseConnection();
        if ($this->isInDatabase()) {
            $params = [
                "ssssssiiisissi",
                $this->getFName(),
                $this->getLName(),
                $this->getEmail(),
                $this->getAltEmail(),
                $this->getStreetAddress(),
                $this->getCity(),
                $this->getProvince(self::MODE_DBID),
                $this->getPostalCode(),
                $this->getPhone(),
                $this->getGradSemester(),
                $this->getGradYear(),
                $this->getSalt(),
                $this->getHash(),
                $this->getIsActive(),
                $this->getUserID()
            ];
            $result = $dbc->query("update", "UPDATE `user` SET 
                                      `nmFirst`=?,`nmLast`=?,`txEmail`=?,`txEmailAlt`=?,
                                      `txStreetAddress`=?,`txCity`=?,`fkProvinceID`=?,`txPostalCode`=?,
                                      `nPhone`=?,`enGradSemester`=?,`dtGradYear`=?,`blSalt`=?,
                                      `txHash`=?,`isActive`=?
                                      WHERE `pkUserID`=?", $params);

            $params = ["i", $this->getUserID()];
            $result = ($result and $dbc->query("delete", "DELETE FROM `userpermissions` WHERE `fkUserID`=?", $params));

            foreach ($this->getPermissions() as $permission) {
                $params = ["ii", $permission->getPermissionID(), $this->getUserID()];
                $result = ($result and $dbc->query("insert", "INSERT INTO `userpermissions` (`fkPermissionID`,`fkUserID`) VALUES (?,?)", $params));
            }
        } else {
            $params = [
                "ssssssiiisissi",
                $this->getFName(),
                $this->getLName(),
                $this->getEmail(),
                $this->getAltEmail(),
                $this->getStreetAddress(),
                $this->getCity(),
                $this->getProvince(self::MODE_DBID),
                $this->getPostalCode(),
                $this->getPhone(),
                $this->getGradSemester(),
                $this->getGradYear(),
                $this->getSalt(),
                $this->getHash(),
                $this->getIsActive()
            ];
            $result = $dbc->query("insert", "INSERT INTO `user` (`pkUserID`, 
                                          `nmFirst`, `nmLast`, `txEmail`, `txEmailAlt`, 
                                          `txStreetAddress`, `txCity`, `fkProvinceID`, `txPostalCode`, 
                                          `nPhone`, `enGradSemester`, `dtGradYear`, `blSalt`, 
                                          `txHash`, `isActive`) 
                                          VALUES 
                                          (NULL,?,?,?,?,?,?,?,?,?,?,?,?,?,?)", $params);

            $params = ["s", $this->getEmail()];
            $result2 = $dbc->query("select", "SELECT `pkUserID` FROM `user` WHERE `txEmail`=?", $params);

            $this->setUserID($result2["pkUserID"]);

            foreach ($this->getPermissions() as $permission) {
                $params = ["ii", $permission->getPermissionID(), $this->getUserID()];
                $result = ($result and $dbc->query("insert", "INSERT INTO `userpermissions` (`fkPermissionID`,`fkUserID`) VALUES (?,?)", $params));
            }

            $this->isInDatabase = $result;
        }

        return (bool)$result;
    }

    /**
     * @param string $hash
     * @return bool
     */
    private function setHash(string $hash): bool
    {
        $dbc = new DatabaseConnection();
        if (strlen($hash) == $dbc->getMaximumLength("user", "txHash")) {
            $this->hash = $hash;
            return true;
        }
        return false;
    }

    /**
     * @param string $salt
     * @return bool
     */
    private function setSalt(string $salt): bool
    {
        $dbc = new DatabaseConnection();
        if (strlen($salt) <= $dbc->getMaximumLength("user", "blSalt") and strlen($salt) == strlen(Hasher::randomSalt())) {
            $this->salt = $salt;
            return true;
        }
        return false;
    }

    /**
     * @param int $userID
     * @return bool
     */
    private function setUserID(int $userID): bool
    {
        if ($this->isInDatabase()) {
            return false;
        } else {
            $options = [
                "options" => [
                    "min_range" => 0,
                    "max_range" => pow(2, 31) - 1
                ]
            ];
            if ($filtered = filter_var($userID, FILTER_VALIDATE_INT, $options)) {
                $this->userID = $filtered;
                return true;
            }
            return false;
        }
    }
}