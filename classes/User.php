<?php

/*
* A user class to keep track of everything of importance regarding a
* user's information.
*/

class User
{

    private $addr;
    private $altEmail;
    private $city;
private $country;
    private $email;
    private $fName;
    private $gradSemester;
        private $gradYear;  //array("pkStateID"=>000,   "idISO"=>"",    "nmName"=>"")
        private $hash;   //array("pkCountryID"=>000, "idISO"=>"",    "nmName"=>"",   "idPhoneCode"=>000)
    private $isActive;
    private $isInDatabase;
    private $lName;
    private $phone;
private $province;
    private $salt;
    private $userID;
    private $zip;

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
     * @param $email
     * @return null|User
     */
    public static function load($email)
    {
        try {
            return new User($email);
        } catch (InvalidArgumentException $iae) {
            return null;
        }
    }

    /**
     * @param $email
     * @return bool
     */
    public function __construct1($email)
    {
        $dbc = new Dbc();
        $params = [$email];
        $user = $dbc->query("select", "SELECT * FROM `user` WHERE `txEmail`=?", $params);

        if ($user) {
            $params = [$user["fkProvinceID"]];
            $province = $dbc->query("select", "SELECT * FROM `province` WHERE `pkStateID`=?", $params);
            if ($province) {
                $params = [$province["fkCountryID"]];
                $country = $dbc->query("select", "SELECT * FROM `country` WHERE `pkCountryID`=?", $params);

                if ($country) {
                    $r1 = $this->setUserID($user["pkUserID"]);
                    $r2 = $this->setFName($user["nmFirstName"]);
                    $r3 = $this->setLName($user["nmLastName"]);
                    $r4 = $this->setEmail($user["txEmail"]);
                    $r5 = $this->setAltEmail($user["txAltEmail"]);
                    $r6 = $this->setAddr($user["txStreetAddress"]);
                    $r7 = $this->setCity($user["txCity"]);
                    $r8 = $this->setProvince($province["idISO"]);
                    $r9 = $this->setCountry($country["idISO"]);
                    $r10 = $this->setZip($user["nZip"]);
                    $r11 = $this->setPhone($user["nPhone"]);
                    $r12 = $this->setGradsemester($user["enGradSemester"]);
                    $r13 = $this->setGradYear($user["dtGradYear"]);
                    $r14 = $this->setSalt($user["blSalt"]);
                    $r15 = $this->setHash($user["txHash"]);
                    $r16 = $this->setIsActive($user["isActive"]);
                    $this->isInDatabase = true;
                    if (!($r1 and $r2 and $r3 and $r4 and $r5 and $r6 and $r7 and $r8 and $r9 and $r10 and $r11 and $r12 and $r13 and $r14 and $r15 and $r16)) {
                        throw new InvalidArgumentException();
                    }
                }
            }
        } else {
            throw new InvalidArgumentException("User not found");
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
     */
    public function __construct13($fName, $lName, $email, $altEmail, $addr, $city, $province, $zip, $phone, $gradSemester, $gradYear, $password, $isActive)
    {
        $dbc = new Dbc();
        $params = [$province];
        $province = $dbc->query("select", "SELECT * FROM `province` WHERE `idISO`=?", $params);
        if ($province) {
            $params = [$province["fkCountryID"]];
            $country = $dbc->query("select", "SELECT * FROM `country` WHERE `pkCountryID`=?", $params);

            if ($country) {
                $r1 = $this->setFName($fName);
                $r2 = $this->setLName($lName);
                $r3 = $this->setEmail($email);
                $r4 = $this->setAltEmail($altEmail);
                $r5 = $this->setAddr($addr);
                $r6 = $this->setCity($city);
                $r7 = $this->setProvince($province["idISO"]);
                $r8 = $this->setCountry($country["idISO"]);
                $r9 = $this->setZip($zip);
                $r10 = $this->setPhone($phone);
                $r11 = $this->setGradsemester($gradSemester);
                $r12 = $this->setGradYear($gradYear);
                $r13 = $this->updatePassword($password);
                $r14 = $this->setIsActive($isActive);
                $r15 = $this->isInDatabase = false;
                if (!($r1 and $r2 and $r3 and $r4 and $r5 and $r6 and $r7 and $r8 and $r9 and $r10 and $r11 and $r12 and $r13 and $r14 and $r15)) {
                    throw new InvalidArgumentException();
                }
            }
        }
        throw new InvalidArgumentException("Invalid province");
    }

    /**
     * @return mixed
     */
    public function getAddr()
    {
        return $this->addr;
    }

    /**
     * @return mixed
     */
    public function getAltEmail()
    {
        return $this->altEmail;
    }

    /**
     * @return mixed
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @return mixed
     */
    public function getCountry($identifier)
    {
        $identifier = strtolower($identifier);
        switch ($identifier) {
            case "iso":
            case "idiso":
                return $this->country["idISO"];
                break;
            case "phone":
            case "idphonecode":
                return $this->country["idPhoneCode"];
            default:
                return $this->country["nmName"];
        }
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @return mixed
     */
    public function getFName()
    {
        return $this->fName;
    }

    /**
     * @return mixed
     */
    public function getGradSemester()
    {
        return $this->gradSemester;
    }

    /**
     * @return mixed
     */
    public function getGradYear()
    {
        return $this->gradYear;
    }

    /**
     * @return mixed
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * @return mixed
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * @return mixed
     */
    public function getLName()
    {
        return $this->lName;
    }

    /**
     * @return mixed
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @return mixed
     */
    public function getProvince($identifier)
    {
        $identifier = strtolower($identifier);
        switch ($identifier) {
            case "iso":
            case "idiso":
                return $this->province["idISO"];
                break;
            case "pkstateid":
            case "stateid":
            case "internalid":
                return $this->province["pkStateID"];
            default:
                return $this->province["nmName"];
        }
    }

    /**
     * @return mixed
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * @return mixed
     */
    public function getUserID()
    {
        return $this->userID;
    }

    /**
     * @return mixed
     */
    public function getZip()
    {
        return $this->zip;
    }

    /**
     * @param mixed $addr
     */
    public function setAddr($addr)
    {
        if (strlen((string)$addr) <= 50 and $filtered = filter_var($addr, FILTER_SANITIZE_STRING)) {
            $this->addr = $filtered;
            return true;
        }
        return false;
    }

    /**
     * @param mixed $email
     */
    public function setAltEmail($email)
    {
        if ($filtered = filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->altEmail = filter_var($filtered, FILTER_SANITIZE_EMAIL);
            return true;
        }
        return false;
    }

    /**
     * @param mixed $city
     */
    public function setCity($city)
    {
        if (strlen((string)$city) <= 50 and $filtered = filter_var($city, FILTER_SANITIZE_STRING)) {
            $this->city = $filtered;
            return true;
        }
        return false;
    }

    /**
     * @param $country ISO code, country name, or primary key in database
     * @return bool true if successfully set, false otherwise
     */
    public function setCountry($country)
    {
        if (gettype($country) == "string" or gettype($country) == "integer") {
            $dbc = new Dbc();
            $params = [$country];
            if (gettype($country) == "string") {
                $country = strtoupper($country);
                if (strlen($country) == 2) {
                    $result = $dbc->query("select", "SELECT * FROM `country` WHERE `idISO`=?", $params);
                } else {
                    $result = $dbc->query("select", "SELECT * FROM `country` WHERE UPPER(`nmName`)=?", $params);
                }
            } else {
                $result = $dbc->query("select", "SELECT * FROM `country` WHERE `pkCountryID`=?", $params);
            }

            if ($result) {
                if (isset($this->province)) {
                    $params = [$this->province["idISO"]];
                    $result2 = $dbc->query("select", "SELECT * FROM `province` WHERE `idISO`=?", $params);
                    if ($result2) {
                        $params = $result2["fkCountryID"];
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
     * @param mixed $email
     */
    public function setEmail($email)
    {
        if ($filtered = filter_var($email, FILTER_VALIDATE_EMAIL) or $email === null) {
            if ($email === null) {
                $this->email = null;
            } else {
                $this->email = filter_var($filtered, FILTER_SANITIZE_EMAIL);
            }
            return true;
        }
        return false;
    }

    /**
     * @param mixed $fName
     */
    public function setFName($fName)
    {
        if (strlen((string)$fName) <= 20 and $filtered = filter_var($fName, FILTER_SANITIZE_STRING)) {
            $this->fName = $filtered;
            return true;
        }
        return false;
    }

    /**
     * @param mixed $gradsemester
     */
    public function setGradSemester($gradSemester)
    {
        $values = array("Fall", "Summer", "Winter");
        if (in_array($gradSemester, $values)) {
            $this->gradSemester = $gradSemester;
            return true;
        } else {
            return false;
        }

    }

    /**
     * @param mixed $gradYear
     */
    public function setGradYear($gradYear)
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
     * @param mixed $isActive
     */
    public function setIsActive($isActive)
    {
        if ($filtered = filter_var($isActive, FILTER_VALIDATE_BOOLEAN)) {
            $this->isActive = $filtered;
            return true;
        }
        return false;
    }

    /**
     * @param mixed $lName
     */
    public function setLName($lName)
    {
        if (strlen((string)$lName) <= 20 and $filtered = filter_var($lName, FILTER_SANITIZE_STRING)) {
            $this->lName = $filtered;
            return true;
        }
        return false;
    }

    /**
     * @param mixed $phone
     */
    public function setPhone($phone)
    {
        $phoneNumberUtil = \libphonenumber\PhoneNumberUtil::getInstance();
        $phoneNumberObject = $phoneNumberUtil->parse($phone, $this->getCountry("ISO"));
        $isvalid = $phoneNumberUtil->isValidNumberForRegion($phoneNumberObject, $this->getCountry("ISO"));

        if ($isvalid) {
            $this->phone = $phoneNumberUtil->format($phoneNumberObject, PhoneNumberFormat::E164);
            return true;
        }
        return false;
    }

    /**
     * @param $province ISO code or province name
     * @return bool
     */
    public function setProvince($province)
    {
        if (gettype($province) == "string") {
            $province = strtoupper($province);
            $dbc = new Dbc();
            $params = [$province];
            if (strlen($province) == 2) {
                $result = $dbc->query("select", "SELECT * FROM `province` WHERE `idISO`=?", $params);
            } else {
                $result = $dbc->query("select", "SELECT * FROM `province` WHERE UPPER(`nmName`)=?", $params);
            }

            if ($result) {
                if (isset($this->country) and $this->country["pkCountryID"] != $result["fkCountryID"]) {
                    $this->setCountry($result["fkCountryID"]);
                }
                $this->province["pkStateID"] = $result["pkStateID"];
                $this->province["idISO"] = $result["idISO"];
                $this->province["nmName"] = $result["nmName"];
                return true;
            }
        }
        return false;
    }

    /**
     * @param mixed $zip
     */
    public function setZip($zip)
    {
        $options = [
            "options" => [
                "min_range" => 10000,
                "max_range" => 99999
            ]
        ];
        if ($filtered = filter_var($zip, FILTER_VALIDATE_INT, $options)) {
            $this->zip = $filtered;
            return true;
        }
        return false;
    }

    /**
     * @return bool indicates if the update was completed successfully
     */
    public function updateDatabase()
    {
        $dbc = new Dbc();
        $params = [
            $this->getFName(),
            $this->getLName(),
            $this->getEmail(),
            $this->getAltEmail(),
            $this->getAddr(),
            $this->getCity(),
            $this->getProvince("stateID"),
            $this->getZip(),
            $this->getPhone(),
            $this->getGradSemester(),
            $this->getGradYear(),
            $this->getSalt(),
            $this->getHash(),
            $this->getIsActive()
        ];
        if ($this->isInDatabase) {
            $dbc->query("update", "UPDATE `user` SET 
                                      `nmFirst`=?,`nmLast`=?,`txEmail`=?,`txEmailAlt`=?,
                                      `txStreetAddress`=?,`txCity`=?,`fkProvinceID`=?,`nZip`=?,
                                      `nPhone`=?,`enGradSemester`=?,`dtGradYear`=?,`blSalt`=?,
                                      `txHash`=?,`isActive`=?
                                      WHERE `pkUserID`=?", $params);
        } else {
            $dbc->query("insert", "INSERT INTO `user` (`pkUserID`, 
                                          `nmFirst`, `nmLast`, `txEmail`, `txEmailAlt`, 
                                          `txStreetAddress`, `txCity`, `fkProvinceID`, `nZip`, 
                                          `nPhone`, `enGradSemester`, `dtGradYear`, `blSalt`, 
                                          `txHash`, `isActive`) 
                                          VALUES 
                                          (NULL,?,?,?,?,?,?,?,?,?,?,?,?,?,?)", $params);
            $this->isInDatabase = $dbc;
        }

        return (bool)$dbc;
    }

    /**
     * @param $password
     * @return bool
     */
    public function updatePassword($password)
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
     * @param $hash
     */
    private function setHash($hash)
    {
        if (strlen($hash) == 64) {
            $this->hash = $hash;
        }
    }

    /**
     * @param $salt
     * @return bool
     */
    private function setSalt($salt)
    {
        if (strlen($salt) == 16) {
            $this->salt = $salt;
            return true;
        }
        return false;
    }

    /**
     * @param $userID
     * @return bool
     */
    private function setUserID($userID)
    {
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