<?php
namespace samson\upload;

/**
 * Created by PhpStorm.
 * User: onysko
 * Date: 09.02.2015
 * Time: 14:37
 */

/**
 * Class ServerHandler
 * @package samson\upload
 */
class ServerHandler
{
    /**
     * Get file name from $_SERVER array
     * @return string Name of uploaded file
     */
    public function name()
    {
        return urldecode($_SERVER['HTTP_X_FILE_NAME']);
    }

    /**
     * Get file size from $_SERVER array
     * @return string Size of uploaded file
     */
    public function size()
    {
        return $_SERVER['HTTP_X_FILE_SIZE'];
    }

    /**
     * Get file type from $_SERVER array
     * @return string Mime type of uploaded file
     */
    public function type()
    {
        return $_SERVER['HTTP_X_FILE_TYPE'];
    }

    /**
     * Get file content from input socket
     * @return string File content
     */
    public function file()
    {
        return file_get_contents('php://input');
    }

    public function write($file, $fileName, $uploadDir)
    {
        /** @var $fs \samsonphp\fs\FileService Pointer to module controller */
        $fs = & m('fs');

        return $fs->write($file, $fileName, $uploadDir);
    }
}