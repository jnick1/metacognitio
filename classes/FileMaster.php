<?php

define("UPLOAD_DIR", $_SERVER["DOCUMENT_ROOT"] . "/" . AutoLoader::PROJECT_DIR . "files/");
define("DOWNLOAD_DIR", $_SERVER["DOCUMENT_ROOT"] . "/" . AutoLoader::PROJECT_DIR . "/downloads/");

/**
 * Created by PhpStorm.
 * User: Jacob
 * Date: 4/14/2017
 * Time: 9:11 PM
 */
class FileMaster
{
    const ALLOWED_DOC_MIME_TYPES = [
        "application/vnd.openxmlformats-officedocument.wordprocessingml.document",
        "application/msword",
        "application/vnd.oasis.opendocument.text",
        "text/rtf",
        "text/plain"
    ];
    const ALLOWED_IMG_MIME_TYPES = [
        "image/jpeg",
        "image/png",
        "image/tiff"
    ];
    const ALLOWED_LICENSE_MIME_TYPES = [
        "application/pdf"
    ];
    const IGNORE_DIRECTORIES = 2;
    const IGNORE_EMPTY_DIRECTORIES = 1;

    /**
     * Deletes all files in the DOWNLOAD_DIR directory which have existed past their designated timeout period.
     *
     * @return bool
     * @throws Exception
     */
    public static function clearDownloads(): bool
    {
        $files = self::getDirFlatContents(DOWNLOAD_DIR);

        foreach ($files as $file) {
            //Fixed timeout used for now until need arises for more user account customization/this to be added to a
            //file object.
            if (time() - filectime($file) > 60) {
                $result = unlink($file);
                if (!$result) {
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * Allows a user to download a file by redirecting the user to the download directory.
     *
     * @param File $file
     */
    public static function downloadFile(File $file): void
    {
        if ($file->isInDatabase()) {
            $file->updateFromDatabase();
            file_put_contents(DOWNLOAD_DIR . $file->getName(), $file->getContents());
        } else {
            file_put_contents(DOWNLOAD_DIR . $file->getName(), $file->getContents());
        }
        header("Location: " . DOWNLOAD_DIR . "");
        exit();
    }

    //TODO: determine if getDirContents and getFileContents are actually necessary

    /**
     * This function recursively searches through a given directory and returns a
     * multi-dimensional array of directories and filenames, which stores returned
     * values under the following scheme:
     *
     *      filenames:      # => "path/to/file.ext"
     *      directores:     "path/to/directory" => array(filenames/directories)
     *
     * This function takes 3 arguments (the fourth is used during recursion, but
     * not when the function is initially called by you). They are described below:
     *
     * 1. dir specifies the path to begin recursively searching.
     *
     * 2. ignoreArray specifies an array of strings. If any path contains one of
     *      the strings in the ignoreArray, that path, and all children paths will
     *      be ignored by the search.
     *
     * 3. options allows for additional settings to be considered during the search.
     *      The only options currently supported are the following:
     *
     *      "flag" => "IGNORE_EMPTY_DIRECTORIES"
     *          if set, this indicates to the function that empty directories should
     *          not be reported in the results.
     *
     * 4. results stores the results from a previous call to the function, and thus
     *      permits the recursive behavior of the function.
     *
     * @param string $dir
     * @param string[] $ignoreArray
     * @param int[] $options
     * @param string[] $results
     * @return string[]|bool
     */
    public static function getDirContents(string $dir, array $ignoreArray = [], array $options = [], array &$results = [])
    {
        if (is_dir($dir)) {
            $files = array_diff(scandir($dir), ['..', '.']);

            foreach ($files as $key => $value) {
                $flag = false;
                $path = realpath($dir . DIRECTORY_SEPARATOR . $value);
                foreach ($ignoreArray as $ignore) {
                    if (strpos($path, $ignore)) {
                        $flag = true;
                        break;
                    }
                }
                if ($flag) {
                    continue;
                } else {
                    /* set to skip with 2 entries because there will always be "." and "..", which aren't actually in the
                     * directory.
                     */
                    if (is_dir($path)) {
                        if (in_array(self::IGNORE_EMPTY_DIRECTORIES, $options) and count(scandir($path)) === 2) {
                            continue;
                        } else if ($value == "." or $value == "..") {
                            continue;
                        } else {
                            self::getDirContents($path, $ignoreArray, $options, $results[$path]);
                            $results[] = $path;
                        }
                    } else {
                        $results[] = $path;
                    }
                }
            }
            return $results;
        } else {
            return false;
        }
    }

    /**
     * Examines the specified directory and returns a flat array listing all files and directories within the specified
     * directory. This examination is conducted recursively.
     * Returns an array on success, false on failure.
     *
     * @param string $dir
     * @param string[] $ignoreArray
     * @param int[] $options
     * @param string[] $results
     * @return string[]|bool
     */
    public static function getDirFlatContents(string $dir, array $ignoreArray = [], array $options = [], array &$results = [])
    {
        if (is_dir($dir)) {
            $files = array_diff(scandir($dir), ['..', '.']);

            foreach ($files as $key => $value) {
                $flag = false;
                $path = realpath($dir . DIRECTORY_SEPARATOR . $value);
                foreach ($ignoreArray as $ignore) {
                    if (strpos($path, $ignore)) {
                        $flag = true;
                        break;
                    }
                }
                if ($flag) {
                    continue;
                } else {
                    /* set to skip with 2 entries because there will always be "." and "..", which aren't actually in the
                     * directory.
                     */
                    if (is_dir($path)) {
                        if (in_array(self::IGNORE_EMPTY_DIRECTORIES, $options) and count(scandir($path)) === 2) {
                            continue;
                        } else {
                            self::getDirFlatContents($path, $ignoreArray, $options, $results);
                            $results[] = $path;
                        }
                    } else {
                        $results[] = $path;
                    }
                }
            }
            if (in_array(self::IGNORE_DIRECTORIES, $options)) {
                foreach ($results as $key => $result) {
                    if (is_dir($result)) {
                        unset($results[$key]);
                    }
                }
            }
            return array_values($results);
        } else {
            return false;
        }
    }

    /**
     * Uploads a file from the $_FILES super-global array and returns the saved file object.
     * The value of $varName should be the file's key in the $_FILES array (i.e. the value of the "name" attribute in
     * the HTML "input" tag).
     * Ex: <input type="file" name="$varName">
     *
     * @param string $varName
     * @return File
     */
    public static function uploadFromFILES(string $varName): File
    {
        $file = new File($_FILES[$varName]["name"], "");
        move_uploaded_file($_FILES[$varName]["tmp_name"], File::UPLOAD_DIR . $file->getInternalName());
        $file->fetchContents();
        $file->updateToDatabase();
        return $file;
    }

    /**
     * Uploads a file that has been manually created, meaning that the contents of the File object are stored in the
     * file, but not elsewhere in the site.
     *
     * @param File $file
     * @return bool
     */
    public static function uploadFromManualObject(File $file): bool
    {
        if ($file->isActive() and !$file->isInDatabase()) {
            file_put_contents(UPLOAD_DIR . $file->getInternalName(), $file->getContents());
            $file->updateToDatabase();
            return true;
        } else {
            return false;
        }
    }
}