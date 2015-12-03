<?php
/**
 * Created by PhpStorm.
 * User: onysko
 * Date: 13.02.2015
 * Time: 15:46
 */

namespace samsonphp\upload;


class File
{
    /** @var string|boolean real file path */
    private $dir;

    /** @var string Original uploaded file name */
    private $original;

    /** @var string Generated file name */
    private $name;

    /** @var string File MIME type */
    private $mimeType;

    /** @var string extension */
    private $extension;

    /** @var int File size */
    private $size;

    /** @return string Get file directory without file name  */
    public function dir()
    {
        return $this->dir;
    }

    /** @return string Full path to file with file name */
    public function path()
    {
        return $this->dir.$this->name;
    }

    /** @return string Uploaded file name */
    public function original()
    {
        return $this->original;
    }

    /** @return string Uploaded new file name */
    public function name()
    {
        return $this->name;
    }

    /** @return string MIME type */
    public function mimeType()
    {
        return $this->mimeType;
    }

    /**
     * If $extension is set, tries to compare file extension to input extension and return a result
     * Otherwise returns file extension
     * @param string $extension Supposed file extension
     * @return bool|string Result of extension comparison or extension by itself.
     */
    public function extension($extension = null)
    {
        return isset($extension) ? ($extension === $this->extension ? true : false) : $this->extension;
    }

    /** @return int File size */
    public function size()
    {
        return $this->size;
    }
}
