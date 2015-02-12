<?php
namespace samsonphp\upload;

/**
 * Created by PhpStorm.
 * User: onysko
 * Date: 11.02.2015
 * Time: 16:55
 */

interface iHandler
{
    /**
     * Get file name
     * @param null $name Name of post file (for using $_FILES array)
     * @return string Name of uploaded file
     */
    public function name($name = null);

    /**
     * Get file size
     * @param null $name Name of post file (for using $_FILES array)
     * @return integer Size of uploaded file
     */
    public function size($name = null);

    /**
     * Get file type
     * @param null $name Name of post file (for using $_FILES array)
     * @return string Mime type of uploaded file
     */
    public function type($name = null);

    /**
     * Get file content
     * @param null $name Name of post file (for using $_FILES array)
     * @return string File content
     */
    public function file($name = null);

    /**
     * Write file in servers file system
     * @param $file mixed File content
     * @param $fileName string File name
     * @param $uploadDir string Catalog for uploading on server
     * @return bool|string Path to file or false if some errors found
     */
    public function write($file, $fileName, $uploadDir);
}
