<?php

/**
 * Created by PhpStorm.
 * User: Jacob
 * Date: 4/15/2017
 * Time: 11:35 PM
 */
class Submission
{

    const MODE_DESCRIPTION = 1;
    const MODE_ID = 2;
    const MODE_NAME = 3;
    const STATUS_CANCELLED = "Cancelled";
    const STATUS_FINAL = "Apply Final Edits";
    const STATUS_INITIAL = "Initial Review";
    const STATUS_PUBLISH = "Publish";
    const STATUS_REJECTED = "Rejected";
    const STATUS_REVISION = "Revision Review";

    /**
     * @var string
     */
    private $additionalAuthors;
    /**
     * @var User
     */
    private $author;
    /**
     * @var File
     */
    private $file;
    /**
     * @var array ["id"=>int, "name"=>string, "description"=>string]
     */
    private $form;
    /**
     * @var bool
     */
    private $isInDatabase;
    /**
     * @var File
     */
    private $licenseFile;
    /**
     * @var int
     */
    private $pageCount;
    /**
     * @var Publication|null
     */
    private $publication;
    /**
     * @var float
     */
    private $rating;
    /**
     * @var string
     */
    private $status;
    /**
     * @var int
     */
    private $submissionID;
    /**
     * @var string
     */
    private $title;

    /**
     * Submission constructor.
     */
    public function __construct()
    {
        //This segment of code originally written by rayro@gmx.de
        //http://php.net/manual/en/language.oop5.decon.php
        $a = func_get_args();
        $i = func_num_args();
        if ($i > 6) {
            $i = 6;
        }
        if (method_exists($this, $f = '__construct' . $i)) {
            call_user_func_array(array($this, $f), $a);
        }
    }

    /**
     * @param int $submissionID
     * @throws Exception
     */
    public function __construct1(int $submissionID)
    {
        $dbc = new DatabaseConnection();
        $params = ["i", $submissionID];
        $submission = $dbc->query("select", "SELECT * FROM `submission` WHERE `pkSubmissionID` = ?", $params);
        if ($submission) {
            $result = [
                $this->setSubmissionID($submission["pkSubmissionID"]),
                $this->setAdditionalAuthors($submission["txAdditionalAuthors"]),
                $this->setAuthor(new User($submission["fkAuthorID"], User::MODE_DBID)),
                $this->setForm($submission["fkFormID"], self::MODE_ID),
                $this->setFile(new File($submission["fkFilename"])),
                $this->setLicenseFile(new File($submission["fkLicenseFile"])),
                $this->setPageCount($submission["nPageCount"]),
                $this->setPublication(new Publication($submission["fkPublicationID"])),
                $this->setRating($submission["nRating"]),
                $this->setStatus($submission["enStatus"]),
                $this->setTitle($submission["nmTitle"]),
            ];
            $this->isInDatabase = true;
            if (in_array(false, $result, true)) {
                throw new Exception("Submission->__construct1($submissionID) - Unable to construct Submission object; variable assignment failure - (" . implode(" ", array_keys($result, false, true)) . ")");
            }
        } else {
            throw new Exception("Submission->__construct1($submissionID) - Unable to select from database");
        }
    }

    /**
     * @param string $additionalAuthors
     * @param User $author
     * @param File $file
     * @param string $form
     * @param int $pageCount
     * @param string $title
     * @param Publication|null $publication
     * @throws Exception
     */
    public function __construct6(string $additionalAuthors, User $author, File $file, string $form, int $pageCount, string $title, Publication $publication = null)
    {
        $result = [
            $this->setTitle($title),
            $this->setAdditionalAuthors($additionalAuthors),
            $this->setAuthor($author),
            $this->setForm($form),
            $this->setFile($file),
            $this->setPageCount($pageCount),
            $this->setPublication($publication),
            $this->setStatus(self::STATUS_INITIAL),
        ];
        $this->isInDatabase = false;
        if (in_array(false, $result, true)) {
            var_dump($file);
            var_dump($this->getFile());
            throw new Exception("Submission->__construct6($additionalAuthors, $author, $file, $form, $pageCount, $title, $publication) - Unable to construct Submission object; variable assignment failure - (" . implode(" ", array_keys($result, false, true)) . ")");
        }
    }

