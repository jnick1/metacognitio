<?php
/**
 * Created by PhpStorm.
 * User: Jacob
 * Date: 4/4/2017
 * Time: 2:01 PM
 */

include "../../autoload.php";

$_SESSION["controller"] = $controller = new Controller("Create Account");

$controller->initModuleDir();
$controller->processREQUEST();
$controller->setHeader(new PageAssembly("header"));
$controller->setFooter(new PageAssembly("footer"));
$controller->addCSS($controller->getModuleDir() . "css/createaccount.min.css");
$controller->addCSS("resources/lib/jquery-ui/jquery-ui.css");
$controller->addCSS("resources/lib/jquery-dropdown/jquery.dropdown.min.css");
$controller->addJavaScript("resources/lib/jquery/jQuery.min.js");
$controller->addJavaScript("resources/lib/jquery-ui/jquery-ui.min.js");
$controller->addJavaScript("resources/lib/jquery-dropdown/jquery.dropdown.min.js");

?>
<!DOCTYPE html>
<html lang="en-US">
    <head>
        <?php $controller->printHead(); ?>
    </head>
    <body>
        <div id="page" class="hfeed site">
            <?php $controller->printHeader(); ?>
            <div id="content" class="site-content">
                <article id="post-8" class="single-post post-8 page type-page status-publish hentry">
                    <div class="entry-content">
                        <h1 class="page-title"></h1>
                        <div align="center">
                            <div align="center">
                                <div align="left" style="width: 300px; border: solid 1px #333333;">
                                    <div style="background-color: #333333; color: #FFFFFF; padding: 3px;">
                                        <b>HCCCS</b>
                                        <b>Create Account</b></div>
                                    <div style="margin: 30px">
                                        <form action="" method="post">
                                            <label style="font-size: small">
                                                First Name:
                                                <input class="tbox" name="fName" style="width: 238px; height: 25px; padding-left: 5px;" type="text">
                                            </label>
                                            <label style="font-size: small">
                                                Last Name:
                                                <input class="tbox" name="lName" style="width: 238px; height: 25px; padding-left: 5px;" type="text">
                                            </label>
                                            <label style="font-size: small">
                                                Email:
                                                <input class="tbox" name="email" style="width: 238px; height: 25px; padding-left: 5px;" type="email">
                                            </label>
                                            <label style="font-size: small">
                                                Alternate Email:
                                                <input class="tbox" name="altEmail" style="width: 238px; height: 25px; padding-left: 5px;" type="email">
                                            </label>
                                            <label style="font-size: small">
                                                Phone Number:
                                                <input class="tbox" name="phone" style="width: 238px; height: 25px; padding-left: 5px;" type="text">
                                            </label>
                                            <label style="font-size: small">
                                                Street Address:
                                                <input class="tbox" name="streetAddress" style="width: 238px; height: 25px; padding-left: 5px;" type="text">
                                            </label>
                                            <label style="font-size: small">City:</label>
                                            <label style="font-size: small; padding-left: 100px;">State:</label>
                                            <label style="font-size: small; padding-left: 20px;">Zip:</label><br>
                                            <input class="tbox" name="city" style="width: 120px; height: 25px; padding-left: 5px;" type="text">
                                            <input class="tbox" name="province" style="width: 40px; height: 25px;" type="text">
                                            <input class="tbox" name="zip" style="width: 60px; height: 25px; " type="text">
                                            <label style="font-size: small">
                                                <span style="display: block"> Excpected Graduation </span>
                                                <label style="font-size: small">Semester:</label>
                                                <label style="font-size: small; padding-left: 60px;">Year:</label><br>
                                                <input class="tbox" name="gradSemester" style="width: 110px; height: 25px; padding-left: 5px;" type="text">
                                                <input class="tbox" name="gradYear" style="width: 110px; height: 25px; padding-left: 5px;" type="text">
                                            </label>
                                            <label style="font-size: small">
                                                Password:
                                                <input class="tbox" name="password" style="width: 238px; height: 25px; padding-left: 5px;" type="password">
                                            </label>
                                            <label style="font-size: small">
                                                Confirm Password:
                                                <input class="tbox" name="confirmPassword" style="width: 238px; height: 25px; padding-left: 5px;" type="password">
                                            </label>
                                            <input type="hidden" name="requestType" value="createAccount">
                                            <input style="width: 120px; height: 40px; font-size: small; margin-top: 20px;" type="submit" value=" Create Account "><br>
                                        </form>
                                        <div style="font-size: 11px; color: #cc0000; margin-top: 10px">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </article>
            </div>
            <?php $controller->printFooter(); ?>
        </div>
    </body>
</html>
