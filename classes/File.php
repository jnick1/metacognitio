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
     * @var bool
     */
    private $active;
    /**
     * @var string bytes that make up the file stored as a string
     */
    private $contents;
    /**
     * @var string
     */
    private $name;
    /**
     * @var File
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
        //TODO: finish implementation of constructors for this class (should probably leave database connectivity to the FileMaster
    }

    /**
     * Originally written by mgutt, February 6, 2017
     * http://stackoverflow.com/a/42058764
     *
     * @param $filename
     * @return string
     */
    private static function beautify_filename(string $filename): string {
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
    private static function filter_filename($filename, $beautify=true): string {
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
     * @return string
     */
    public function getContents(): string
    {
        return $this->contents;
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
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->active;
    }

    /**
     * @param bool $active
     */
    public function setActive(bool $active)
    {
        $this->active = $active;
    }

    /**
     * @param string $contents
     */
    public function setContents(string $contents)
    {
        $this->contents = $contents;
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
    public function setParent(File $parent=null): bool
    {
        if ($parent instanceof File or $parent === null) {
            if($this != $parent) {
                $this->parent = $parent;
                return true;
            } else {
                throw new LogicException("File->setParent($parent) - Unable to set File as parent of itself");
            }
        } else {
            throw new InvalidArgumentException("File->setParent($parent) - Expected User|null: got " . (gettype($parent) == "object" ? get_class($parent) : gettype($parent)));
        }
    }
}