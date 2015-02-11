<?php
namespace samsonphp\upload;

use samson\core\CompressableExternalModule;

/**
 * SamsonPHP Upload module
 *
 * @package SamsonPHP
 * @author Vitaly Iegorov <egorov@samsonos.com>
 */
class UploadController extends CompressableExternalModule
{
    /** @var string Module identifier */
    public $id = 'upload';

    /** @var  callable External handler to build relative file path */
    public $uploadDirHandler;

    /** @var string Prefix for image path saving in db */
    public $pathPrefix = __SAMSON_BASE__;

    /** @var callable External handler to build file name */
    public $fileNameHandler;

    /** @var \samsonphp\fs\FileService Pointer to file system module */
    public $fs;

    /** @var ServerHandler Server functions handler */
    public $serverHandler;

    /**
     * Init current file system module and server requests handler
     */
    protected function initFields()
    {
        /** @var \samsonphp\fs\FileService $fs */
        $fs = !isset($this->fs) ? m('fs') : $this->fs;

        // Store pointer to file system module
        $this->fs = $fs;

        // Set server handler object
        $this->serverHandler = !isset($this->serverHandler) ? new ServerHandler() : $this->serverHandler;
    }

    /**
     * Initialize module
     * @param array $params Collection of module parameters
     * @return bool True if module successfully initialized
     */
    public function init(array $params = array())
    {
        // Init FileSystem and ServerHandler
        $this->initFields();

        // If no valid handlers are passed - use generic handlers
        if (!isset($this->uploadDirHandler) || !is_callable($this->uploadDirHandler)) {
            $this->uploadDirHandler = array($this, 'defaultDirHandler');
        }

        // Call parent initialization
        parent::init($params);
    }

    /**
     * Default relative path builder handler
     * @return string Relative path for uploading
     */
    public function defaultDirHandler()
    {
        // Default file path
        $path = 'upload';

        // Create upload dir if it does not present
        if (!$this->fs->exists($path)) {
            $this->fs->mkDir($path);
        }

        return $path;
    }

    /**
     * Returns a file size limit in bytes based on the PHP upload_max_filesize and post_max_size
     * @return float Size value
     */
    public function __async_maxfilesize()
    {
        $maxSize = -1;
        $sizeString = '';

        if ($maxSize < 0) {
            $sizeString = ini_get('post_max_size');
            // Start with post_max_size.
            $maxSize = $this->parseSize($sizeString);

            // If upload_max_size is less, then reduce. Except if upload_max_size is
            // zero, which indicates no limit.
            $uploadMax = $this->parseSize(ini_get('upload_max_filesize'));
            if ($uploadMax > 0 && $uploadMax < $maxSize) {
                $maxSize = $uploadMax;
                $sizeString = ini_get('upload_max_filesize');
            }
        }
        return array('status' => true, 'maxSize' => $maxSize, 'sizeString' => $sizeString);
    }

    /**
     * Function to get size in bytes
     * @param string $size File size string
     * @return float Size number
     */
    private function parseSize($size)
    {
        // Remove the non-unit characters from the size.
        $unit = preg_replace('/[^bkmgtpezy]/i', '', $size);
        // Remove the non-numeric characters from the size.
        $size = preg_replace('/[^0-9\.]/', '', $size);
        if ($unit) {
            // Find the position of the unit in the ordered
            // string which is the power of magnitude to multiply a kilobyte by.
            return round($size * pow(1024, stripos('bkmgtpezy', $unit[0])));
        } else {
            return round($size);
        }
    }
}
