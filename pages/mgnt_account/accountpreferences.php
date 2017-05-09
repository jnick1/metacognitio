<?php

include "../../autoload.php";

$controller = new Controller("My Account");
$controller->initModuleDir();
$controller->processREQUEST();
$controller->addCSS($controller->getModuleDir() . "/css/accountpreferences.min.css");
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
            <?php include $controller->getHomeDir() . Controller::HEADER_FILE; ?>
            <div id="content" class="site-content" style="text-align:center;">

                <!--For the following texts boxes, populate the fields with user information
                from the database, when "save" is pressed, then overwrite the preferences in
                the database
                -->
                <article id="post-8" class="single-post post-8 page type-page status-publish hentry">
                    <div class="entry-content">
                        <h1 class="page-title"></h1>
                        <div align="center">
                            <div align="center">
                                <div align="left" style="width: 300px; border: solid 1px #333333;">
                                    <div style="background-color: #333333; color: #FFFFFF; padding: 3px;">
                                        <b>HCCCS</b>
                                        <b>Account Preferences</b></div>
                                    <div style="margin: 30px">
                                        <form action="" method="post">
                                            <hr>
                                            <label style="font-size: large">Personal Information</label><br>
                                            <hr>
                                            <p style="padding: 10px"></p>
                                            <label style="font-size: small">First Name:</label><br>
                                            <input class="tbox" name="firstname" style="width: 238px; height: 25px; padding-left: 5px;" type="text" value="<?php echo Controller::getLoggedInUser()->getFirstName(); ?>">
                                            <label style="font-size: small">Last Name:</label><br>
                                            <input class="tbox" name="lastname" style="width: 238px; height: 25px; padding-left: 5px;" type="text" value="<?php echo Controller::getLoggedInUser()->getLastName(); ?>">
                                            <label style="font-size: small">Email:</label><br>
                                            <input class="tbox" name="email" style="width: 238px; height: 25px; padding-left: 5px;" type="text" value="<?php echo Controller::getLoggedInUser()->getEmail(); ?>">
                                            <label style="font-size: small">Alternative Email:</label><br>
                                            <input class="tbox" name="altemail" style="width: 238px; height: 25px; padding-left: 5px;" type="text" value="<?php echo Controller::getLoggedInUser()->getAltEmail(); ?>">
                                            <label style="font-size: small">Phone Number:</label><br>
                                            <input class="tbox" name="name" style="width: 238px; height: 25px; padding-left: 5px;" type="text" value="<?php echo Controller::getLoggedInUser()->getPhone(); ?>">
                                            <label style="font-size: small">Street Address:</label><br>
                                            <input class="tbox" name="name" style="width: 238px; height: 25px; padding-left: 5px;" type="text" value="<?php echo Controller::getLoggedInUser()->getStreetAddress(); ?>">
                                            <label style="font-size: small">City:</label>
                                            <label style="font-size: small; padding-left: 100px;">State:</label>
                                            <label style="font-size: small; padding-left: 20px;">Zip:</label><br>
                                            <input class="tbox" name="city" style="width: 120px; height: 25px; padding-left: 5px;" type="text" value="<?php echo Controller::getLoggedInUser()->getCity(); ?>">
                                            <input class="tbox" name="state" style="width: 40px; height: 25px;" type="text" value="<?php echo Controller::getLoggedInUser()->getProvince(User::MODE_ISO_SHORT); ?>">
                                            <input class="tbox" name="zip" style="width: 61px; height: 25px; " type="text" value="<?php echo Controller::getLoggedInUser()->getPostalCode(); ?>">
                                            <label style="font-size: small">Excpected Graduation</label><br>
                                            <label style="font-size: small">Semester:</label>
                                            <label style="font-size: small; padding-left: 60px;">Year:</label><br>
                                            <input class="tbox" name="semester" style="width: 110px; height: 25px; padding-left: 5px;" type="text" value="<?php echo Controller::getLoggedInUser()->getGradSemester(); ?>">
                                            <input class="tbox" name="year" style="width: 110px; height: 25px; padding-left: 5px;" type="text" value="<?php echo Controller::getLoggedInUser()->getGradYear(); ?>">
                                            <p style="padding: 20px"></p>
<!--                                            <hr>-->
<!--                                            <label style="font-size: large">Change Password</label><br>-->
<!--                                            <hr>-->
<!--                                            <p style="padding: 10px"></p>-->
<!--                                            <label style="font-size: small">Current Password:</label><br>-->
<!--                                            <input class="tbox" name="oldpassword" style="width: 238px; height: 25px; padding-left: 5px;" type="password">-->
<!--                                            <label style="font-size: small">New Password:</label><br>-->
<!--                                            <input class="tbox" name="password" style="width: 238px; height: 25px; padding-left: 5px;" type="password">-->
<!--                                            <label style="font-size: small">Confirm New Password:</label><br>-->
<!--                                            <input class="tbox" name="confirm" style="width: 238px; height: 25px; padding-left: 5px;" type="password">-->
<!--                                            <p style="padding: 20px"></p>-->
<!--                                            <hr>-->
<!--                                            <label style="font-size: large">Permissions</label><br>-->
<!--                                            <hr>-->
<!--                                            <p style="padding: 10px"></p>-->
<!--                                            <label style="font-size: small">[current highest permission]</label><br>-->
<!--                                            <label style="font-size: small">[description]</label><br>-->
<!--                                            <br><br>-->
<!--                                            <input style="width: 120px; height: 40px; font-size: small" type="submit" value=" Save "><br>-->
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
            <?php include $controller->getHomeDir() . Controller::FOOTER_FILE; ?>
        </div>
    </body>
</html>
