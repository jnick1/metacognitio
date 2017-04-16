<?php

/**
 * Created by PhpStorm.
 * User: Jacob
 * Date: 4/6/2017
 * Time: 11:41 PM
 */
class Logger
{
    //TODO: Any implementation for this class.

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
        $this->logCache = array();
        $this->logRetrieved = array();
    }

    /**
     * Logs an event
     *
     * @param int $eventType
     */
    public function log(int $eventType)
    {
        //TODO: Finish implementation of this method (maybe switch-case for event types? how many parameters need to be used?)
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
    public function deleteLog(int $logID): bool
    {
        //TODO: any implementation for this method
    }
}