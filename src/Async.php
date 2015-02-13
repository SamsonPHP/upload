<?php
/**
 * Created by PhpStorm.
 * User: onysko
 * Date: 13.02.2015
 * Time: 16:00
 */

namespace samsonphp\upload;


class Async implements AdapterInterface
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
     * @return File[] Array of uploaded files
     */
    public function upload()
    {
        // Try to get upload file with new upload method
        $this->realName = $this->handler->name();

        // If upload data exists
        if (isset($this->realName) && $this->realName != '') {
            // Try to create upload
            return $this->createUpload();
        }

        // Failed
        return false;
    }
}
