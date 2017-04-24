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
     * @var array [LogEvent]
     */
    private $logCache;
    /**
     * Stores any retrieved log events from the database (read only; do not write this to the database).
     *
     * @var array [LogEvent]
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
     * Logs an event
     *
     * @param int $eventType
     * @return bool
     */
    public function log(int $eventType): bool
    {
        //TODO: Finish implementation of this method (maybe switch-case for event types? how many parameters need to be used?)
        switch ($eventType) {
            case LogEvent::AUTHOR_BIOGRAPHY:
                break;
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
    }

    /**
     * Examines all logs in the database and local cache whose timestamps are either after $startTime or are null for inconsistencies.
     * This mainly refers to times when log events occur out of a order when they should.
     * The function reports any anomalous events it finds in an array. How you deal with them at this point is up to the user.
     *
     * @param int $startTime
     * @return array
     */
    public function normalizeLog(int $startTime): array
    {
        //TODO: any implemenation for this method. Low priority, focus on other stuff first
    }

    /**
     * Deletes any log events matching the given ID from both the database and the local cache.
     *
     * @param int $logID
     * @return bool
     */
    public function deleteLogEvent(int $logID): bool
    {
        //TODO: any implementation for this method
    }

    /**
     * Dumps the contents of the cache to a .log file in the server's "tmp" directory. Each LogEvent is printed to the
     * file on a separate line using the class's __toString method.
     *
     * @return bool
     */
    public function dumpCache(): bool
    {
        //TODO: implement
    }

    /**
     * Dumps the contents of the database log to a .log file in the server's "tmp" directory. Each LogEvent is
     * printed to the file on a separate line using the class's __toString method.
     *
     * @return bool
     */
    public function dumpStoredLog(): bool
    {
        //TODO: implement
    }

    /**
     * Dumps both the contents of the local log cache and the database log to a .log file in the server's "tmp"
     * directory. Each LogEvent is printed to the file on a separate line using the class's __toString method.
     *
     * @return bool
     */
    public function dumpFullLog(): bool
    {
        //TODO: implement
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
        //TODO: implement
    }

    /**
     * Dumps both the contents of the local log cache and the database log to a .log file in the server's "tmp"
     * directory. Each LogEvent is printed to the file on a separate line using the class's __toString method.
     * After Completing the dump, both the cache and the database are cleared of all log events.
     *
     * @return bool
     */
    public function clearAndDumpFullLog(): bool
    {
        //TODO: implement
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

    }
}