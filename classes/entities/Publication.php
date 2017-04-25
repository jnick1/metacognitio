<?php

/**
 * Created by PhpStorm.
 * User: Jacob
 * Date: 4/15/2017
 * Time: 11:40 PM
 */
class Publication
{

    /**
     * Stores an integer representation of the ISBN for a publication, if available. If one is not available, then $ISBN
     * is set to null.
     *
     * @var int|null
     */
    private $ISBN;
    /**
     * Stores the file which contains the cover design of the publication to be used for easy visual recognition by the
     * user in an interface. Stores the value, null, if not available.
     *
     * @var File|null
     */
    private $coverFile;
    /**
     * A description of the publication. No specification exists on what this may be, as it can vary wildly between
     * publications.
     *
     * @var string
     */
    private $description;
    /**
     * Stores the edition identifier of the publication. For example, this would be 2 if a publication is
     * reprinted with additions, corrections, or the like that weren't present in the first edition. Publications
     * without multiple editions cause this value to default to 1.
     *
     * @var int
     */
    private $editionID;
    /**
     * Indicates whether a Publication object instance is also stored in the database.
     *
     * @var bool
     */
    private $isInDatabase;
    /**
     * Stores the iteration identifier of the publication. For example, this would be 2 if a publication is
     * the second volume in a series. For an independent publication, this value is always 1.
     *
     * @var int
     */
    private $iterationID;
    /**
     * Stores the actual publication date of the publication. Stores null if the publication has not been published.
     *
     * @var DateTime|null
     */
    private $publicationDate;
    /**
     * Stores the unique identifier of this publication. Must match a record stored in the database.
     *
     * @var int
     */
    private $publicationID;
    /**
     * Stores the serial to which this publication belongs. May be null if the publication is independent of a serial.
     * A publication's combination of $serial, $editionID, and $iterationID must be unique if $serial is not null.
     *
     * @var Serial|null
     */
    private $serial;
    /**
     * Stores the current production status of a publication. May hold one of the following values:
     * "WIP", "Published", "Cancelled"
     *
     * @var string
     */
    private $status;
    /**
     * Stores the target publication date for the publication. If the publication is already published, the date remains
     * stored as part of the publication.
     *
     * @var DateTime
     */
    private $targetPublicationDate;
    /**
     * Stores the title of the publication. If the publication is a member of a serial (i.e. $serial is not null), this
     * variable becomes a read-only copy of the serial's title. In independent publications, no such interaction occurs.
     *
     * @var string
     */
    private $title;

    /**
     * Publication constructor.
     */
    public function __construct()
    {
        //This segment of code originally written by rayro@gmx.de
        //http://php.net/manual/en/language.oop5.decon.php
        $a = func_get_args();
        $i = func_num_args();
        if ($i > 5) {
            $i = 5;
        }
        if (method_exists($this, $f = '__construct' . $i)) {
            call_user_func_array(array($this, $f), $a);
        }
    }

    /**
     * Construct a new Publication instance from database information.
     *
     * @param int $publicationID
     * @param int $iterationID
     * @param int $editionID
     * @throws Exception
     */
    public function __construct3(int $publicationID, int $iterationID, int $editionID)
    {
        $dbc = new DatabaseConnection();
        $publication = $dbc->query("select", "SELECT * FROM `publication` WHERE `pkPublicationID` = ? AND `idIteration` = ? AND `idEdition` = ?");
        if ($publication) {
            $result = [
                $this->setISBN($publication["idISBN"]),
                $this->setCoverFile(new File($publication["fkCoverFilename"])),
                $this->setDescription($publication["txDescription"]),
                $this->setEditionID($publication["idEdition"]),
                $this->isInDatabase = true,
                $this->setIterationID($publication["idIteration"]),
                $this->setPublicationDate(new DateTime($publication["dtPublished"])),
                $this->setPublicationID($publication["pkPublicationID"]),
                $this->setSerial(new Serial($publication["fkSerialID"])),
                $this->setStatus($publication["enStatus"]),
                $this->setTargetPublicationDate(new DateTime($publication["dtPublicationTarget"])),
                $this->setTitle($publication["nmTitle"])
            ];
            if (in_array(false, $result, true)) {
                throw new Exception("Publication->__construct3($publicationID, $iterationID, $editionID) - Unable to construct Publication object; variable assignment failure - (" . implode(" ", array_keys($result, false, true)) . ")");
            }
        } else {
            throw new Exception("Publication->__construct3($publicationID, $iterationID, $editionID) - Unable to select from database");
        }
    }

