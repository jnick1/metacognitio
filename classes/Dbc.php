<?php

/**
 * Class dbc
 * Originally created by Lionite, Mar 27, 2014
 * https://www.binpress.com/tutorial/using-php-with-mysql-the-right-way/17
 *
 * Modified to suit needs of the project.
 */
class Dbc
{
    /**
     * @var The database connection
     */
    protected static $connection;
    private static $inTransaction;

    /**
     * Destructor for dbc
     */
    public function __destruct()
    {
        if (isset(self::$connection)) {
            $this::$connection->close();
        }
    }

    /**
     * Connect to the database
     *
     * @return bool|mysqli false on failure / mysqli MySQLi object instance on success
     */
    public function connect()
    {
        // Try and connect to the database
        if (!isset(self::$connection)) {
            // Load configuration as an array. Use the actual location of your configuration file
            $config = parse_ini_file("../../../../secure/metacognitio/config.ini");
            self::$connection = mysqli_connect($config["host"], $config["username"], $config["password"], $config["database"]);
        }

        // If connection was not successful, handle the error
        if (self::$connection->connect_error !== null) {
            // Handle error - notify administrator, log to a file, show an error screen, etc.
            return false;
        }
        return self::$connection;
    }

    /**
     * Fetch the last error from the database
     *
     * @return string Database error message
     */
    public function error()
    {
        $connection = $this->connect();
        return $connection->error;
    }

    /**
     * @param string $type
     * @param string $query
     * @param array|null $parameters
     * @return array|bool
     *
     * This is a helper function to speed up the process of continually coding
     * prepared statements for queries. It accepts three arguments:
     *       $type : a string indicating what type of query should be executed. It may
     *               have any one of the following values: "select single", "select
     *               multiple", "update", "insert", or "delete"
     *      $query : a string containing an SQL query
     * $parameters : an array of parameters to be bound to any "?" (question marks)
     *               found in $query
     *
     * If %type is "update", "insert", or "delete", the function will return true or
     * false, indicating if the query was a success.
     *
     * If $type is "select single", the function will return an associative array
     * where the keys of the array are column names, and the values are the returned
     * values from the query for their respective columns.
     *
     * If $type is "select multiple", the function will return a multidimensional
     * array, where each element is an associative array, like in a result returned
     * from when $type is "select single". These arrays are indexed numerically.
     */
    public function query(string $type, string $query, array &$parameters = NULL)
    {
        $connection = $this->connect();
        $type = strtolower($type);

        switch ($type) {
            case "select single":
            case "select":

                if ($stmt = $connection->prepare($query)) {
                    if (!is_null($parameters)) {
                        call_user_func_array(array($stmt, "bind_param"), $parameters);
                    }
                    $stmt->execute();
                    $result = $stmt->get_result()->fetch_assoc();
                    $stmt->free_result();
                    $stmt->close();
                }
                return $result;
            case "select array":
            case "select multiple":

                $result = [];
                if ($stmt = $connection->prepare($query)) {
                    if (!is_null($parameters)) {
                        call_user_func_array(array($stmt, "bind_param"), $parameters);
                    }
                    $stmt->execute();
                    $res = $stmt->get_result();
                    while ($row = $res->fetch_assoc()) {
                        $result[] = $row;
                    }
                    $stmt->free_result();
                    $stmt->close();
                }
                if (empty($result)) {
                    return false;
                } else {
                    return $result;
                }

                break;
            case "insert":
            case "update" :
            case "delete" :

                if ($stmt = $connection->prepare($query)) {
                    if (!is_null($parameters)) {
                        call_user_func_array(array($stmt, "bind_param"), $parameters);
                    }
                    $result = $stmt->execute();
                    $stmt->close();
                }
                return $result;
            case "isset":
            case "exist":
            case "exists":

                if ($stmt = $connection->prepare($query)) {
                    if (!is_null($parameters)) {
                        call_user_func_array(array($stmt, "bind_param"), $parameters);
                    }
                    $stmt->execute();
                    $result = $stmt->get_result()->fetch_assoc();
                    $stmt->free_result();
                    $stmt->close();
                }
                return (bool)$result;
            default:
                return false;
        }
    }

    /**
     * Quote and escape value for use in a database query
     *
     * @param string $value The value to be quoted and escaped
     * @return string The quoted and escaped string
     */
    public function quote(string $value)
    {
        $connection = $this->connect();
        return "'" . $connection->real_escape_string($value) . "'";
    }

    /**
     * @return bool
     * @throws Exception
     */
    public function startTransaction()
    {
        $connection = $this->connect();
        if ($stmt = $connection->prepare("SET autocommit = 0; START TRANSACTION;")) {
            $result = $stmt->execute();
            $stmt->close();
            return $this::$inTransaction = $result;
        }
        throw new Exception("Unable to connect to database");
    }

    /**
     * @return bool
     * @throws Exception
     */
    public function commitTransaction()
    {
        $connection = $this->connect();
        if ($stmt = $connection->prepare("COMMIT;")) {
            $result = $stmt->execute();
            $stmt->close();
            return $this::$inTransaction = !$result;
        }
        throw new Exception("Unable to connect to database");
    }

    /**
     * @return bool
     * @throws Exception
     */
    public function rollbackTransaction()
    {
        $connection = $this->connect();
        if ($stmt = $connection->prepare("ROLLBACK;")) {
            $result = $stmt->execute();
            $stmt->close();
            return $result;
        }
        throw new Exception("Unable to connect to database");
    }
}