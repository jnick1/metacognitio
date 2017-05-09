<?php

/**
 * Created by PhpStorm.
 * User: Jacob
 * Date: 4/6/2017
 * Time: 11:41 PM
 */
class Logger
{

    /**
     * Stores the local cache of LogEvents waiting to be saved to the database.
     *
     * @var LogEvent[]
     */
    private $logCache;
    /**
     * Stores any retrieved log events from the database (read only; do not write this to the database).
     *
     * @var LogEvent[]
     */
    private $logRetrieved;

    /**
     * Logger constructor.
     */
    public function __construct()
    {
        $this->logCache = [];
        $this->logRetrieved = [];
    }

    /**
     * Generates a new name for a log dump file.
     *
     * @return string
     */
    private static function generateLogFileName(): string
    {
        return "log_" . time() . ".log";
    }

    /**
     * Outputs the contents of the cache to a string. Typically used only for debugging during development.
     *
     * @return string
     */
    public function __toString(): string {
        $output = "";
        $dbc = new DatabaseConnection();
        $i = count($this->logCache);
        foreach ($this->logCache as $log) {
            if ($log->getEventID() === null) {
                $output .= "[CACHE] " . $log;
            } else {
                $output .= "[" . $log->getEventID() . "] " . $log;
            }
            if($i > 1) {
                $output .= "\n";
            }
            $i--;
        }
        return $output;
    }

    /**
     * Resets the local cache to an empty state.
     *
     * @return bool
     */
    public function clearCache(): bool
    {
        $this->logCache = [];
        return true;
    }

    /**
     * Resets the log stored in the database to an empty state.
     *
     * @return bool
     */
    public function clearStoredLog(): bool
    {
        $dbc = new DatabaseConnection();
        return (bool)$dbc->query("truncate", "TRUNCATE TABLE `log`");
    }

    /**
     * Deletes all LogEvents from the current logCache which have not been saved to the database (i.e. those which
     * have a null $eventID).
     * Returns the number of logs that were deleted from the cache.
     *
     * @return int
     */
    public function clearUnsavedCache(): int
    {
        $start = count($this->logCache);
        foreach ($this->logCache as $key => $log) {
            if ($log->getEventID() === null) {
                unset($this->logCache[$key]);
            }
        }
        return $start - count($this->logCache);
    }