    /**
     * Construct a new Publication instance.
     *
     * @param string $title
     * @param int $publicationID
     * @param int $editionID
     * @param int $iterationID
     * @param string $description
     * @param string $status
     * @param DateTime|null $targetPublicationDate
     * @param DateTime|null $publicationDate
     * @param Serial|null $serial
     * @param int|null $ISBN
     * @param File|null $coverFile
     * @throws Exception
     */
    public function __construct5(string $title, int $publicationID, int $editionID, int $iterationID, string $description, string $status = "WIP", DateTime $targetPublicationDate = null, DateTime $publicationDate = null, Serial &$serial = null, int $ISBN = null, File $coverFile = null)
    {
        $result = [
            $this->setISBN($ISBN),
            $this->setCoverFile($coverFile),
            $this->setDescription($description),
            $this->setPublicationID($publicationID),
            $this->setEditionID($editionID),
            $this->setIterationID($iterationID),
            $this->setPublicationDate($publicationDate),
            $this->setSerial($serial),
            $this->setStatus($status),
            $this->setTargetPublicationDate($targetPublicationDate),
            $this->setTitle($title)
        ];
        if (in_array(false, $result, true)) {
            throw new Exception("Publication->__construct4($title, $description, $editionID, $iterationID, $status, ".$targetPublicationDate->format('Y-m-d').", ".$publicationDate->format('Y-m-d').", $serial, $ISBN, $coverFile) - Unable to construct Publication object; variable assignment failure - (" . implode(" ", array_keys($result, false, true)) . ")");
        }
        $this->isInDatabase = false;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return "{" . implode(" ", [$this->getTitle(), $this->getPublicationID(), $this->getIterationID(), $this->getEditionID(), $this->getISBN(), $this->isInDatabase()]) . "}";
    }

