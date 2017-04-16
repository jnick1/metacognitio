<?php

/**
 * Created by PhpStorm.
 * User: Jacob
 * Date: 4/15/2017
 * Time: 11:35 PM
 */
class Submission
{

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

}