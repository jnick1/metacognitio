<?php
/*
 * A user class to keep track of everything of importance regarding a
 * user's information.
 */

/*
 * At the present moment this is just getters and setters for stuff in the
 * data dictionary.
 */

class User {

    private $UserID;
    private $fname;
    private $lname;
    private $email;
    private $altEmail;
    private $addr;
    private $city;
    private $province;  //array("pkStateID"=>000,   "idISO"=>"",    "nmName"=>"")
    private $country;   //array("pkCountryID"=>000, "idISO"=>"",    "nmName"=>"",   "idPhoneCode"=>000)
    private $zip;
    private $phone;
    private $gradsemester;
    private $gradyear;
    private $isactive;

    /**
     * User constructor.
     */
    public function __construct()
    {
        //This segment of code originally written by rayro@gmx.de
        //http://php.net/manual/en/language.oop5.decon.php
        $a = func_get_args();
        $i = func_num_args();
        if (method_exists($this,$f='__construct'.$i)) {
            call_user_func_array(array($this,$f),$a);
        }
    }

    /**
     * @param $email
     * @return bool
     */
    public function __construct1($email)
    {
        $dbc = new dbc();
        $params=[$email];
        $user = $dbc->query("select","SELECT * FROM `user` WHERE `txEmail`=?",$params);

        if($user) {
            $params=[$user["fkProvinceID"]];
            $province = $dbc->query("select", "SELECT * FROM `province` WHERE `pkStateID`=?", $params);
            if ($province) {
                $params=[$province["fkCountryID"]];
                $country = $dbc->query("select", "SELECT * FROM `country` WHERE `pkCountryID`=?", $params);
            }
        }
        if($country)
        {
            $this->setUserID($user["pkUserID"]);
            $this->setFname($user["nmFirstName"]);
            $this->setLname($user["nmLastName"]);
            $this->setEmail($user["txEmail"]);
            $this->setAltEmail($user["txAltEmail"]);
            $this->setAddr($user["txStreetAddress"]);
            $this->setCity($user["txCity"]);
            $this->setProvince($province["idISO"]);
            $this->setCountry($country["idISO"]);
            $this->setZip($user["nZip"]);
            $this->setPhone($user["nPhone"]);
            $this->setGradsemester($user["enGradSemester"]);
            $this->setGradyear($user["dtGradYear"]);
            $this->setIsactive($user["isActive"]);
            return true;
        }
        return false;
    }

    /**
     * @return mixed
     */
    public function getUserID()
    {
        return $this->UserID;
    }

    /**
     * @param mixed $UserID
     */
    public function setUserID($UserID)
    {
        $options=[
            "options"=>[
                "min_range"=>1,
                "max_range"=>pow(2,31)-1
            ]
        ];
        if($filtered = filter_var($UserID,FILTER_VALIDATE_INT,$options))
        {
            $this->UserID = $filtered;
            return true;
        }
        return false;
    }

    /**
     * @return mixed
     */
    public function getFname()
    {
        return $this->fname;
    }

    /**
     * @param mixed $fname
     */
    public function setFname($fname)
    {
        if(strlen((string) $fname)<=20 and $filtered = filter_var($fname,FILTER_SANITIZE_STRING))
        {
            $this->fname = $filtered;
            return true;
        }
        return false;
    }

    /**
     * @return mixed
     */
    public function getLname()
    {
        return $this->lname;
    }

    /**
     * @param mixed $lname
     */
    public function setLname($lname)
    {
        if(strlen((string) $lname)<=20 and $filtered = filter_var($lname,FILTER_SANITIZE_STRING))
        {
            $this->lname = $filtered;
            return true;
        }
        return false;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email)
    {
        if($filtered = filter_var($email,FILTER_VALIDATE_EMAIL))
        {
            $this->email = filter_var($filtered, FILTER_SANITIZE_EMAIL);
            return true;
        }
        return false;
    }

    /**
     * @return mixed
     */
    public function getAltEmail()
    {
        return $this->altEmail;
    }

    /**
     * @param mixed $email
     */
    public function setAltEmail($email)
    {
        if($filtered = filter_var($email,FILTER_VALIDATE_EMAIL))
        {
            $this->altEmail = filter_var($filtered, FILTER_SANITIZE_EMAIL);
            return true;
        }
        return false;
    }

    /**
     * @return mixed
     */
    public function getAddr()
    {
        return $this->addr;
    }

    /**
     * @param mixed $addr
     */
    public function setAddr($addr)
    {
        if(strlen((string) $addr)<=50 and $filtered = filter_var($addr,FILTER_SANITIZE_STRING))
        {
            $this->addr = $filtered;
            return true;
        }
        return false;
    }