    /**
     * @return File|null
     */
    public function getCoverFile()
    {
        return $this->coverFile;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return int
     */
    public function getEditionID(): int
    {
        return $this->editionID;
    }

    /**
     * @return int|null
     */
    public function getISBN()
    {
        return $this->ISBN;
    }

    /**
     * @return int
     */
    public function getIterationID(): int
    {
        return $this->iterationID;
    }

    /**
     * @return DateTime|null
     */
    public function getPublicationDate()
    {
        return $this->publicationDate;
    }

    /**
     * @return int
     */
    public function getPublicationID(): int
    {
        return $this->publicationID;
    }

    /**
     * @return null|Serial
     */
    public function getSerial()
    {
        return $this->serial;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @return DateTime
     */
    public function getTargetPublicationDate(): DateTime
    {
        return $this->targetPublicationDate;
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
        return $this->isInDatabase;
    }

    /**
     * @param File|null $coverFile
     * @return bool
     */
    public function setCoverFile(File $coverFile = null): bool
    {
        if (isset($coverFile)) {
            if ($mime = $coverFile->getMimeType()) {
                if (in_array($mime, FileMaster::ALLOWED_IMG_MIME_TYPES)) {
                    $this->coverFile = $coverFile;
                    return true;
                }
            }
            return false;
        } else {
            $this->coverFile = $coverFile;
            return true;
        }
    }

    /**
     * @param string $description
     * @return bool
     */
    public function setDescription(string $description): bool
    {
        if ($filtered = filter_var($description, FILTER_SANITIZE_STRING)) {
            $dbc = new DatabaseConnection();
            if (strlen($filtered) <= $dbc->getMaximumLength("publication", "txDescription")) {
                $this->description = $description;
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * @param int $editionID
     * @return bool
     */
    public function setEditionID(int $editionID): bool
    {
        $dbc = new DatabaseConnection();
        if ($editionID >= 1 and
            strlen($editionID) <= $dbc->getMaximumLength("publication", "idEdition") and
            ($this->isInDatabase() or $this->checkProposedKeys($this->getIterationID(), $editionID, $this->getSerial()))
        ) {

            $this->editionID = $editionID;
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param int|null $ISBN
     * @return bool
     */
    public function setISBN($ISBN): bool
    {
        $dbc = new DatabaseConnection();
        $params = ["i", $ISBN];
        $result = $dbc->query("isset", "SELECT * FROM `publication` WHERE `idISBN` = ?", $params);
        if (strlen($ISBN) == $dbc->getMaximumLength("publication", "idISBN") and $result) {
            $this->ISBN = $ISBN;
            return true;
        }
        return false;
    }

    /**
     * @param int $iterationID
     * @return bool
     */
    public function setIterationID(int $iterationID): bool
    {
        $dbc = new DatabaseConnection();
        if ($iterationID >= 1 and
            strlen($iterationID) <= $dbc->getMaximumLength("publication", "idIteration") and
            ($this->isInDatabase() or $this->checkProposedKeys($iterationID, $this->getEditionID(), $this->getSerial()))
        ) {
            $this->iterationID = $iterationID;
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param DateTime|null $publicationDate
     * @return bool
     */
    public function setPublicationDate(DateTime $publicationDate = null): bool
    {
        $this->publicationDate = $publicationDate;
        return true;
    }

    /**
     * @param null|Serial $serial
     * @return bool
     */
    public function setSerial(Serial $serial = null): bool
    {
        $this->serial = $serial;
        return true;
    }

    /**
     * @param string $status
     * @return bool
     */
    public function setStatus(string $status): bool
    {
        $allowed = ["WIP", "Published", "Cancelled"];
        if (in_array($status, $allowed)) {
            $this->status = $status;
            return true;
        }
        return false;
    }

    /**
     * @param DateTime $targetPublicationDate
     * @return bool
     */
    public function setTargetPublicationDate(DateTime $targetPublicationDate): bool
    {
        $this->targetPublicationDate = $targetPublicationDate;
        return true;
    }

    /**
     * @param string $title
     * @return bool
     */
    public function setTitle(string $title): bool
    {
        $dbc = new DatabaseConnection();
        if (strlen($title) <= $dbc->getMaximumLength("publication", "nmTitle")) {
            $this->title = $title;
            return true;
        } else {
            return false;
        }
    }

    /**
     * Given the parameters specified, the function checks to see if their combination in the database would result
     * in a conflict of unique or primary keys, as the publication table contains multiple unique keys which must
     * always maintain their integrity.
     * Returns true if the given key selection has not been used in the database, false otherwise.
     *
     * @param int $iterationID
     * @param int $editionID
     * @param Serial $serial
     * @return bool
     */
    private function checkProposedKeys(int $iterationID, int $editionID, Serial $serial = null): bool
    {
        $dbc = new DatabaseConnection();
        $result = false;
        if (isset($serial)) {
            $params = [
                "iii",
                $iterationID,
                $editionID,
                $serial->getSerialID()
            ];
            $result = $dbc->query("isset", "SELECT * FROM `publication` WHERE `idIteration` = ? AND `idEdition` = ? AND `fkSerialID` = ?", $params);
        }
        $params = [
            "iii",
            $this->getPublicationID(),
            $iterationID,
            $editionID
        ];
        $result = ($result or $dbc->query("isset", "SELECT * FROM `publication` WHERE `pkPublicationID` = ? AND `idIteration` = ? AND `idEdition` = ?", $params));
        return !$result;
    }

    /**
     * @param int $publicationID
     * @return bool
     */
    private function setPublicationID(int $publicationID): bool
    {
        $dbc = new DatabaseConnection();
        if (strlen($publicationID) <= $dbc->getMaximumLength("publication", "pkPublicationID")) {
            $this->publicationID = $publicationID;
            return true;
        } else {
            return false;
        }
    }

}