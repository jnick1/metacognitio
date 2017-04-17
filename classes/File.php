<?php

/**
 * Created by PhpStorm.
 * User: Jacob
 * Date: 4/14/2017
 * Time: 9:58 PM
 */
class File
{

    private const DEFAULT_PATH = "C:/wamp64/www/metacognitio/files";

    /**
     * Bytes that make up the file stored as a string.
     *
     * @var string
     */
    private $contents;
    /**
     * Stores the internally-used, hashed, filename of the file.
     *
     * @var string
     */
    private $internalName;
    /**
     * Allow read and write access to file data? (bool)
     *
     * @var bool
     */
    private $isActive;
    /**
     * Indicates if the file is stored in the database
     *
     * @var bool
     */
    private $isInDatabase;
    /**
     * Stores the externally-used, human-readable, filename of the file.
     *
     * @var string
     */
    private $name;
    /**
     * The parent file of this file.
     *
     * @var File|null
     */
    private $parent;

    public function __construct()
    {
        //This segment of code originally written by rayro@gmx.de
        //http://php.net/manual/en/language.oop5.decon.php
        $a = func_get_args();
        $i = func_num_args();
        if (method_exists($this, $f = '__construct' . $i)) {
            call_user_func_array(array($this, $f), $a);
        }
    }

    /**
     * Originally written by mgutt, February 6, 2017
     * http://stackoverflow.com/a/42058764
     *
     * @param $filename
     * @return string
     */
    private static function beautify_filename(string $filename): string
    {
        // reduce consecutive characters
        $filename = preg_replace(array(
            // "file   name.zip" becomes "file-name.zip"
            '/ +/',
            // "file___name.zip" becomes "file-name.zip"
            '/_+/',
            // "file---name.zip" becomes "file-name.zip"
            '/-+/'
        ), '-', $filename);
        $filename = preg_replace(array(
            // "file--.--.-.--name.zip" becomes "file.name.zip"
            '/-*\.-*/',
            // "file...name..zip" becomes "file.name.zip"
            '/\.{2,}/'
        ), '.', $filename);
        // lowercase for windows/unix interoperability http://support.microsoft.com/kb/100625
        $filename = mb_strtolower($filename, mb_detect_encoding($filename));
        // ".file-name.-" becomes "file-name"
        $filename = trim($filename, '.-');
        return $filename;
    }

