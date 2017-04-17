<?php

/*
 *
 */

class Controller
{

    const FOOTER_FILE = "pages/pageassembly/footer.php";
    const HEADER_FILE = "pages/pageassembly/header.php";
    const MODULE_DIR = "pages";

    /**
     * @var Logger
     */
    public static $logger;
    /**
     * @var array file paths from site root to css needed by the page
     */
    private $CSS;
    /**
     * @var DatabaseConnection Page-wide connection to the database
     */
    private $dbc;
    /**
     * @var string
     */
    private $favicon;
    /**
     * @var string
     */
    private $homeDir;
    /**
     * @var array file paths from site root to javascript needed by the page
     */
    private $javaScript;
    /**
     * @var string
     */
    private $moduleDir;
    /**
     * @var string
     */
    private $pageTitle;
    /**
     * @var array
     */
    private $scrubbed;
    /**
     * @var int
     */
    private $tabIncrement;

    /**
     * Controller constructor.
     * @param string $pageTitle
     */
    public function __construct(string $pageTitle)
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        $this->scrubbed = array_map(array($this, "spamScrubber"), $_POST);
        $this->dbc = new DatabaseConnection();
        $this->tabIncrement = 1;
        $this->pageTitle = $pageTitle;
        $this->setHomeDir();
        $this->CSS = array();
        $this->javaScript = array();

