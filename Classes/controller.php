<?php
/*
 *
 */
class Controller
{

    /**
     * @var string
     */
    private $homeDir;
    /**
     * @var int
     */
    private $tabIncrement;
    /**
     * @var array
     */
    private $scrubbed;
    /**
     * @var array file paths from site root to css needed by the page
     */
    private $CSS;
    /**
     * @var array file paths from site root to javascript needed by the page
     */
    private $javaScript;
    /**
     * @var string
     */
    private $pageTitle;
    /**
     * @var string
     */
    private $favicon;
    /**
     * @var Dbc Page-wide connection to the database
     */
    private $dbc;

    /**
     * @param $value
     * @return string
     */
    private static function spamScrubber($value) {
        // This function is useful for preventing spam in form results.  Should be used on all $_POST arrays.
        // To Use:  $scrubbed=array_map('spam_scrubber',ARRAY_NAME);  Where ARRAY_NAME might be equal to an array such as $_POST
        // Then refer to the item in the array as $scrubbed['item_name']

        // List of very bad values:

        $very_bad=array('to:', 'cc:', 'bcc:', 'content-type:', 'mime-version:', 'multipart-mixed:', 'content-transfer-encoding:');
        // IF any of the very bad strings are in the submitted value, return an empty string:
        foreach ($very_bad as $v) {
            if (stripos($value, $v) !== false) {
                return '';
            }
        }
        // Replace any newline characters with spaces:
        //strip_tags() will remove all HTML and PHP tags. Safe, but remove if HTML formatting required.
        $value =strip_tags(str_replace(array( "\r", "\n","%0a", "%0d"), ' ', $value));
        return trim($value);
    }

    /**
     * Controller constructor.
     */
    public function __construct($pageTitle)
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        $this->scrubbed = array_map(array($this, "spamScrubber"), $_POST);
        $this->homedir = dirname($_SERVER["SCRIPT_NAME"]);
        $this->dbc = new Dbc();
        $this->tabIncrement = 1;
        $this->pageTitle = $pageTitle;
    }

    /**
     * @return int
     */
    public function getTabIncrement()
    {
        return $this->tabIncrement;
    }

    /**
     * @param $tabIncrement
     * @return bool
     */
    public function setTabIncrement($tabIncrement)
    {
        if($filtered = filter_var($tabIncrement,FILTER_VALIDATE_INT)) {
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
     * @return mixed
     */
    public function getPageTitle()
    {
        return $this->pageTitle;
    }

    /**
     * @param $pageTitle
     * @return bool
     */
    public function setPageTitle($pageTitle)
    {
        if($filtered = filter_var($pageTitle, FILTER_SANITIZE_STRING)) {
            $this->pageTitle = $filtered;
            return true;
        }
        return false;
    }

    /**
     *
     */
    public function printPageTitle()
    {
        if(isset($this->pageTitle)) {
            echo "<title>".$this->getPageTitle()."</title>";
        }
    }

    /**
     * @return mixed
     */
    public function getHomeDir()
    {
        return $this->homeDir;
    }

    /**
     * @param $CSS
     * @return bool
     */
    public function setCSS($CSS)
    {
        if(is_array($CSS)) {
            $this->CSS = $CSS;
            return true;
        }
        return false;
    }

    /**
     *
     */
    public function printCSS()
    {
        if(isset($this->CSS)) {
            foreach($this->CSS as $CSS) {
                echo "<link rel='stylesheet' type='text/css' href='".$this->homedir.$CSS."'>";
            }
        }
    }

    /**
     * @param $javaScript
     * @return bool
     */
    public function setJavaScript($javaScript)
    {
        if(is_array($javaScript)) {
            $this->javaScript = $javaScript;
            return true;
        }
        return false;
    }

    /**
     *
     */
    public function printJavaScript()
    {
        if(isset($this->javaScript)) {
            foreach($this->javaScript as $javaScript) {
                echo "<script type='text/javascript' src='".$this->homedir.$javaScript."'></script>";
            }
        }
    }

    /**
     * @param $favicon
     * @return bool
     */
    public function setFavicon($favicon)
    {
        if($filtered = filter_var($favicon, FILTER_SANITIZE_STRING)) {
            $this->favicon = $filtered;
            return true;
        }
        return false;
    }

    /**
     *
     */
    public function printFavicon()
    {
        if (isset($this->favicon)) {
            echo "<link rel='icon' type='".mime_content_type($this->favicon)."' href='".$this->getHomeDir().$this->favicon."' sizes='128x128'>";
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
}