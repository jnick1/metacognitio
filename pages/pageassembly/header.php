<header class="site-header">
    <nav class="col-xs-12 col-sm-6 main-navigation no-padding" role="navigation">
        <div class="menu-main-menu-container">
            <ul id="menu-main-menu" class="menu">
                <li id="menu-item-15" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-20">
                    <a href="<?php echo $controller->getHomeDir(); ?>">Home</a></li>
                <li id="menu-item-20" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-20">
                    <a href="<?php echo $controller->getHomeDir() . "pages/dashboard/dashboard.php"; ?>">Dashboard</a>
                </li>
                <li id="menu-item-45"
                    class="menu-item menu-item-type-post_type menu-item-object-page current-menu-item page_item page-item-8 current_page_item menu-item-45">
                    <a href="<?php echo $controller->getHomeDir() . "pages/createaccount/createaccount.php"; ?>">Create
                        Account</a></li>
            </ul>
        </div>
        <?php
        if(Controller::isUserLoggedIn()) {
            ?>
            <div class="menu-main-menu-container">
                <form action="<?php echo $controller->getHomeDir(); ?>" method="post">
                    <input type="hidden" name="requestType" value="logout">
                    <input style="width: 80px; height: 40px" type="submit" value=" Logout ">
                </form>
            </div>
            <?php
        }
        ?>
    </nav>
    <div class="site-logo">
        <h1 class="site-title">
            <a href="<?php echo $controller->getHomeDir(); ?>">METACOGNITIO</a>
        </h1>
    </div>
</header>