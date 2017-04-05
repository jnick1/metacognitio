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
$css = [
    $controller->getModuleDir()."/css/dashboard.min.css",
    "java/lib/jquery-ui/jquery-ui.css",
    "java/lib/jquery-dropdown/jquery.dropdown.min.css"
];
$controller->setCSS($css);
$java = [
    "java/lib/jquery/jQuery.min.js",
    "java/lib/jquery-ui/jquery-ui.min.js",
    "java/lib/jquery-dropdown/jquery.dropdown.min.js",
];
$controller->setJavaScript($java);
unset($java);
unset($css);

?>
<!DOCTYPE html>
<html lang="en-US">
    <head>
        <?php $controller->printHead(); ?>
    </head>
    <body>
        <div id="page" class="hfeed site">
            <?php include $controller->getHomeDir().HEADER_FILE; ?>
            <div id="content" class="site-content">
                <h2> Welcome to your Dashboard! </h2>
            </div>
        </div>
    </body>
</html>
