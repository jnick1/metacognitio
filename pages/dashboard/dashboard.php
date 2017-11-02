<?php
/**
 * Created by PhpStorm.
 * User: Jacob
 * Date: 4/4/2017
 * Time: 2:01 PM
 */

include "../../autoload.php";

$_SESSION["controller"] = $controller = new Controller("My Dashboard");

$controller->initModuleDir();
$controller->processREQUEST();
$controller->PageAssembler()->setHeader(new PageAssembly("header"));
$controller->PageAssembler()->setFooter(new PageAssembly("footer"));
$controller->PageAssembler()->addCSS($controller->getModuleDir() . "/css/dashboard.min.css");
$controller->PageAssembler()->addCSS("resources/lib/jquery-ui/jquery-ui.css");
$controller->PageAssembler()->addCSS("resources/lib/jquery-dropdown/jquery.dropdown.min.css");
$controller->PageAssembler()->addJavaScript("resources/lib/jquery/jQuery.min.js");
$controller->PageAssembler()->addJavaScript("resources/lib/jquery-ui/jquery-ui.min.js");
$controller->PageAssembler()->addJavaScript("resources/lib/jquery-dropdown/jquery.dropdown.min.js");

?>
<!DOCTYPE html>
<html lang="en-US">
    <head>
        <?php $controller->PageAssembler()->printHead(); ?>
    </head>
    <body>
        <div id="page" class="hfeed site">
            <?php $controller->PageAssembler()->printHeader(); ?>
            <div id="content" class="site-content">
                <h2> Welcome to your Dashboard! </h2>
                <?php if ($controller->userHasAccess([new Permission(Permission::PERMISSION_EXECUTIVE)])) { ?>
                    <div style="width:800px; margin:0 auto;">
                        <p style="font-size: large">Emily Spunaugle</p>
                        <p style="font-size: small">Library Liaison for Honors College</p>
                        <p style="font-size: small">
                            Kresge Library acts as a key distribution point for the organization, as HC Librarian Emily
                            Spunaugle has agreed to act as our point of contact within the library to put digital copies
                            of the journal online, and to also hold hard copies of the journal for student use.
                        </p>
                        <p style="font-size: small">Email: spunaugle@oakland.edu</p>
                    </div>
                <?php } ?>
            </div>
            <?php $controller->PageAssembler()->printFooter(); ?>
        </div>
    </body>
</html>