        if(!isset(self::$logger)) {
            self::$logger = new Logger();
        }
    }

    /**
     * @return User|null
     */
    public static function getLoggedInUser()
    {
        if (self::isUserLoggedIn()) {
            return $_SESSION["user"];
        } else {
            return null;
        }
    }

    /**
     * @return bool
     */
    public static function isUserLoggedIn()
    {
        return isset($_SESSION["user"]);
    }

    /**
     * @param User|null $user
     * @return bool
     */
    public static function setLoggedInUser(User $user = null)
    {
        if ($user === null) {
            unset($_SESSION["user"]);
            return true;
        }
        if ($user instanceof User) {
            if ($user->isInDatabase()) {
                $_SESSION["user"] = $user;
                return true;
            } else {
                throw new LogicException("Controller:setLoggedInUser($user) - Unable to set non-database-saved user as logged-in user");
            }
        } else {
            throw new InvalidArgumentException("Controller::setLoggedInUser($user) -  Expected User: got " . (gettype($user) == "object" ? get_class($user) : gettype($user)));
        }
    }

    /**
     * A function to check for potentially dangerous inputs to forms
     * @param $value
     * @return string
     */
    private static function spamScrubber(string $value)
    {
        // This function is useful for preventing spam in form results.  Should be used on all $_POST arrays.
        // To Use:  $scrubbed=array_map('spam_scrubber',ARRAY_NAME);  Where ARRAY_NAME might be equal to an array such as $_POST
        // Then refer to the item in the array as $scrubbed['item_name']

        // List of very bad values:

        $very_bad = array('to:', 'cc:', 'bcc:', 'content-type:', 'mime-version:', 'multipart-mixed:', 'content-transfer-encoding:');
        // IF any of the very bad strings are in the submitted value, return an empty string:
        foreach ($very_bad as $v) {
            if (stripos($value, $v) !== false) {
                return '';
            }
        }
        // Replace any newline characters with spaces:
        //strip_tags() will remove all HTML and PHP tags. Safe, but remove if HTML formatting required.
        $value = strip_tags(str_replace(array("\r", "\n", "%0a", "%0d"), ' ', $value));
        return trim($value);
    }

    /**
     * Push new CSS into the local CSS specifi  cation
     * @param string $CSS
     * @return int|bool
     */
    public function addCSS(string $CSS)
    {
        if ($filtered = filter_var($CSS, FILTER_SANITIZE_STRING)) {
            if (!in_array($filtered, $this->CSS)) {
                return array_push($this->CSS, $filtered);
            }
        }
        return false;
    }

    /**
     * Push new javascript into the local js.js specifi  cation
     * @param string $javaScript
     * @return int|bool
     */
    public function addJavaScript(string $javaScript)
    {
        if ($filtered = filter_var($javaScript, FILTER_SANITIZE_STRING)) {
            if (!in_array($filtered, $this->javaScript)) {
                return array_push($this->javaScript, $filtered);
            }
        }
        return false;
    }

    /**
     * @return string
     */
    public function getAbsoluteHomeDir()
    {
        return $_SERVER["DOCUMENT_ROOT"] . $this->homeDir;
    }

    /**
     * @return mixed
     */
    public function getHomeDir()
    {
        return $this->homeDir;
    }

    /**
     * @return string
     */
    public function getModuleDir()
    {
        return $this->moduleDir;
    }

    /**
     * @return mixed
     */
    public function getPageTitle()
    {
        return $this->pageTitle;
    }

    /**
     * @return int
     */
    public function getTabIncrement()
    {
        return $this->tabIncrement;
    }

    /**
     *
     * @return bool
     */
    public function initModuleDir()
    {
        $stack = debug_backtrace();
        $pathToCaller = $stack[0]['file'];
        if (stripos($pathToCaller, Controller::MODULE_DIR)) {
            $pathArr = explode(DIRECTORY_SEPARATOR, $pathToCaller);
            $nextDir = array_search(Controller::MODULE_DIR, $pathArr) + 1;
            $this->moduleDir = Controller::MODULE_DIR . "/" . $pathArr[$nextDir] . "/";
            return true;
        }
        return false;
    }

    /**
     *
     */
    public function printHead()
    {
        /**
         * The four sections of this function used to be separate, but their only instance of use was to be called in
         * this function, so they were eliminated and recombined here to reduce overcrowding of functions in the
         * Controller class.
         */
        echo "<meta charset='UTF-8'>";
        /**
         * Print out the Favicon for the site (the little image in the browser tab)
         */
        if (isset($this->favicon)) {
            echo "<link rel='icon' type='" . mime_content_type($this->favicon) . "' href='" . $this->getHomeDir() . $this->favicon . "' sizes='128x128'>";
        }
        /**
         * Print out links to all CSS files needed by the page (CSS, not SCSS; browsers can't handle SCSS)
         */
        if (isset($this->CSS)) {
            foreach ($this->CSS as $CSS) {
                echo "<link rel='stylesheet' type='text/css' href='" . $this->homeDir . $CSS . "'>";
            }
        }
        /**
         * Print out the links to all JavaScript files needed by the page
         */
        if (isset($this->javaScript)) {
            foreach ($this->javaScript as $javaScript) {
                echo "<script type='text/javascript' src='" . $this->homeDir . $javaScript . "'></script>";
            }
        }
        /**
         * Finally, print out the title of the page in an HTML <title> tag
         */
        if (isset($this->pageTitle)) {
            echo "<title>" . $this->getPageTitle() . "</title>";
        }
    }

    /**
     * A middle-man to handle requests of the http variety
     * @return bool
     */
    public function processREQUEST()
    {
        switch (strtoupper($_SERVER["REQUEST_METHOD"])) {
            case "POST":
                return $this->processPOST();
                break;
            case "GET":
                return $this->processGET();
                break;
            default:
                return false;
        }
    }

    /**
     * @param string $favicon
     * @return bool
     */
    public function setFavicon(string $favicon)
    {
        if ($filtered = filter_var($favicon, FILTER_SANITIZE_STRING)) {
            $this->favicon = $filtered;
            return true;
        }
        return false;
    }

    /**
     * @param string $pageTitle
     * @return bool
     */
    public function setPageTitle(string $pageTitle)
    {
        if ($filtered = filter_var($pageTitle, FILTER_SANITIZE_STRING)) {
            $this->pageTitle = $filtered;
            return true;
        }
        return false;
    }

    /**
     * @param int $tabIncrement
     * @return bool
     */
    public function setTabIncrement(int $tabIncrement)
    {
        if ($filtered = filter_var($tabIncrement, FILTER_VALIDATE_INT)) {
            $this->tabIncrement = $filtered;
            return true;
        }
        return false;
    }

    /**
     * @return int
     */
    public function useTabIncrement()
    {
        return $this->tabIncrement++;
    }

    /**
     * @return bool
     */
    private function processGET()
    {
        $this->scrubbed = array_map(array("Controller", "spamScrubber"), $_GET);
        //TODO: Finish implementation via switch-case for various GET submit types.
        return true; //temporary return value
    }

    /**
     * @return bool
     */
    private function processPOST()
    {
        $this->scrubbed = array_map(array("Controller", "spamScrubber"), $_POST);
        switch ($this->scrubbed["requestType"]) {
            /**
             * Required POST variables for this case:
             *      requestType : "createAccount"
             *            fName : string
             *            lName : string
             *            email : string (email format)
             *         altEmail : string (email format)
             *    streetAddress : string
             *             city : string
             *         province : string (ISO code)
             *              zip : int 5 digits in length
             *            phone : int 9 digits in length
             *     gradSemester : "Winter", "Summer", or "Fall"
             *         gradYear : int 4 digits in length
             *         password : string
             */
            case "createAccount":
                $args = [
                    $this->scrubbed["fName"],
                    $this->scrubbed["lName"],
                    $this->scrubbed["email"],
                    $this->scrubbed["altEmail"],
                    $this->scrubbed["streetAddress"],
                    $this->scrubbed["city"],
                    $this->scrubbed["province"],
                    $this->scrubbed["zip"],
                    $this->scrubbed["phone"],
                    $this->scrubbed["gradSemester"],
                    $this->scrubbed["gradYear"],
                    $this->scrubbed["password"],
                    true
                ];
                call_user_func_array("Authenticator::register", $args);
                break;
            case "login":
                $args = [
                    $this->scrubbed["email"],
                    $this->scrubbed["password"]
                ];
                call_user_func_array("Authenticator::authenticate", $args);
                break;
            case "logout":
                Authenticator::logout();
                break;
        }
        return true; //temporary return value
    }

    /**
     * @return bool
     */
    private function setHomeDir()
    {
        $path = explode("/", dirname($_SERVER["SCRIPT_NAME"]));
        $homeDir = "";
        foreach ($path as $dir) {
            if ($dir != "metacognitio" and $dir != "") {
                $homeDir .= ".." . DIRECTORY_SEPARATOR;
            }
        }
        $this->homeDir = $homeDir;
        return true;
    }
}
