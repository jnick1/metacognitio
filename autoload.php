<?php
/**
 * Created by PhpStorm.
 * User: Jacob
 * Date: 4/4/2017
 * Time: 2:18 PM
 */

define("CLASSES_DIR",$_SERVER["DOCUMENT_ROOT"]."/metacognitio/classes/");

class AutoLoader {
    protected static $paths = array(
        CLASSES_DIR,
    );
    public static function addPath($path) {
        $path = realpath($path);
        if ($path) {
            self::$paths[] = $path;
        }
    }
    public static function load($class) {
        $classPath = $class.".php"; // Do whatever logic here
        foreach (self::$paths as $path) {
            if (is_file($path . $classPath)) {
                require_once $path . $classPath;
                return;
            }
        }
    }
}

spl_autoload_register(array("AutoLoader", "load"));

//set_include_path(get_include_path().PATH_SEPARATOR."classes/");
//
//// You can use this trick to make autoloader look for commonly used "My.class.php" type filenames
//spl_autoload_extensions('.php');
//
//// Use default autoload implementation
//spl_autoload_register();