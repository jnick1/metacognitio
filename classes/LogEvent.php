<?php

/**
 * Created by PhpStorm.
 * User: Jacob
 * Date: 4/14/2017
 * Time: 9:10 PM
 */
class LogEvent
{

    const MODE_DESCRIPTION = 1;
    const MODE_ID = 2;
    const MODE_NAME = 3;

    /*
     * Event types and their pkEventID's in the database (needs to be updated every once in a while
     * so that any added event types are represented here).
     */
    //TODO: reorder these to get the numbers and their alphabetical code in order, and update records in the database
    const AUTHOR_BIOGRAPHY = 12;
    const AUTHOR_CANCEL = 9;
    const AUTHOR_LICENSE = 11;
    const AUTHOR_REVISION = 10;
    const AUTHOR_SUBMIT = 8;
    const SUBMISSION_CREATE = 13;
    const USER_ACTIVATE = 7;
    const USER_CREATE = 1;
    const USER_DEACTIVATE = 6;
    const USER_DELETE = 2;
    const USER_LOGIN = 4;
    const USER_PASSWORD = 5;
    const USER_UPDATE = 3;

    /**
     * @var int
     */
    private $eventID;
    /**
     * @var array ["id"=>int,"name"=>string,"description"=>string]
     */
    private $eventtype;
    /**
     * @var File
     */
    private $file;
    /**
     * Foreign key referencing primary key in the table specified by $table.
     * Thus, it can vary, and be either an string or an int.
     *
     * @var int|string
     */
    private $identifier;
    /**
     * An array storing both the name of the column, and the value of the identifier stored in the table referred to
     * in $table;
     *
     * @var array ["name"=string,"id"=string|int]
     */
    private $table;
    /**
     * @var string
     */
    private $timestamp;
    /**
     * @var User
     */
    private $user;

    public function __construct()
    {
        //This segment of code originally written by rayro@gmx.de
        //http://php.net/manual/en/language.oop5.decon.php
        $a = func_get_args();
        $i = func_num_args();
        if ($i > 2) {
            $i = 2;
        }
        if (method_exists($this, $f = '__construct' . $i)) {
            call_user_func_array(array($this, $f), $a);
        }
    }

    public function __construct1(int $eventID)
    {
        $dbc = new DatabaseConnection();
        $params = ["i", $eventID];
        $event = $dbc->query("select", "SELECT * FROM `log` WHERE `pkLogID`=?", $params);

        if ($event) {
            //TODO: finish implementation of this constructor
        }
    }

    public function __construct2(User $user, int $eventTypeID, int $timestamp = null, string $table = null, $identifier = null, File $file = null)
    {
        //TODO: finish implementation of this constructor
    }

    /**
     * @return int
     */
    public function getEventID(): int
    {
        return $this->eventID;
    }

    /**
     * @param int $mode
     * @return string|int
     */
    public function getEventType(int $mode)
    {
        switch($mode) {
            case self::MODE_ID:
                return $this->eventtype["id"];
            case self::MODE_DESCRIPTION:
                return $this->eventtype["description"];
            default:
                return $this->eventtype["name"];
        }
    }

    /**
     * @return File
     */
    public function getFile(): File
    {
        return $this->file;
    }

    /**
     * @return int|string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * @return string
     */
    public function getTable(): string
    {
        return $this->table;
    }

    /**
     * @return string
     */
    public function getTimestamp(): string
    {
        return $this->timestamp;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param int $eventTypeID
     * @return bool
     * @throws Exception|InvalidArgumentException
     */
    public function setEventType($eventType, int $mode): bool
    {
        $dbc = new DatabaseConnection();
        if($mode === self::MODE_ID) {
            $max = $dbc->query("select", "SELECT MAX(`pkEventID`) AS `max` FROM `event`")["max"];
            $options = [
                "options" => [
                    "min_range" => 1,
                    "max_range" => $max
                ]
            ];
            if ($filtered = filter_var($eventType, FILTER_VALIDATE_INT, $options)) {
                $params = ["i", $filtered];
                $event = $dbc->query("select", "SELECT `nmEvent`, `txDescription` FROM `event` WHERE `pkEventID`=?", $params);
                if ($event) {
                    $this->eventtype = ["id" => $filtered, "name" => $event["nmEvent"], "description" => $event["txDescription"]];
                    return true;
                } else {
                    throw new Exception("LogEvent->setEventType($eventType) -  Unable to select from database");
                }
            } else {
                throw new InvalidArgumentException("LogEvent->setEventType($eventType) -  Not a recognized event type");
            }
        }
        //TODO: finish implementation of different modes in this method
    }

    /**
     * @param File $file
     * @return bool
     */
    public function setFile(File $file): bool
    {
        $this->file = $file;
        //TODO: maybe add validation to ensure that file exists in database (implement isInDatabase in File class)
    }

    /**
     * @param int|string $identifier
     * @return bool
     * @throws Exception
     */
    public function setIdentifier($identifier): bool
    {
        if($this->getTable() === null) {
            throw new Exception("File->setIdentifier($identifier) - Unable to set foreign key identifier when table is null");
        } else {
            $dbc = new DatabaseConnection();
            $params = [""];
            $exists = $dbc->query("exists", "SELECT * FROM `".$this->getTable()."` WHERE `".$this->getIdentifierName()."` = ?", $params);
            //TODO: finish implementation of this method
        }
    }

    /**
     * @param string $table
     * @return bool
     * @throws Exception|InvalidArgumentException
     */
    public function setTable(string $table): bool
    {
        $dbc = new DatabaseConnection();
        $tables = $dbc->query("select multiple", "SHOW TABLES");

        if ($tables) {
            $tableList = [];
            foreach ($tables as $table) {
                $tableList[] = $table["Tables_in_metacognitiodb"];
            }
            if (in_array($table, $tableList)) {
                $this->table = $table;
                return true;
            } else {
                throw new InvalidArgumentException("LogEvent->setTable($table) - Unable to find table in database");
            }
        } else {
            throw new Exception("LogEvent->setTable($table) - Unable to select from database");
        }
    }

    /**
     * @param int $time
     * @return void
     */
    public function setTimestamp(int $time = null): void
    {
        if ($time !== null) {
            $this->timestamp = date('Y-m-d H:i:s', $time);
        } else {
            $this->timestamp = date('Y-m-d H:i:s', time());
        }
    }

    /**
     * @param int $eventID
     * @return bool
     */
    private function setEventID(int $eventID): bool
    {
        $this->eventID = $eventID;
        //TODO: maybe implement validation to make sure ID isn't used in database.
    }

}