    /**
     * Originally written by mgutt, February 6, 2017
     * http://stackoverflow.com/a/42058764
     *
     * @param $filename
     * @param bool $beautify
     * @return mixed|string
     */
    private static function filter_filename($filename, $beautify = true): string
    {
        // sanitize filename
        $filename = preg_replace(
            '~
        [<>:"/\\|?*]|            # file system reserved https://en.wikipedia.org/wiki/Filename#Reserved_characters_and_words
        [\x00-\x1F]|             # control characters http://msdn.microsoft.com/en-us/library/windows/desktop/aa365247%28v=vs.85%29.aspx
        [\x7F\xA0\xAD]|          # non-printing characters DEL, NO-BREAK SPACE, SOFT HYPHEN
        [#\[\]@!$&\'()+,;=]|     # URI reserved https://tools.ietf.org/html/rfc3986#section-2.2
        [{}^\~`]                 # URL unsafe characters https://www.ietf.org/rfc/rfc1738.txt
        ~x',
            '-', $filename);
        // avoids ".", ".." or ".hiddenFiles"
        $filename = ltrim($filename, '.-');
        // optional beautification
        if ($beautify) $filename = File::beautify_filename($filename);
        // maximise filename length to 255 bytes http://serverfault.com/a/9548/44086
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        $filename = mb_strcut(pathinfo($filename, PATHINFO_FILENAME), 0, 255 - ($ext ? strlen($ext) + 1 : 0), mb_detect_encoding($filename)) . ($ext ? '.' . $ext : '');
        return $filename;
    }

    /**
     * Constructor for retrieving existing files from the database.
     *
     * @param string $internalName
     */
    public function __construct1(string $internalName)
    {
        $dbc = new DatabaseConnection();
        $params = ["s", $internalName];
        $file = $dbc->query("select", "SELECT * FROM `file` WHERE `pkFilename`=?", $params);

        if ($file) {
            $this->internalName = $file["pkFilename"];
            $this->fetchContents();
            $this->setName($file["nmTitle"]);
            $this->setIsActive($file["isActive"]);
            $this->isInDatabase = true;
            if (isset($file["fkFilename"])) {
                $this->setParent(new File($file["fkFilename"]));
            }
        } else {
            throw new InvalidArgumentException("File->__construct1($internalName) - Unable to select from database");
        }
    }

    /**
     * Constructor for new files added by user/via interface. Leave $parent as null as desired.
     *
     * @param string $name
     * @param string $contents
     * @param File|null $parent
     */
    public function __construct3(string $name, string $contents, File $parent)
    {
        $this->setName($name);
        $this->setIsActive(true);
        $this->newInternalName();
        $this->isInDatabase = false;
        $this->setParent($parent);
        $this->setContents($contents);
    }

    /**
     * @return bool
     */
    public function fetchContents(): bool
    {
        if ($this->isActive()) {
            $this->setContents(file_get_contents(self::DEFAULT_PATH . $this->getInternalName()));
            return true;
        } else {
            return false;
        }
    }

    /**
     * @return string|bool
     */
    public function getContents()
    {
        if ($this->isActive()) {
            return $this->contents;
        } else {
            return false;
        }

    }

    /**
     * @return string
     */
    public function getInternalName(): string
    {
        return $this->internalName;
    }

    /**
     * @return string
     */
    public function getMimeType(): string
    {
        return mime_content_type($this->getName());
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return File
     */
    public function getParent(): File
    {
        return $this->parent;
    }

    /**
     * @return File
     */
    public function getRootParent(): File
    {
        $file = $this;
        while ($file->getParent() !== null) {
            $file = $file->getParent();
        }
        return $file;
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->isActive;
    }

    /**
     * @return bool
     */
    public function isInDatabase(): bool
    {
        return $this->isInDatabase;
    }

    /**
     * @return bool
     */
    public function newInternalName(): bool
    {
        if (!$this->isInDatabase()) {
            $this->internalName = Hasher::randomHash() . substr(strrchr($this->getName(), "."), 1);
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param string $contents
     */
    public function saveContents(): void
    {
        file_put_contents(self::DEFAULT_PATH . $this->getInternalName(), $this->getContents());
    }

    /**
     * @param string $contents
     */
    public function setContents(string $contents): void
    {
        $this->contents = $contents;
    }

    /**
     * @param bool $isActive
     */
    public function setIsActive(bool $isActive): void
    {
        $this->isActive = $isActive;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): bool
    {
        $this->name = File::filter_filename($name);
    }

    /**
     * @param File $parent
     * @return bool
     * @throws LogicException|InvalidArgumentException
     */
    public function setParent(File $parent = null): bool
    {
        if ($this != $parent) {
            $this->parent = $parent;
            return true;
        } else {
            throw new LogicException("File->setParent($parent) - Unable to set File as parent of itself");
        }
    }

    /**
     * @return bool
     * @throws LogicException
     */
    public function updateFromDatabase(): bool
    {
        if (!$this->isInDatabase()) {
            throw new LogicException("File->updateFromDatabase() - Unable to pull from database when File instance is not stored in database");
        } else {
            $this->__construct1($this->getInternalName());
            return true;
        }
    }

    /**
     * Push updated local content to the database
     * @return bool
     */
    public function updateToDatabase(): bool
    {
        $dbc = new DatabaseConnection();
        if($this->isInDatabase()) {
            if($this->getParent() !== null) {
                $parent = $this->getParent()->getInternalName();
            } else {
                $parent = null;
            }
            $params = ["ssis", $parent, $this->getName(), $this->isActive(), $this->getInternalName()];
            $result = $dbc->query("update", "UPDATE `file` SET `fkFilename`=?, `nmTitle`=?, `isActive`=? WHERE `pkFilename` = ?", $params);
            $this->saveContents();
            return (bool) $result;
        } else {
            if($this->getParent() !== null) {
                $parent = $this->getParent()->getInternalName();
            } else {
                $parent = null;
            }
            $params = ["sssi", $this->getInternalName(), $parent, $this->getName(), $this->isActive()];
            $result = $dbc->query("insert", "INSERT INTO `file`(`pkFilename`, `fkFilename`, `nmTitle`, `isActive`) VALUES (?,?,?,?)", $params);
            $this->saveContents();
            return (bool) $result;
        }
    }
}