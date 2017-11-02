<?php
/**
 * Created by PhpStorm.
 * User: Jacob
 * Date: 11/2/2017
 * Time: 2:18 PM
 */

class PageAssembler
{
    /**
     * An array of stings containing relative file paths (starting without a directory separator) that point towards CSS
     * files. Each Controller instance's $CSS variable should hold the paths to all css files required for use in the
     * page that each Controller manages.
     *
     * @var string[]
     */
    private $CSS;
    /**
     * String containing the relative file path to the image file which stores the favicon for the site.
     *
     * @var string
     */
    private $favicon;
    /**
     * PageAssembly referring to the footer for a particular page.
     *
     * @var PageAssembly
     */
    private $footer;
    /**
     * PageAssembly referring to the header for a particular page.
     *
     * @var PageAssembly
     */
    private $header;
    /**
     * An array of stings containing relative file paths (starting without a directory separator) that point towards JS
     * files. Each Controller instance's $javaScript variable should hold the paths to all JS files required for use in
     * the page that each Controller manages.
     *
     * @var string[]
     */
    private $javaScript;
    /**
     * String containing the title of the current page as indicated in tab titles in web browsers.
     *
     * @var string
     */
    private $pageTitle;
    /**
     * An array of PageAssemblies that indicate which PageAssembly files should be included at the end of a page, to
     * await interaction with the page to reveal themselves. This array is intended for use by Popups.
     *
     * @var PageAssembly[]
     */
    private $popups;
    /**
     * homedir passed down from the creating controller class.
     *
     * @var string
     */
    private $homeDir;

    public function __construct(string $pageTitle, string $homeDir)
    {
        $this->CSS = [];
        $this->javaScript = [];
        $this->popups = [];
        $this->pageTitle = $pageTitle;
        $this->homeDir = $homeDir;
    }

    /**
     * Push new CSS into the local CSS specification
     *
     * @param string $CSS
     * @return int|bool
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
     * Push new javascript into the local js.js specification
     *
     * @param string $javaScript
     * @return int|bool
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

    /**
     * Push a new PageAssembly into the local pageassembly inclusion list
     *
     * @param PageAssembly $popup
     */
    public function addPopup(PageAssembly $popup)
    {
        if (!in_array($popup, $this->popups, true)) {
            array_push($this->popups, $popup);
            foreach ($popup->getCSS() as $CSS) {
                $this->addCSS($CSS);
            }
            foreach ($popup->getJavaScript() as $javaScript) {
                $this->addJavaScript($javaScript);
            }
        }
    }

    /**
     * @return string
     */
    public function getPageTitle(): string
    {
        return $this->pageTitle;
    }

    /**
     * Prints (via inclusion) the HTML for the page's footer
     */
    public function printFooter(): void
    {
        if (isset($this->footer)) {
            //See long explanation of this $controller var in the printPopups function
            @$controller = $_SESSION["controller"];
            include $this->footer->getMain();
        }
    }

