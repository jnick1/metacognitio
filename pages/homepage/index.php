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
$controller->PageAssembler()->addCSS($controller->getModuleDir() . "css/index.min.css");
$controller->PageAssembler()->addCSS("resources/lib/jquery-ui/jquery-ui.min.css");
$controller->PageAssembler()->addCSS("resources/lib/jquery-dropdown/jquery.dropdown.min.css");
$controller->PageAssembler()->addJavaScript("resources/lib/jquery/jQuery.min.js");
$controller->PageAssembler()->addJavaScript("resources/lib/jquery-ui/jquery-ui.min.js");
$controller->PageAssembler()->addJavaScript("resources/lib/jquery-dropdown/jquery.dropdown.min.js");
$controller->PageAssembler()->setHeader(new PageAssembly("header"));
$controller->PageAssembler()->setFooter(new PageAssembly("footer"));
$controller->PageAssembler()->addPopup(new PageAssembly("login"));
$controller->PageAssembler()->addPopup(new PageAssembly("register"));
?>
<!DOCTYPE html>

<html lang="en-US">
    <head>
        <?php $controller->PageAssembler()->printHead(); ?>
    </head>
    <body>
        <div id="page">
            <?php $controller->PageAssembler()->printHeader(); ?>
            <div id="content" class="site-content">

            </div>
            <?php $controller->PageAssembler()->printFooter(); ?>
        </div>
        <?php $controller->PageAssembler()->printPopups(); ?>
    </body>
</html>
