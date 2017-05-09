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
     * @var bool
     */
    private $inDatabase;
    /**
     * @var string
     */
    private $iterationName;
    /**
     * @var Publication[]
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
     * Constructs new Serial object from preexisting data in database.
     *
     * @param int $serialID
     * @throws Exception
     */
    public function __construct1(int $serialID)
    {
        $dbc = new DatabaseConnection();
        $params = ["i", $serialID];
        $serial = $dbc->query("select", "SELECT * FROM `serial` WHERE `pkSerialID` = ?", $params);
        if ($serial) {
            $result = [
                $this->setSerialID($serialID),
                $this->setISSN($serial["idISSN"]),
                $this->setIterationName($serial["enIterationName"]),
                $this->setTitle($serial["nmTitle"]),
            ];
            $this->publications = [];
            $this->inDatabase = true;
            $publications = $dbc->query("select multiple", "SELECT `pkPublicationID`, `idIteration`, `idEdition` FROM `publication` WHERE `fkSerialID` = ?", $params);
            if ($publications) {
                if (count($publications) > 0) {
                    foreach ($publications as $pub) {
                        $result[] = $this->addPublication(new Publication($pub["pkPublicationID"], $pub["idIteration"], $pub["idEdition"]));
                    }
                }
            } else {
                throw new Exception("Serial->__construct1($serialID) - Unable to select from database");
            }
            if (in_array(false, $result, true)) {
                throw new Exception("Serial->__construct1($serialID) - Unable to construct Serial object; variable assignment failure - (" . implode(" ", array_keys($result, false, true)) . ")");
            }
        }
    }

    /**
     * Construct a new Serial instance.
     *
     * @param string $title
     * @param string $iterationName
     * @param string|null $ISSN
     * @throws Exception
     */
    public function __construct2(string $title, string $iterationName, string $ISSN = null)
    {
        $result = [
            $this->setTitle($title),
            $this->setIterationName($iterationName),
            $this->setISSN($ISSN)
        ];
        $this->publications = [];
        $this->inDatabase = false;
        if (in_array(false, $result, true)) {
            throw new Exception("Serial->__construct2($title, $iterationName, $ISSN) - Unable to construct Serial object; variable assignment failure - (" . implode(" ", array_keys($result, false, true)) . ")");
        }
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return "{" . implode(" ", [$this->getTitle(), $this->getISSN(), $this->getIterationName(), $this->getSerialID()]) . "}";
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
     * @return Publication|null
     */
    public function getPublication(int $iteration, int $edition)
    {
        $pubs = $this->getPublications();
        foreach ($pubs as $pub) {
            // this is always true, but it is included to allow for ease of code auto-completion by PhpStorm (and so it doesn't bug out when I call class methods)
            if ($pub instanceof Publication) {
                if ($pub->getIterationID() === $iteration and $pub->getEditionID() === $edition) {
                    return $pub;
                }
            }
        }
        return null;
    }

    /**
     * @return Publication[]
     */
    public function getPublications(): array
    {
        return $this->publications;
    }

    /**
     * @return int|null
     */
    public function getSerialID()
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
     * @return bool
     */
    public function isInDatabase(): bool
    {
        return $this->inDatabase;
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
        if ($this->isInDatabase()) {
            $this->__construct1($this->getSerialID());
            return true;
        } else {
            return false;
        }
    }

    /**
     * @return bool
     */
    public function updateToDatabase(): bool
    {
        $dbc = new DatabaseConnection();
        if ($this->isInDatabase()) {
            $params = [
                "sssi",
                $this->getISSN(),
                $this->getTitle(),
                $this->getIterationName(),
                $this->getSerialID()
            ];
            $result = $dbc->query("update", "UPDATE `serial` SET `idISSN` = ?, `nmTitle` = ?, `enIterationName` = ? WHERE `pkSerialID` = ?", $params);
        } else {
            $params = [
                "sss",
                $this->getISSN(),
                $this->getTitle(),
                $this->getIterationName()
            ];
            $result = $dbc->query("insert", "INSERT INTO `serial` (`pkSerialID`, `idISSN`, `nmTitle`, `enIterationName`) VALUES (NULL,?,?,?)", $params);
            $serial = $dbc->query("select", "SELECT LAST_INSERT_ID() AS `id`");
            if ($serial) {
                $this->setSerialID($serial["id"]);
            }
            $result = ($result and $serial);
        }
        return (bool)$result;
    }

    /**
     * @param int $serialID
     * @return bool
     */
    private function setSerialID(int $serialID): bool
    {
        $this->serialID = $serialID;
        return true;
    }

}