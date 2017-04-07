<?php

define("MODULE_DIR", "pages");
define("HEADER_FILE", "pages/pageassembly/header.php");
define("FOOTER_FILE", "/pages/pageassembly/footer.php");

/*
 *
 */

class Controller
{

    /**
     * @var array file paths from site root to css needed by the page
     */
    private $CSS;
    /**
     * @var Dbc Page-wide connection to the database
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
        $this->dbc = new Dbc();
        $this->tabIncrement = 1;
        $this->pageTitle = $pageTitle;
        $this->setHomeDir();
    }

    /**
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
     * @param string $CSS
     * @return int
     */
    public function addCSS(string $CSS)
    {
        if ($filtered = filter_var($CSS, FILTER_SANITIZE_STRING)) {
            if (!in_array($filtered, $this->CSS)) {
                return array_push($this->CSS, $filtered);
            }
        }
    }

    /**
     * @param string $javaScript
     * @return int
     */
    public function addJavaScript(string $javaScript)
    {
        if ($filtered = filter_var($javaScript, FILTER_SANITIZE_STRING)) {
            if (!in_array($filtered, $this->javaScript)) {
                return array_push($this->javaScript, $filtered);
            }
        }
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
     * @return bool
     */
    public function initModuleDir()
    {
        $stack = debug_backtrace();
        $pathToCaller = $stack[0]['file'];
        if (stripos($pathToCaller, MODULE_DIR)) {
            $pathArr = explode(DIRECTORY_SEPARATOR, $pathToCaller);
            $nextDir = array_search(MODULE_DIR, $pathArr) + 1;
            $this->moduleDir = MODULE_DIR . "/" . $pathArr[$nextDir] . "/";
            return true;
        }
        return false;
    }

    /**
     *
     */
    public function printCSS()
    {
        if (isset($this->CSS)) {
            foreach ($this->CSS as $CSS) {
                echo "<link rel='stylesheet' type='text/css' href='" . $this->homeDir . $CSS . "'>";
            }
        }
    }

    /**
     *
     */
    public function printFavicon()
    {
        if (isset($this->favicon)) {
            echo "<link rel='icon' type='" . mime_content_type($this->favicon) . "' href='" . $this->getHomeDir() . $this->favicon . "' sizes='128x128'>";
        }
    }

    /**
     *
     */
    public function printHead()
    {
        echo "<meta charset='UTF-8'>";
        $this->printFavicon();
        $this->printCSS();
        $this->printJavaScript();
        $this->printPageTitle();
    }

    /**
     *
     */
    public function printJavaScript()
    {
        if (isset($this->javaScript)) {
            foreach ($this->javaScript as $javaScript) {
                echo "<script type='text/javascript' src='" . $this->homeDir . $javaScript . "'></script>";
            }
        }
    }

    /**
     *
     */
    public function printPageTitle()
    {
        if (isset($this->pageTitle)) {
            echo "<title>" . $this->getPageTitle() . "</title>";
        }
    }

    /**
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
            case "createAccount":
                //TODO: Finish implementation via Authenticator class, and also HTML POST return values
                break;
            case "login":
                //TODO: Finish implementation via Authenticator class, and also HTML POST return values
                break;
            case "logout":
                //TODO: Finish implementation via Authenticator class, and also HTML POST return values
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