    /**
     * Deletes any log events matching the given ID from both the database and the local cache.
     * Returns the number of LogEvents deleted on success, false on failure.
     *
     * WARNING: it may be difficult to distinguish the return value of this function using ==
     * (as 0 and false are equivalent). If checking the output of the function, it is recommended for you to use
     * the identity operator, ===, instead of the equivalence operator.
     *
     * @param int $logID
     * @return bool|int
     */
    public function deleteLogEvent(int $logID): bool
    {
        $start = count($this->logCache);
        foreach ($this->logCache as $key => $log) {
            if ($log->getEventID() == $logID) {
                unset($this->logCache[$key]);
            }
        }
        $dbc = new DatabaseConnection();
        $count = $dbc->query("select", "SELECT COUNT(*) AS `count` FROM `log`");
        if ($count) {
            $params = ["i", $logID];
            $delete = $dbc->query("delete", "DELETE FROM `log` WHERE `pkLogID` = ?", $params);
            if ($delete) {
                $count2 = $dbc->query("select", "SELECT COUNT(*) AS `count` FROM `log`");
                if ($count2) {
                    return ($start - count($this->logCache)) + ($count["count"] - $count2["count"]);
                } else {
                    return false;
                }
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * Dumps both the contents of the local log cache and the database log to a .log file in the server's "tmp"
     * directory. Each LogEvent is printed to the file on a separate line using the class's __toString method.
     * After Completing the dump, both the cache and the database are cleared of all log events.
     *
     * @return bool
     */
    public function dumpAndClearFullLog(): bool
    {
        $dump = $this->dumpFullLog();
        if ($dump) {
            return $this->clearCache() and $this->clearStoredLog();
        } else {
            return false;
        }
    }

    /**
     * Dumps the contents of the cache to a .log file in the server's "tmp" directory. Each LogEvent is printed to the
     * file on a separate line using the class's __toString method.
     * Return the number of bytes written to the file on success, false on failure.
     *
     * @return int|bool
     */
    public function dumpCache()
    {
        $output = "";
        $dbc = new DatabaseConnection();
        foreach ($this->logCache as $log) {
            if ($log->getEventID() === null) {
                $output .= "[CACHE] " . $log . PHP_EOL;
            } else {
                $output .= "[" . $log->getEventID() . "] " . $log . PHP_EOL;
            }
        }
        return file_put_contents(TMP_DIR . self::generateLogFileName(), $output);
    }

    /**
     * Dumps both the contents of the local log cache and the database log to a .log file in the server's "tmp"
     * directory. Each LogEvent is printed to the file on a separate line using the class's __toString method.
     * Return the number of bytes written to the file on success, false on failure.
     *
     * @return int|bool
     */
    public function dumpFullLog()
    {
        $output = "";
        foreach ($this->logCache as $log) {
            if ($log->getEventID() === null) {
                $output .= "[CACHE] " . $log . PHP_EOL;
            } else {
                $output .= "[" . $log->getEventID() . "] " . $log . PHP_EOL;
            }
        }
        $this->retrieveStoredLog();
        foreach ($this->logRetrieved as $log) {
            $output .= "[" . $log->getEventID() . "] " . $log . PHP_EOL;
        }
        return file_put_contents(TMP_DIR . self::generateLogFileName(), $output);
    }

    /**
     * Dumps the contents of the database log to a .log file in the server's "tmp" directory. Each LogEvent is
     * printed to the file on a separate line using the class's __toString method.
     * Return the number of bytes written to the file on success, false on failure.
     *
     * @return int|bool
     */
    public function dumpStoredLog()
    {
        $output = "";
        $this->retrieveStoredLog();
        foreach ($this->logRetrieved as $log) {
            $output .= "[" . $log->getEventID() . "] " . $log . PHP_EOL;
        }
        return file_put_contents(TMP_DIR . self::generateLogFileName(), $output);
    }

    /**
     * Logs an event based off of the provided event type.
     * The event type then determine which parameters are required.
     *
     * @param int $eventType
     * @param mixed[]|null $parameters ["file"=File|null, "table"=string|null, "identifiers"=mixed[]|null]
     * @return bool
     */
    public function log(int $eventType, array $parameters = null): bool
    {
        /*
         * This line is only included so that PhpStorm stops bothering me about "$table might not be set!" via the
         * switch-case block below.
         */
        $table = null;
        $identifiers = null;
        //First switch-case to do some smart assignment of parameters so they don't need to be passed in
        //TODO: smart-ify this first switch case to automatically grab some identifiers and allow objects to be passed in the $parameters array, instead of primary key values
        switch ($eventType) {
            //Events that modify a table
            case LogEvent::AUTHOR_BIOGRAPHY:
                $modificationType = "Table";
                $table = "authorbiographies";
                $identifiers = $parameters["identifiers"];
                break;
            case LogEvent::CONTACT_CREATE:
            case LogEvent::CONTACT_DELETE:
            case LogEvent::CONTACT_UPDATE:
                $modificationType = "Table";
                $table = "contact";
                $identifiers = $parameters["identifiers"];
                break;
            case LogEvent::EDITOR_UPDATE_FORMS:
                $modificationType = "Table";
                $table = "editorforms";
                $identifiers = $parameters["identifiers"];
                break;
            case LogEvent::EDITOR_MEETING_NO:
            case LogEvent::EDITOR_MEETING_YES:
                $modificationType = "Table";
                $table = "meetingeditors;";
                $identifiers = $parameters["identifiers"];
                break;
            case LogEvent::MEETING_UPDATE_LOCATION:
            case LogEvent::MEETING_UPDATE_TIME:
                $modificationType = "Table";
                $table = "meeting";
                $identifiers = $parameters["identifiers"];
                break;
            case LogEvent::NOTIFICATION_CREATE:
            case LogEvent::NOTIFICATION_DISMISS:
                $modificationType = "Table";
                $table = "notification";
                $identifiers = $parameters["identifiers"];
                break;
            case LogEvent::PUBLICATION_STATUS_CANCELLED:
            case LogEvent::PUBLICATION_STATUS_PUBLISHED:
            case LogEvent::PUBLICATION_STATUS_WIP:
                $modificationType = "Table";
                $table = "publication";
                $identifiers = $parameters["identifiers"];
                break;
            case LogEvent::SERIAL_CREATE:
            case LogEvent::SERIAL_DELETE:
            case LogEvent::SERIAL_UPDATE:
                $modificationType = "Table";
                $table = "serial";
                $identifiers = $parameters["identifiers"];
                break;
            case LogEvent::SUBMISSION_STATUS_CANCELLED:
            case LogEvent::SUBMISSION_STATUS_FINAL:
            case LogEvent::SUBMISSION_STATUS_INITIAL:
            case LogEvent::SUBMISSION_STATUS_PUBLISH:
            case LogEvent::SUBMISSION_STATUS_REJECTED:
            case LogEvent::SUBMISSION_STATUS_REVISION:
            case LogEvent::SUBMISSION_UPDATE:
            case LogEvent::SUBMISSION_UPDATE_PUBLICATION:
                $modificationType = "Table";
                $table = "submission";
                $identifiers = $parameters["identifiers"];
                break;
            case LogEvent::USER_PERMISSION_ADD:
            case LogEvent::USER_PERMISSION_REMOVE:
            case LogEvent::USER_UPDATE:
            case LogEvent::USER_UPDATE_EMAIL:
            case LogEvent::USER_UPDATE_PASSWORD:
                $modificationType = "Table";
                $table = "user";
                $identifiers = $parameters["identifiers"];
                break;
            //Events that modify a file
            case LogEvent::FILE_ACTIVATE:
            case LogEvent::FILE_DEACTIVATE:
            case LogEvent::SUBMISSION_REVISION_UPLOAD:
                $modificationType = "File";
                break;
            //Events that modify a table and a file
            case LogEvent::CRITIQUE_CREATE:
            case LogEvent::CRITIQUE_DELETE:
            case LogEvent::CRITIQUE_UPDATE:
                $modificationType = "Table and file";
                $table = "critique";
                $identifiers = $parameters["identifiers"];
                break;
            case LogEvent::MEETING_CREATE:
            case LogEvent::MEETING_DELETE:
                $modificationType = "Table and file";
                $table = "meeting";
                $identifiers = $parameters["identifiers"];
                break;
            case LogEvent::PUBLICATION_CREATE:
            case LogEvent::PUBLICATION_DELETE:
            case LogEvent::PUBLICATION_UPDATE:
                $modificationType = "Table and file";
                $table = "publication";
                $identifiers = $parameters["identifiers"];
                break;
            case LogEvent::SUBMISSION_CREATE:
            case LogEvent::SUBMISSION_DELETE:
            case LogEvent::SUBMISSION_LICENSE:
                $modificationType = "Table and file";
                $table = "submission";
                $identifiers = $parameters["identifiers"];
                break;
            case LogEvent::SUBMISSION_REVISION_ANONYMIZE:
                $modificationType = "Table and file";
                $table = "anonymized";
                $identifiers = $parameters["identifiers"];
                break;
            case LogEvent::MEETING_UPDATE_AGENDA:
                $modificationType = "Table and file";
                $table = "meeting";
                $identifiers = $parameters["identifiers"];
                break;
            //Events that modify neither a table nor a file
            case LogEvent::CRITIQUE_SEND:
            case LogEvent::USER_FORGOT_PASSWORD_REQUEST:
            case LogEvent::USER_LOGIN:
            case LogEvent::USER_CREATE:
            case LogEvent::USER_DELETE:
            case LogEvent::USER_ACTIVATE:
            case LogEvent::USER_DEACTIVATE:
                $modificationType = "Neither table nor file";
                break;
            default:
                return false;
        }
        //Second switch-case to actually add the LogEvent to the cache.
        switch ($modificationType) {
            case "Table":
                $event = new LogEvent(Controller::getLoggedInUser(), $eventType, null, $table, $identifiers);
                $this->logCache[] = $event;
                return true;
            case "File":
                $event = new LogEvent(Controller::getLoggedInUser(), $eventType, $parameters["file"]);
                $this->logCache[] = $event;
                return true;
            case "Table and file":
                $event = new LogEvent(Controller::getLoggedInUser(), $eventType, $parameters["file"], $table, $identifiers);
                $this->logCache[] = $event;
                return true;
            case "Neither table nor file":
                $event = new LogEvent(Controller::getLoggedInUser(), $eventType);
                $this->logCache[] = $event;
                return true;
            default:
                return false;
        }
    }

    /**
     * Manually add a LogEvent to the current log cache. This function should only be used when you want to overwrite an
     * event already stored in the database, and so need to use an already-existing pkLogID. Alternatively, any events
     * that occur at a time other than immediately at the current time should be entered here.
     *
     * @param LogEvent $logEvent
     * @return bool
     */
    public function logManual(LogEvent $logEvent): bool
    {
        $this->logCache[] = $logEvent;
        return true;
    }

    /**
     * Examines all logs in the database and local cache whose timestamps are either after $startTime or are null for
     * inconsistencies. This mainly refers to times when log events occur out of a order when they should.
     * The function reports any anomalous events it finds in an array.
     * How you deal with them at this point is up to the user.
     *
     * @param DateTime $startTime
     * @return LogEvent[]
     */
    public function normalizeLog(DateTime $startTime)
    {
        //TODO: any implementation for this method. Low priority, focus on other stuff first
        return [];
    }

    /**
     * Updates the current $logRetrieved with the entire contents of the stored log in the database.
     * Returns the number of logs retrieved on success, false on failure.
     *
     * @return int|bool
     */
    public function retrieveStoredLog()
    {
        $this->logRetrieved = [];
        $dbc = new DatabaseConnection();
        $events = $dbc->query("select multiple", "SELECT `pkLogID` FROM `log`");
        if ($events) {
            foreach ($events as $event) {
                $this->logRetrieved[] = new LogEvent($event["pkLogID"]);
            }
            return count($this->logRetrieved);
        } else {
            return false;
        }
    }

    /**
     * Saves all LogEvents in the cache to the database log. When $overwrite is true, any logs stored in the database
     * that have the same pkLogID as a log stored in the cache will be overwritten by the most recently stored LogEvent
     * with that pkLogID. When $overwrite is false, any logs in the cache with the same pkLogID as a log already stored
     * in the database will not be written to the database.
     *
     * Afterwards, the local cache is reset to an empty state.
     *
     * @param bool $overwrite
     * @return bool
     */
    public function saveAndClearCache(bool $overwrite = false): bool
    {
        return $this->saveCache() and $this->clearCache();
    }

    /**
     * Saves all LogEvents in the cache to the database log. When $overwrite is true, any logs stored in the database
     * that have the same pkLogID as a log stored in the cache will be overwritten by the most recently stored LogEvent
     * with that pkLogID. When $overwrite is false, any logs in the cache with the same pkLogID as a log already stored
     * in the database will not be written to the database.
     *
     * @param bool $overwrite
     * @return bool
     */
    public function saveCache(bool $overwrite = false): bool
    {
        $dbc = new DatabaseConnection();
        $dbc->startTransaction();
        foreach ($this->logCache as $key => $log) {
            if ($log->getIdentifiers() === null and $log->getTable() !== null) {
                continue;
            } else {
                if ($log->getEventID() === null) {
                    $identifiers = json_encode(array_combine($log->getTable(LogEvent::MODE_DESCRIPTION), $log->getIdentifiers()));
                    $params = ["isssiis", null, $log->getTable(), $identifiers, $log->getFile()->getInternalName(), $log->getUser()->getUserID(), $log->getEventType(LogEvent::MODE_ID), $log->getTimestamp()->format("Y-m-d H:i:s")];
                    $insert = $dbc->query("insert", "INSERT INTO `log`(`pkLogID`, `nmTable`, `fkIdentifier`, `fkFilename`, `fkUserID`, `fkEventID`, `dtTimestamp`) VALUES (?,?,?,?,?,?,?)", $params);
                    $id = $dbc->query("select", "SELECT LAST_INSERT_ID() AS `id`");
                    if ($insert and $id) {
                        $this->logCache[$key]->setEventID($id["id"]);
                    } else {
                        $dbc->rollbackTransaction();
                        return false;
                    }
                } else {
                    if ($overwrite) {
                        if ($log->getIdentifiers() === null) {
                            continue;
                        } else {
                            $identifiers = json_encode(array_combine($log->getTable(LogEvent::MODE_DESCRIPTION), $log->getIdentifiers()));
                            $params = ["sssiisi", $log->getTable(), $identifiers, $log->getFile()->getInternalName(), $log->getUser()->getUserID(), $log->getEventType(LogEvent::MODE_ID), $log->getTimestamp()->format("Y-m-d H:i:s"), $log->getEventID()];
                            $update = $dbc->query("update", "UPDATE `log` SET `nmTable` = ?, `fkIdentifier` = ?, `fkFilename` = ?, `fkUserID` = ?, `fkEventID` = ?, `dtTimestamp` = ? WHERE `pkLogID` = ?", $params);
                            if (!$update) {
                                $dbc->rollbackTransaction();
                                return false;
                            }
                        }
                    }
                }
            }
        }
        $dbc->commitTransaction();
        return true;
    }
}