    /**
     * Prints javascript, favicon, CSS, and page title
     */
    public function printHead(): void
    {
        /**
         * The four sections of this function used to be separate, but their only instance of use was to be called in
         * this function, so they were eliminated and recombined here to reduce overcrowding of functions in the
         * Controller class (originally).
         */
        echo "<meta charset='UTF-8'>";
        /**
         * Print out the Favicon for the site (the little image in the browser tab)
         */
        if (isset($this->favicon)) {
            echo "<link rel='icon' type='" . mime_content_type($this->favicon) . "' href='" . $this->homeDir . $this->favicon . "' sizes='128x128'>";
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
     * Prints (via inclusion) the HTML for the page's header
     */
    public function printHeader(): void
    {
        if (isset($this->header)) {
            //See long explanation of this $controller var in the printPopups function
            @$controller = $_SESSION["controller"];
            include $this->header->getMain();
        }
    }

    /**
     * Prints (via inclusion) the HTML for each popup included in a page
     */
    public function printPopups(): void
    {
        if (isset($this->popups)) {
            /*
             * The controller is set here but not used so that it's accessible to each of the popups.
             * It's a bit complicated why this is needed, but it essentially boils down to the following reasons:
             * 1. In order to remove, "include" statements from individual pages, and leave the work to the
             *    controller, they were moved into this function (and printHeader, and printFooter).
             * 2. Moving the include statements into the controller means that the code in those files is
             *    included INTO THE CONTROLLER's CLASS FILE when the particular function is called.
             * 3. Because of this, the $controller variable, which is set in whichever page the controller was
             *    created in, is inaccessible to the various included files that are included IN THE CONTROLLER CLASS
             * Thus, in each page, we just set $controller = $_SESSION["controller"] = ... so that the controller
             * instance for that page is saved in the PHP session. Now if an included page references the $controller
             * for the page it was intended to be included in, it can access it via the session. In keeping with
             * the DRY (Don't Repeat Yourself) Principle, the line of code that associates the $controller variable
             * with the session variable is included here, since that then permits every included page to have access
             * to it.
             *
             * Note: because of the isolation between pages, if a specific page requires a specific variable from it's
             * caller, then it will likely need to be passed via the session manually between the two pages, otherwise
             * this main inclusion function will get cluttered with individual vars.
             */
            @$controller = $_SESSION["controller"];
            /**
             * @var $popup PageAssembly
             */
            foreach ($this->popups as $popup) {
                echo "<div id='controller-popup-" . $popup->getName() . "' style='display: none'>";
                include $popup->getMain();
                echo "</div>";
            }
        }
    }

    /**
     * @param string $favicon
     * @return bool
     */
    public function setFavicon(string $favicon): bool
    {
        if ($filtered = filter_var($favicon, FILTER_SANITIZE_STRING)) {
            $this->favicon = $filtered;
            return true;
        }
        return false;
    }

    /**
     * Sets the footer for a page and automatically adds the footer's required JS and CSS files to the current page.
     * If a footer is set when another has already been set for the page, the JS and CSS files for the original footer
     * will be removed from the to-be-loaded JS & CSS, and the new footer's JS & CSS will be added instead.
     *
     * Note: if one of the original footer's JS or CSS files is needed after the change, it will have to be added back
     * in manually.
     *
     * @param PageAssembly $footer
     */
    public function setFooter(PageAssembly $footer)
    {
        if (isset($this->footer)) {
            $this->CSS = array_diff($this->CSS, $this->footer->getCSS());
            $this->javaScript = array_diff($this->javaScript, $this->footer->getJavaScript());
        }
        $this->footer = $footer;
        foreach ($footer->getCSS() as $CSS) {
            $this->addCSS($CSS);
        }
        foreach ($footer->getJavaScript() as $javaScript) {
            $this->addJavaScript($javaScript);
        }
    }

    /**
     * Sets the header for a page and automatically adds the header's required JS and CSS files to the current page.
     * If a header is set when another has already been set for the page, the JS and CSS files for the original header
     * will be removed from the to-be-loaded JS & CSS, and the new header's JS & CSS will be added instead.
     *
     * Note: if one of the original header's JS or CSS files is needed after the change, it will have to be added back
     * in manually.
     *
     * @param PageAssembly $header
     */
    public function setHeader(PageAssembly $header)
    {
        if (isset($this->header)) {
            $this->CSS = array_diff($this->CSS, $this->header->getCSS());
            $this->javaScript = array_diff($this->javaScript, $this->header->getJavaScript());
        }
        $this->header = $header;
        foreach ($header->getCSS() as $CSS) {
            $this->addCSS($CSS);
        }
        foreach ($header->getJavaScript() as $javaScript) {
            $this->addJavaScript($javaScript);
        }
    }

    /**
     * @param string $pageTitle
     * @return bool
     */
    public function setPageTitle(string $pageTitle): bool
    {
        if ($filtered = filter_var($pageTitle, FILTER_SANITIZE_STRING)) {
            $this->pageTitle = $filtered;
            return true;
        }
        return false;
    }
}