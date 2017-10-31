<?php
/**
 * Created by PhpStorm.
 * User: Jacob
 * Date: 4/4/2017
 * Time: 2:01 PM
 */

include "autoload.php";

$controller = $_SESSION["controller"] = new Controller("MetaCognitio");

$controller->initModuleDir();
$controller->processREQUEST();
$controller->addCSS($controller->getModuleDir() . "css/index.min.css");
$controller->addCSS("resources/lib/jquery-ui/jquery-ui.min.css");
$controller->addCSS("resources/lib/jquery-dropdown/jquery.dropdown.min.css");
$controller->addJavaScript("resources/lib/jquery/jQuery.min.js");
$controller->addJavaScript("resources/lib/jquery-ui/jquery-ui.min.js");
$controller->addJavaScript("resources/lib/jquery-dropdown/jquery.dropdown.min.js");
$controller->setHeader(new PageAssembly("header"));
$controller->setFooter(new PageAssembly("footer"));
$controller->addPopup(new PageAssembly("login"));
?>
<!DOCTYPE html>

<html lang="en-US">
    <head>
        <?php $controller->printHead(); ?>
    </head>
    <body>
        <div id="page">
            <?php $controller->printHeader(); ?>
            <div id="content" class="site-content">

            </div>
            <?php $controller->printFooter(); ?>
        </div>
        <?php $controller->printPopups(); ?>
    </body>
</html>
