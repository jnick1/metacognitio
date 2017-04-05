<?php
/**
 * Created by PhpStorm.
 * User: Jacob
 * Date: 4/4/2017
 * Time: 2:18 PM
 */

define("CLASSES_DIR",$_SERVER["DOCUMENT_ROOT"]."/metacognitio/classes/");

/**
 * Originally written by ircmaxell (2010/07/21)
 * http://stackoverflow.com/a/3300138
 *
 * Modified for project use.
 *
 * Class AutoLoader
 */
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
