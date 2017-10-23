<?php

include "../../autoload.php";

$_SESSION["controller"] = $controller = new Controller("New Submission");

$controller->initModuleDir();
$controller->processREQUEST();
$controller->setHeader(new PageAssembly("header"));
$controller->setFooter(new PageAssembly("footer"));
$controller->addCSS($controller->getModuleDir() . "/css/newsubmission.min.css");
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
                                    <div style="background-color: #333333; color: #FFFFFF; padding: 3px; text-align: center;">
                                        <b>New Submission</b></div>
                                    <div style="margin: 30px">
                                        <form action="<?php echo $controller->getHomeDir(); ?>" method="post" enctype="multipart/form-data">
                                            <label style="font-size: small">
                                                Title:
                                                <input class="tbox" name="title" style="width: 238px; height: 25px; padding-left: 5px;" type="text">
                                            </label>
                                            <label style="font-size: small">
                                                Additional Authors:
                                                <input class="tbox" name="additionalAuthors" style="width: 238px; height: 25px; padding-left: 5px;" type="text">
                                            </label>
                                            <label style="font-size: small">
                                                Publication:
                                                <input class="tbox" name="publication" style="width: 238px; height: 25px; padding-left: 5px;" type="text">
                                            </label>
                                            <label style="font-size: small">
                                                Form:
                                                <input class="tbox" name="form" style="width: 238px; height: 25px; padding-left: 5px;" type="text">
                                            </label>
                                            <label style="font-size: small">
                                                Page Count:<br>
                                                <input class="tbox" name="pageCount" style="width: 40px; height: 25px; padding-left: 5px;" type="number"><br>
                                            </label>
                                            <label style="font-size: small">
                                                Upload:
                                                <input type="file" name="file" class="tbox">
                                            </label>
                                            <br>
                                            <!--<div style="width: 238px; height: 70px; padding 10px; border: 1px solid #aaaaaa;" ondrop="drop(event)" ondragover="allowDrop(event)"></div>-->
                                            <br>
                                            <input type="hidden" name="requestType" value="submit">
                                            <input style="width: 100px; height: 40px; font-size: small" type="submit" value=" Save ">
                                            <input style="width: 100px; height: 40px; font-size: small" type="button" value=" Cancel ">
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
