<?php
/**
 * Created by PhpStorm.
 * User: Jacob
 * Date: 4/4/2017
 * Time: 2:01 PM
 */

include "../../autoload.php";

$controller = new Controller("My Dashboard");
$controller->initModuleDir();
$controller->processREQUEST();
$controller->addCSS($controller->getModuleDir() . "/css/dashboard.min.css");
$controller->addCSS("java/lib/jquery-ui/jquery-ui.css");
$controller->addCSS("java/lib/jquery-dropdown/jquery.dropdown.min.css");
$controller->addJavaScript("java/lib/jquery/jQuery.min.js");
$controller->addJavaScript("java/lib/jquery-ui/jquery-ui.min.js");
$controller->addJavaScript("java/lib/jquery-dropdown/jquery.dropdown.min.js");

?>
<!DOCTYPE html>
<html lang="en-US">
    <head>
        <?php $controller->printHead(); ?>
    </head>
    <body>
        <div id="page" class="hfeed site">
            <?php include $controller->getHomeDir() . HEADER_FILE; ?>
            <div id="content" class="site-content">
                <h2> Welcome to your Dashboard! </h2>
            </div>
            <?php include $controller->getHomeDir() . FOOTER_FILE; ?>
        </div>
    </body>
</html>
