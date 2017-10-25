<header>
    <div id="header-title">
        <h1>
            <a href="<?php echo $controller->getHomeDir(); ?>">METACOGNITIO</a>
        </h1>
    </div>
    <?php //global site nav links for navigation regardless of the currently logged in user; i.e. permission levels
    //should not change what gets shown ?>
    <nav id="site-nav-global">
        <ul>
            <?php if (Controller::isUserLoggedIn()) { ?>
                <li class="colorswap-button">
                    <a href="<?php echo $controller->getHomeDir() . "pages/dashboard/dashboard.php"; ?>">Dashboard</a>
                </li>
                <li class="colorswap-button">
                    <a href="<?php echo $controller->getHomeDir() . "pages/mgnt_account/accountpreferences.php"; ?>">My
                        Account</a>
                </li>
            <?php }
            if ($controller->userHasAccess([new Permission(Permission::PERMISSION_AUTHOR)])) { ?>
                <li class="colorswap-button">
                    <a href="<?php echo $controller->getHomeDir() . "pages/mgnt_submission/newsubmission.php"; ?>">New
                        Submission</a>
                </li>
            <?php }
            if (!Controller::isUserLoggedIn()) { ?>
                <li class="colorswap-button">
                    <input type="button" id="showLogin" class="minimalist-button" value="Login">
                </li>
                <li class="colorswap-button">
                    <a href="<?php echo $controller->getHomeDir() . "pages/createaccount/createaccount.php"; ?>">Create
                        Account</a>
                </li>
            <?php } else { ?>
                <li class="colorswap-button">
                    <form action="<?php echo $controller->getHomeDir(); ?>" method="post">
                        <input type="hidden" name="requestType" value="logout">
                        <input type="submit" class="minimalist-button" value="Logout">
                    </form>
                </li>
            <?php } ?>
        </ul>
    </nav>
    <?php
    if (Controller::isUserLoggedIn()) { ?>
        <nav id="site-nav-local">

        </nav>
    <?php } ?>
</header>