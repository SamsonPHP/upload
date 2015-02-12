<?php
namespace samsonphp\upload;

/**
 * Created by PhpStorm.
 * User: onysko
 * Date: 09.02.2015
 * Time: 14:37
 */

/**
 * Class ServerHandler
 * @package samsonphp\upload
 */
class AsyncHandler implements iHandler
{
    /** @var $fs \samsonphp\fs\FileService Pointer to module controller */
    public $fs;

    /**
     * Server Handler constructor
     * @param null $fs FileSystem module
     */
    public function __construct($fs = null)
    {
        $this->fs = isset($fs) ? $fs : m('fs');
    }

    /**
     * Get file name from $_SERVER array
     * @param null $name Name of post file (for using $_FILES array)
     * @return string Name of uploaded file
     */
    public function name($name = null)
    {
        return urldecode($_SERVER['HTTP_X_FILE_NAME']);
    }

    /**
     * Get file size from $_SERVER array
     * @param null $name Name of post file (for using $_FILES array)
     * @return integer Size of uploaded file
     */
    public function size($name = null)
    {
        return $_SERVER['HTTP_X_FILE_SIZE'];
    }

    /**
     * Get file type from $_SERVER array
     * @param null $name Name of post file (for using $_FILES array)
     * @return string Mime type of uploaded file
     */
    public function type($name = null)
    {
        return $_SERVER['HTTP_X_FILE_TYPE'];
    }

    /**
     * Get file content from php input socket
     * @param null $name Name of post file (for using $_FILES array)
     * @return string File content
     */
    public function file($name = null)
    {
        return file_get_contents('php://input');
    }

    /**
     * Write file in servers file system
     * @param $file mixed File content
     * @param $fileName string File name
     * @param $uploadDir string Catalog for uploading on server
     * @return bool|string Path to file or false if some errors found
     */
    public function write($file, $fileName, $uploadDir)
    {
        return $this->fs->write($file, $fileName, $uploadDir);
    }
}
