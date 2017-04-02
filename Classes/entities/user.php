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

    private $email;
    private $fname;
    private $lname;
    private $addr;
    private $city;
    private $zip;
    private $phone;
    private $gradsemester;
    private $gradyear;
    private $isactive;

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
        $this->email = $email;
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
        $this->fname = $fname;
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
        $this->lname = $lname;
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
        $this->addr = $addr;
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
        $this->city = $city;
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
        $this->zip = $zip;
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
        $this->phone = $phone;
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
    public function setGradsemester($gradsemester)
    {
        $this->gradsemester = $gradsemester;
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