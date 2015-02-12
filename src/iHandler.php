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
     * @return string Name of uploaded file
     */
    public function name($name = null);

    /**
     * Get file size
     * @return integer Size of uploaded file
     */
    public function size($name = null);

    /**
     * Get file type
     * @return string Mime type of uploaded file
     */
    public function type($name = null);

    /**
     * Get file content
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
