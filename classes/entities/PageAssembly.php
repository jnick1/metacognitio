<?php
/**
 * Created by PhpStorm.
 * User: Jacob
 * Date: 10/22/2017
 * Time: 7:35 PM
 */

class PageAssembly
{
    const POPUP_DIR = Controller::MODULE_DIR . "pageassembly" . DIRECTORY_SEPARATOR;

    /**
     * String containing an absolute reference to the main file for a pageassembly.
     *
     * @var string
     */
    private $main;
    /**
     * An array of strings containing absolute file paths to the CSS files relevant to a pageassembly.
     *
     * @var string[]
     */
    private $CSS;
    /**
     * An array of string containing absolute file paths to the JavaScript files relevant to a pageassembly.
     *
     * @var string[]
     */
    private $javaScript;

    public function __construct(string $mainFolderName) {
        $path = explode("/", dirname($_SERVER["SCRIPT_NAME"]));
        $homeDir = "";
        foreach ($path as $dir) {
            if ($dir != "" and $dir != rtrim(AutoLoader::PROJECT_DIR, DIRECTORY_SEPARATOR)) {
                $homeDir .= ".." . DIRECTORY_SEPARATOR;
            }
        }
        $relativeDir = $homeDir . self::POPUP_DIR;

        $this->setMain($relativeDir . $mainFolderName . DIRECTORY_SEPARATOR . $mainFolderName . ".php");

        $this->CSS = [];
        //The two different directories exist because the Controller class automatically prepends a homeDir to the
        //included files. Since we use a homeDir here too, we don't want to double up, so there's two different dirs now
        $checkSafeCssDir = $relativeDir . $mainFolderName . DIRECTORY_SEPARATOR . "css" . DIRECTORY_SEPARATOR;
        $inclusionSafeCssDir = self::POPUP_DIR . $mainFolderName . DIRECTORY_SEPARATOR . "css" . DIRECTORY_SEPARATOR;
        $cssFiles = scandir($checkSafeCssDir);
        foreach($cssFiles as $cssFile) {
            //checks to see if the filename ends with ".css", and is at least 5 characters long (must be, in order to end with ".css")
            if(strlen($cssFile)>4 and strpos($cssFile, ".css", strlen($cssFile) - strlen(".css")) !== false) {
                $this->addCSS($inclusionSafeCssDir . $cssFile);
            }
        }

        $this->javaScript = [];
        //The two different directories exist because the Controller class automatically prepends a homeDir to the
        //included files. Since we use a homeDir here too, we don't want to double up, so there's two different dirs now
        $checkSafeJsDir = $relativeDir . $mainFolderName . DIRECTORY_SEPARATOR . "java" . DIRECTORY_SEPARATOR;
        $inclusionSafeJsDir = self::POPUP_DIR . $mainFolderName . DIRECTORY_SEPARATOR . "java" . DIRECTORY_SEPARATOR;
        $jsFiles = scandir($checkSafeJsDir);
        foreach($jsFiles as $jsFile) {
            //checks to see if the filename ends with ".js", and is at least 4 characters long (must be, in order to end with ".js")
            if(strlen($jsFile)>3 and strpos($jsFile, ".js", strlen($jsFile) - strlen(".js")) !== false) {
                $this->addJavaScript($inclusionSafeJsDir . $jsFile);
            }
        }
    }

    /**
     * String pointing to the main file for a pageassembly. Does start with a directory separator.
     *
     * @return string
     */
    public function getMain(): string
    {
        return $this->main;
    }

    /**
     * @param string $main
     */
    public function setMain(string $main): void
    {
        if ($filtered = filter_var($main, FILTER_SANITIZE_STRING)) {
            $dirPath = preg_split("/[\\/]/", $filtered);
            $filtered = implode(DIRECTORY_SEPARATOR, $dirPath);
            $this->main = $filtered;
        }

    }

    /**
     * @return string[]
     */
    public function getCSS(): array
    {
        return $this->CSS;
    }

    /**
     * @param string $CSS
     * @return bool|int
     */
    public function addCSS(string $CSS)
    {
        if ($filtered = filter_var($CSS, FILTER_SANITIZE_STRING)) {
            $dirPath = preg_split("/[\\/]/", $filtered);
            $filtered = implode(DIRECTORY_SEPARATOR, $dirPath);
            if (!in_array($filtered, $this->CSS)) {
                return array_push($this->CSS, $filtered);
            }
        }
        return false;
    }

    /**
     * @return string[]
     */
    public function getJavaScript(): array
    {
        return $this->javaScript;
    }

    /**
     * @param string $javaScript
     * @return bool|int
     */
    public function addJavaScript(string $javaScript)
    {
        if ($filtered = filter_var($javaScript, FILTER_SANITIZE_STRING)) {
            $dirPath = preg_split("/[\\/]/", $filtered);
            $filtered = implode(DIRECTORY_SEPARATOR, $dirPath);
            if (!in_array($filtered, $this->javaScript)) {
                return array_push($this->javaScript, $filtered);
            }
        }
        return false;
    }
}