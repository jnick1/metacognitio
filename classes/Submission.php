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
     * @var bool
     */
    private $isInDatabase;

    /**
     * Submission constructor.
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

    public function __construct1(int $submissionID)
    {
        //TODO: finish implementation (load from database)
    }

    /**
     * @param string $additionalAuthors
     * @param User $author
     * @param File $file
     * @param string $form
     * @param int $pageCount
     * @param Publication $publication
     * @param string $title
     */
    public function __construct7(string $additionalAuthors, User $author, File $file, string $form, int $pageCount, Publication $publication, string $title)
    {
        $this->setTitle($title);
        $this->setAdditionalAuthors($additionalAuthors);
        $this->setAuthor($author);
        $this->setFile($file);
        $this->setForm($form);
        $this->setPageCount($pageCount);
        $this->setPublication($publication);
        $this->isInDatabase = false;
    }

    /**
     * @return string
     */
    public function getAdditionalAuthors(): string
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
    public function getForm(int $mode=self::MODE_NAME)
    {
        switch($mode) {
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
     * @return File
     */
    public function getLicenseFile(): File
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
     * @return float
     */
    public function getRating(): float
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
    public function setAdditionalAuthors(string $additionalAuthors)
    {
        $dbc = new DatabaseConnection();
        if (strlen($additionalAuthors) <= $dbc->getMaximumLength("submission", "txAdditionalAuthors")) {
            $this->additionalAuthors = $additionalAuthors;
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param User $author
     */
    public function setAuthor(User $author)
    {
        $this->author = $author;
    }

    /**
     * @param File $file
     */
    public function setFile(File $file)
    {
        $this->file = $file;
    }

    /**
     * @param $form
     * @param int $mode
     * @return bool
     */
    public function setForm($form, int $mode=self::MODE_NAME): bool
    {
        $dbc = new DatabaseConnection();
        if($mode === self::MODE_ID) {
            $result = $dbc->query("select multiple", "SELECT `pkFormID` FROM `form`");
            $forms = [];
            foreach($result as $r) {
                $forms[] = $r["pkFormID"];
            }
        } else if($mode === self::MODE_NAME) {
            $result = $dbc->query("select multiple", "SELECT `nmTitle` FROM `form`");
            $forms = [];
            foreach($result as $r) {
                $forms[] = $r["nmTitle"];
            }
        } else {
            return false;
        }
        if(in_array($form, $forms)) {
            $this->form = $form;
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param File $licenseFile
     */
    public function setLicenseFile(File $licenseFile): void
    {
        $this->licenseFile = $licenseFile;
    }

    /**
     * @param int $pageCount
     * @return bool
     */
    public function setPageCount(int $pageCount): bool
    {
        $dbc = new DatabaseConnection();
        $maxLength = $dbc->getMaximumLength("submission", "nPageCount");
        if(strlen($pageCount) <= $maxLength) {
            $this->pageCount = $pageCount;
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param Publication|null $publication
     */
    public function setPublication(Publication $publication = null): void
    {
        $this->publication = $publication;
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
    public function updateToDatabase(): bool
    {
        //TODO: finish implementation.
    }

    /**
     * @return bool
     */
    public function updateFromDatabase(): bool
    {
        //TODO: finish implementation.
    }

}