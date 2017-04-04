<?php
/**
 * Created by PhpStorm.
 * User: Jacob
 * Date: 4/4/2017
 * Time: 2:01 PM
 */

include "autoload.php";

$controller = new Controller("MetaCognitio");
$css = [
    "/styles.css",
    "/java/lib/jquery-ui/jquery-ui.css",
    "/java/lib/jquery-dropdown/jquery.dropdown.min.css"
];
$controller->setCSS($css);
$java = [
    "/java/lib/jquery/jQuery.min.js",
    "/java/lib/jquery-ui/jquery-ui.min.js",
    "/java/lib/jquery-dropdown/jquery.dropdown.min.js",
];
$controller->setJavaScript($java);
unset($java);
unset($css);

?>
<!DOCTYPE html>

<html>
    <head>
    <?php $controller->printHead(); ?>
    </head>
    <body>
        <div id="page" class="hfeed site">
            <header class="site-header">
                <nav class="col-xs-12 col-sm-6 main-navigation no-padding" role="navigation">
                    <div class="menu-main-menu-container">
                        <ul id="menu-main-menu" class="menu">
                            <li id="menu-item-20" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-20">
                                <a href="">Dashboard</a></li>
                            <li id="menu-item-45" class="menu-item menu-item-type-post_type menu-item-object-page current-menu-item page_item page-item-8 current_page_item menu-item-45">
                                <a href="">Create Account</a></li>
                        </ul>
                    </div>
                </nav>
                <div class="site-logo">
                    <h1 class="site-title">
                        <a href="<?php echo $controller->getHomeDir(); ?>">METACOGNITIO</a>
                    </h1>
                </div>
            </header>
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
                                            <label style="font-size: large">Email</label><br />
                                            <input class="tbox" name="username" style="width: 238px; height: 25px; padding-left: 5px;" type="text" /><br>
                                            <br />
                                            <label style="font-size: large">Password</label><br />
                                            <input class="tbox" name="password" style="width: 238px; height: 25px; padding-left: 5px;" type="password" /><br>
                                            <br />
                                            <input style="width: 80px; height: 40px" type="submit" value=" Login " /><br />
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
        </div>
    </body>
</html>
