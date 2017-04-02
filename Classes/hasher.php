<?php
/*
 * A class with the purpose of hashing a given input. Implementation details
 * consist of calls to a hashing library.
 */

class Hasher {

    /**
     * @return string
     */
    public function randomHash()
    {
        return hash("sha256",random_bytes(16));
    }

    /**
     * @param $string
     * @return array
     */
    public function cryptographicHash($string)
    {
        $salt = random_bytes(16);
        $salt1 = substr($salt,0,strlen($salt)/2);
        $salt2 = substr($salt,strlen($salt)/2,strlen($salt));
        $hash = hash("sha256",$salt1.$string.$salt2);
        return array("salt"=>$salt,"hash"=>$hash);
    }

    /**
     * @param $string
     * @param $hash
     * @return bool
     */
    public function verifyHash($string, $hash)
    {
        return hash("sha256", $string)==$hash;
    }

    /**
     * @param $string
     * @param $salt
     * @param $hash
     * @return bool
     */
    public function verifySaltedHash($string, $salt, $hash)
    {
        $salt1 = substr($salt,0,strlen($salt)/2);
        $salt2 = substr($salt,strlen($salt)/2,strlen($salt));
        return hash("sha256", $salt1.$string.$salt2)==$hash;
    }

    /*
     * At the moment, this class looks useless, but it exists to separate
     * concerns of hashing from the rest of the system. This becomes very
     * useful if/when we decide to use an external library or change our
     * method later; only these methods in this class need be changed.
     */
}