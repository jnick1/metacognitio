<?php

/**
 * Created by PhpStorm.
 * User: Jacob
 * Date: 4/15/2017
 * Time: 11:41 PM
 */
class Serial
{

    /**
     * @var string
     */
    private $ISSN;
    /**
     * @var string
     */
    private $iterationName;
    /**
     * @var array
     */
    private $publications;
    /**
     * @var int
     */
    private $serialID;
    /**
     * @var string
     */
    private $title;

    /**
     * Serial constructor.
     */
    public function __construct()
    {
        /* This segment of code originally written by rayro@gmx.de
         * http://php.net/manual/en/language.oop5.decon.php
         */
        $a = func_get_args();
        $i = func_num_args();
        if ($i > 2) {
            $i = 2;
        }
        if (method_exists($this, $f = '__construct' . $i)) {
            call_user_func_array(array($this, $f), $a);
        }
    }

    /**
     * @param int $serialID
     */
    public function __construct1(int $serialID)
    {
        $dbc = new DatabaseConnection();
        $params = ["i", $serialID];
        $serial = $dbc->query("select", "SELECT * FROM `serial` WHERE `pkSerialID` = ?", $params);
        //TODO: finish implementation (for pulling serial info from database)
    }

    /**
     * @param string $title
     * @param string $iterationName
     * @param string|null $ISSN
     */
    public function __construct2(string $title, string $iterationName, string $ISSN = null)
    {
        //TODO: finish implementation (for creating new serials)
    }

    /**
     * @param Publication $publication
     * @return bool
     */
    public function addPublication(Publication $publication): bool
    {
        if (in_array($publication, $this->getPublications())) {
            return false;
        } else {
            $this->publications[] = $publication;
            return true;
        }
    }

    /**
     * @return string|null
     */
    public function getISSN()
    {
        return $this->ISSN;
    }

    /**
     * @return string
     */
    public function getIterationName(): string
    {
        return $this->iterationName;
    }

    /**
     * @param int $iteration
     * @param int $edition
     * @return Publication
     */
    public function getPublication(int $iteration, int $edition): Publication
    {

    }

    /**
     * @return array
     */
    public function getPublications(): array
    {
        return $this->publications;
    }

    /**
     * @return int
     */
    public function getSerialID(): int
    {
        return $this->serialID;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param Publication $publication
     * @return bool
     */
    public function removePublication(Publication $publication): bool
    {
        if (in_array($publication, $this->getPublications())) {
            $pubs = $this->getPublications();
            foreach ($pubs as $key => $pub) {
                if ($pub == $publication) {
                    unset($pubs[$key]);
                }
            }
            $this->publications = $pubs;
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param string $ISSN
     * @return bool
     */
    public function setISSN(string $ISSN): bool
    {
        if (isset($ISSN)) {
            $dbc = new DatabaseConnection();
            if (strlen($ISSN) == $dbc->getMaximumLength("serial", "idISSN")) {
                $this->ISSN = $ISSN;
                return true;
            } else {
                return false;
            }
        } else {
            $this->ISSN = null;
            return true;
        }
    }

    /**
     * @param string $iterationName
     * @return bool
     */
    public function setIterationName(string $iterationName): bool
    {
        if ($iterationName == "Volume" or $iterationName == "Issue") {
            $this->iterationName = $iterationName;
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param string $title
     * @return bool
     */
    public function setTitle(string $title): bool
    {
        $dbc = new DatabaseConnection();
        if (strlen($title) <= $dbc->getMaximumLength("serial", "nmTitle")) {
            $this->title = $title;
            return true;
        } else {
            return false;
        }
    }

    /**
     * @return bool
     */
    public function updateFromDatabase(): bool
    {
        //TODO: implementation
    }

    /**
     * @return bool
     */
    public function updateToDatabase(): bool
    {
        //TODO: implementation
    }

    /**
     * @param int $serialID
     */
    private function setSerialID(int $serialID)
    {
        $this->serialID = $serialID;
    }

}