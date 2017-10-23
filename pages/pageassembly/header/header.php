<header>
    <nav id="site-nav-global">
        <div>
            <ul>
                <?php if(Controller::isUserLoggedIn()) { ?>
                    <li>
                        <a href="<?php echo $controller->getHomeDir() . "pages/dashboard/dashboard.php"; ?>">Dashboard</a>
                    </li>
                    <li>
                        <a href="<?php echo $controller->getHomeDir() . "pages/mgnt_account/accountpreferences.php"; ?>">My Account</a>
                    </li>
                <?php } if($controller->userHasAccess([new Permission(Permission::PERMISSION_AUTHOR)])) { ?>
                    <li>
                        <a href="<?php echo $controller->getHomeDir() . "pages/mgnt_submission/newsubmission.php"; ?>">New Submission</a>
                    </li>
                <?php } if(!Controller::isUserLoggedIn()) { ?>
                    <li>
                        <a href="<?php echo $controller->getHomeDir() . "pages/createaccount/createaccount.php"; ?>">Create Account</a>
                    </li>
                <?php } ?>
            </ul>
        </div>
        <?php
        if (Controller::isUserLoggedIn()) {
            ?>
            <div class="navButton">
                <form action="<?php echo $controller->getHomeDir(); ?>" method="post">
                    <input type="hidden" name="requestType" value="logout">
                    <input type="submit" value="Logout">
                </form>
            </div>
            <?php
        }
        ?>
    </nav>
    <div id="header-title">
        <h1>
            <a href="<?php echo $controller->getHomeDir(); ?>">METACOGNITIO</a>
        </h1>
    </div>
    <?php //global site nav links for navigation regardless of the currently logged in user; i.e. permission levels
          //should not change what gets shown ?>

    <nav id="site-nav-local">

    </nav>
</header>