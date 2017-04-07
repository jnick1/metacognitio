<?php
/**
 * Created by PhpStorm.
 * User: Jacob
 * Date: 4/4/2017
 * Time: 2:01 PM
 */

include "autoload.php";

$controller = new Controller("MetaCognitio");
$controller->initModuleDir();
$controller->addCSS($controller->getModuleDir() . "/css/index.min.css");
$controller->addCSS("java/lib/jquery-ui/jquery-ui.css");
$controller->addCSS("java/lib/jquery-dropdown/jquery.dropdown.min.css");
$controller->addJavaScript("java/lib/jquery/jQuery.min.js");
$controller->addJavaScript("java/lib/jquery-ui/jquery-ui.min.js");
$controller->addJavaScript("java/lib/jquery-dropdown/jquery.dropdown.min.js");

?>
<!DOCTYPE html>

<html>
    <head>
        <?php $controller->printHead(); ?>
    </head>
    <body>
        <div id="page" class="hfeed site">
            <?php include $controller->getHomeDir() . HEADER_FILE; ?>
            <div id="content" class="site-content">
                <article id="post-8" class="single-post post-8 page type-page status-publish hentry">
                    <div class="entry-content">
                        <h1 class="page-title"></h1>
                        <div align="center">
                            <div align="center">
                                <div align="left" style="width: 300px; border: solid 1px #333333;">
                                    <div style="background-color: #333333; color: #FFFFFF; padding: 3px;">
                                        <b>Login</b></div>
                                    <div style="margin: 30px">
                                        <form action="" method="post">
                                            <label style="font-size: large">Email</label><br/>
                                            <input class="tbox" name="username"
                                                   style="width: 238px; height: 25px; padding-left: 5px;"
                                                   type="text"/><br>
                                            <br/>
                                            <label style="font-size: large">Password</label><br/>
                                            <input class="tbox" name="password"
                                                   style="width: 238px; height: 25px; padding-left: 5px;"
                                                   type="password"/><br>
                                            <br/>
                                            <input style="width: 80px; height: 40px" type="submit"
                                                   value=" Login "/><br/>
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
            <? $controller->getHomeDir() . FOOTER_FILE; ?>
        </div>
    </body>
</html>
