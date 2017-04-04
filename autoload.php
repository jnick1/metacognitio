<?php
/**
 * Created by PhpStorm.
 * User: Jacob
 * Date: 4/4/2017
 * Time: 2:18 PM
 */

set_include_path(get_include_path().PATH_SEPARATOR."classes/");

// You can use this trick to make autoloader look for commonly used "My.class.php" type filenames
spl_autoload_extensions('.php');

// Use default autoload implementation
spl_autoload_register();