    /**
     * @return mixed
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param mixed $city
     */
    public function setCity($city)
    {
        if(strlen((string) $city)<=50 and $filtered = filter_var($city,FILTER_SANITIZE_STRING))
        {
            $this->city = $filtered;
            return true;
        }
        return false;
    }

    /**
     * @return mixed
     */
    public function getProvince($identifier)
    {
        $identifier = strtolower($identifier);
        switch ($identifier)
        {
            case "iso":
            case "idiso":
                return $this->province["idISO"];
                break;
            default:
                return $this->province["nmName"];
        }
    }

    /**
     * @param $province ISO code or province name
     * @return bool
     */
    public function setProvince($province) {
        if(gettype($province)=="string")
        {
            $province = strtoupper($province);
            $dbc = new dbc();
            $params=[$province];
            if(strlen($province)==2)
            {
                $result = $dbc->query("select", "SELECT * FROM `province` WHERE `idISO`=?",$params);
            }
            else
            {
                $result = $dbc->query("select", "SELECT * FROM `province` WHERE UPPER(`nmName`)=?",$params);
            }

            if($result)
            {
                if(isset($this->country) and $this->country["pkCountryID"] != $result["fkCountryID"])
                {
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
     * @return mixed
     */
    public function getCountry($identifier)
    {
        $identifier = strtolower($identifier);
        switch ($identifier)
        {
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
     * @param $country ISO code, country name, or primary key in database
     * @return bool true if successfully set, false otherwise
     */
    public function setCountry($country)
    {
        if(gettype($country)=="string" or gettype($country)=="integer")
        {
            $dbc = new dbc();
            $params=[$country];
            if(gettype($country)=="string") {
                $country = strtoupper($country);
                if(strlen($country)==2)
                {
                    $result = $dbc->query("select", "SELECT * FROM `country` WHERE `idISO`=?",$params);
                }
                else
                {
                    $result = $dbc->query("select", "SELECT * FROM `country` WHERE UPPER(`nmName`)=?",$params);
                }
            }
            else
            {
                $result = $dbc->query("select", "SELECT * FROM `country` WHERE `pkCountryID`=?",$params);
            }

            if($result)
            {
                if(isset($this->province))
                {
                    $params=[$this->province["idISO"]];
                    $result2 = $dbc->query("select","SELECT * FROM `province` WHERE `idISO`=?",$params);
                    if($result2)
                    {
                        $params=$result2["fkCountryID"];
                        $result3 = $dbc->query("select","SELECT * FROM `country` WHERE `pkCountryID`=?",$params);
                        if($result3)
                        {
                            if($result3["pkCountryID"] != $result["pkCountryID"])
                            {
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
     * @return mixed
     */
    public function getZip()
    {
        return $this->zip;
    }

    /**
     * @param mixed $zip
     */
    public function setZip($zip)
    {
        $options=[
            "options"=>[
                "min_range"=>10000,
                "max_range"=>99999
            ]
        ];
        if($filtered = filter_var($zip,FILTER_VALIDATE_INT,$options))
        {
            $this->zip = $filtered;
            return true;
        }
        return false;
    }

    /**
     * @return mixed
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param mixed $phone
     */
    public function setPhone($phone)
    {
        $phoneNumberUtil = \libphonenumber\PhoneNumberUtil::getInstance();
        $phoneNumberObject = $phoneNumberUtil->parse($phone, $this->getCountry("ISO"));
        $isvalid = $phoneNumberUtil->isValidNumberForRegion($phoneNumberObject, $this->getCountry("ISO"));

        if($isvalid)
        {
            $this->phone = $phoneNumberUtil->format($phoneNumberObject, PhoneNumberFormat::E164);
            return true;
        }
        return false;
    }

    /**
     * @return mixed
     */
    public function getGradsemester()
    {
        return $this->gradsemester;
    }

    /**
     * @param mixed $gradsemester
     */
    public function setGradsemester($gradSemester)
    {
        $values=array("Fall","Summer","Winter");
        if(in_array($gradSemester, $values))
        {
            $this->gradsemester = $gradSemester;
            return true;
        }
        else
        {
            return false;
        }

    }

    /**
     * @return mixed
     */
    public function getGradyear()
    {
        return $this->gradyear;
    }

    /**
     * @param mixed $gradyear
     */
    public function setGradyear($gradyear)
    {
        $this->gradyear = $gradyear;
    }

    /**
     * @return mixed
     */
    public function getIsactive()
    {
        return $this->isactive;
    }

    /**
     * @param mixed $isactive
     */
    public function setIsactive($isactive)
    {
        $this->isactive = $isactive;
    }


}