    /**
     * @return string|null
     */
    public function getAdditionalAuthors()
    {
        return $this->additionalAuthors;
    }

    /**
     * @return User
     */
    public function getAuthor(): User
    {
        return $this->author;
    }

    /**
     * @return File
     */
    public function getFile(): File
    {
        return $this->file;
    }

    /**
     * @param int $mode
     * @return string|int
     */
    public function getForm(int $mode = self::MODE_NAME)
    {
        switch ($mode) {
            case self::MODE_ID:
                return $this->form["id"];
                break;
            case self::MODE_DESCRIPTION:
                return $this->form["description"];
                break;
            default:
                return $this->form["name"];
        }
    }

    /**
     * @return File|null
     */
    public function getLicenseFile()
    {
        return $this->licenseFile;
    }

    /**
     * @return int
     */
    public function getPageCount(): int
    {
        return $this->pageCount;
    }

    /**
     * @return null|Publication
     */
    public function getPublication()
    {
        return $this->publication;
    }

    /**
     * @return float|null
     */
    public function getRating()
    {
        return $this->rating;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @return int
     */
    public function getSubmissionID(): int
    {
        return $this->submissionID;
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
     * @param string $additionalAuthors
     * @return bool
     */
    public function setAdditionalAuthors(string $additionalAuthors=null)
    {
        $dbc = new DatabaseConnection();
        if ($additionalAuthors === null or strlen($additionalAuthors) <= $dbc->getMaximumLength("submission", "txAdditionalAuthors")) {
            $this->additionalAuthors = $additionalAuthors;
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param User $author
     * @return bool
     */
    public function setAuthor(User $author): bool
    {
        if ($author->isInDatabase()) {
            $this->author = $author;
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param File $file
     * @return bool
     */
    public function setFile(File $file)
    {
        if ($file->isInDatabase()) {
            $this->file = $file;
            return true;
        } else {
            return false;
        }
        //TODO: allow/disallow setting the file based on mime type and form.
    }

    /**
     * @param $form
     * @param int $mode
     * @return bool
     */
    public function setForm($form, int $mode = self::MODE_NAME): bool
    {
        $dbc = new DatabaseConnection();
        if ($mode === self::MODE_ID) {
            $params = ["i", $form];
            $result = $dbc->query("select", "SELECT * FROM `form` WHERE `pkFormID` = ?", $params);
        } else if ($mode === self::MODE_NAME) {
            $params = ["s", $form];
            $result = $dbc->query("select", "SELECT * FROM `form` WHERE `nmTitle` = ?", $params);
        } else {
            return false;
        }
        if ($result) {
            $this->form = [
                "id" => $result["pkFormID"],
                "name" => $result["nmTitle"],
                "description" => $result["txDescription"]
            ];
            return true;
        } else {
            return false;
        }
        //TODO: allow/disallow setting form based on current file mime type (if any), otherwise no restriction.
    }

    /**
     * @param File $licenseFile
     * @return bool
     */
    public function setLicenseFile(File $licenseFile): bool
    {
        if ($licenseFile->isInDatabase()) {
            $this->licenseFile = $licenseFile;
            return true;
        } else {
            return false;
        }
        //TODO: allow/disallow setting license file based on mime type
    }

    /**
     * @param int $pageCount
     * @return bool
     */
    public function setPageCount(int $pageCount): bool
    {
        $dbc = new DatabaseConnection();
        $maxLength = $dbc->getMaximumLength("submission", "nPageCount");
        if (strlen($pageCount) <= $maxLength) {
            $this->pageCount = $pageCount;
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param Publication|null $publication
     * @return bool
     */
    public function setPublication(Publication $publication = null): bool
    {
        $this->publication = $publication;
        return true;
    }

    /**
     * @param float $rating
     * @return bool
     */
    public function setRating(float $rating): bool
    {
        $dbc = new DatabaseConnection();
        $mLength = stripos($rating, ".");
        $dLength = strlen($rating) - strrpos($rating, ".") - 1;
        $maxLength = $dbc->getMaximumLength("submission", "nRating");
        if ($mLength <= $maxLength["NUMERIC_PRECISION"] and $dLength <= $maxLength["NUMERIC_SCALE"]) {
            $this->rating = $rating;
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param string $status
     * @return bool
     */
    public function setStatus(string $status): bool
    {
        $possible = [
            self::STATUS_CANCELLED, self::STATUS_FINAL, self::STATUS_INITIAL,
            self::STATUS_PUBLISH, self::STATUS_REJECTED, self::STATUS_REVISION,
        ];
        if (in_array($status, $possible)) {
            $this->status = $status;
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
        if (strlen($title) <= $dbc->getMaximumLength("submission", "nmTitle")) {
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
            $this->__construct1($this->getSubmissionID());
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
        $file = $this->getFile() === null ? null : $this->getFile()->getInternalName();
        $licenseFile = $this->getLicenseFile() === null ? null : $this->getLicenseFile()->getInternalName();
        $publication = $this->getPublication() === null ? null : $this->getPublication()->getPublicationID();

        if ($this->isInDatabase()) {
            $params = [
                "sisisiidssi",
                $this->getTitle(),
                $this->getAuthor()->getUserID(),
                $file,
                $this->getForm(self::MODE_ID),
                $licenseFile,
                $publication,
                $this->getPageCount(),
                $this->getRating(),
                $this->getStatus(),
                $this->getAdditionalAuthors(),
                $this->getSubmissionID()
            ];
            $result = $dbc->query("update", "UPDATE `submission` SET 
                                                          `nmTitle` = ?, 
                                                          `fkAuthorID` = ?, 
                                                          `fkFilename` = ?, 
                                                          `fkFormID` = ?, 
                                                          `fkLicenseFile` = ?,
                                                          `fkPublicationID` = ?,
                                                          `nPageCount` = ?,
                                                          `nRating` = ?,
                                                          `enStatus` = ?,
                                                          `txAdditionalAuthors` = ?
                                                           WHERE `pkSubmissionID` = ?", $params);
        } else {
            $params = [
                "sisisiidss",
                $this->getTitle(),
                $this->getAuthor()->getUserID(),
                $file,
                $this->getForm(self::MODE_ID),
                $licenseFile,
                $publication,
                $this->getPageCount(),
                $this->getRating(),
                $this->getStatus(),
                $this->getAdditionalAuthors()
            ];
            $result = $dbc->query("insert", "INSERT INTO `submission` 
                                                        (`pkSubmissionID`, `nmTitle`, `fkAuthorID`, `fkFilename`, 
                                                        `fkFormID`, `fkLicenseFile`, `fkPublicationID`, `nPageCount`,
                                                        `nRating`, `enStatus`, `txAdditionalAuthors`) VALUES (NULL,?,?,?,?,?,?,?,?,?,?)", $params);
            $submission = $dbc->query("select", "SELECT LAST_INSERT_ID() AS `id`");
            if ($submission) {
                $this->setSubmissionID($submission["id"]);
            }
            $result = ($result and $submission);
        }
        return (bool)$result;
    }

    /**
     * @param int $submissionID
     * @return bool
     */
    private function setSubmissionID(int $submissionID): bool
    {
        $dbc = new DatabaseConnection();
        if (strlen($submissionID) <= $dbc->getMaximumLength("submission", "pkSubmissionID")) {
            $this->submissionID = $submissionID;
            return true;
        } else {
            return false;
        }
    }

}