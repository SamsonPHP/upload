<?php
namespace samsonphp\upload;

/**
 * Generic file uploader
 * @package samsonphp\upload
 * @author Vitaly Iegorov <egorov@samsonos.com>
 * @author Nikita Kotenko <kotenko@samsonos.com>
 */
class Upload
{
    /** @var string|boolean real file path */
    private $filePath;

    /** @var string Name of uploaded file */
    private $realName;

    /** @var string Generated file name */
    private $fileName;

    /** @var string File MIME type */
    private $mimeType;

    /** @var string extension */
    private $extension;

    /** @var int File size */
    private $size;

    /** Supported file extensions */
    protected $extensions = array();

    /** @var array Parameters for callable handlers */
    protected $relPathParameters = array();

    /** @var bool Method of uploading */
    protected $async = true;

    /** @var UploadController Pointer to module controller */
    public $config;

    /** Upload server path */
    public $uploadDir = 'upload/';

    /** @var iHandler Handler for processing file option requests */
    public $handler;

    /**
     * Init module main fields
     * @param array $extensions Allowed file types
     * @param null $relPathParameters Parameters for callback functions
     */
    protected function initParams($extensions = array(), $relPathParameters = null)
    {
        // Set additional relative path parameters
        $this->relPathParameters = !is_array($relPathParameters) ? array($relPathParameters) : $relPathParameters;

        // Set file extension limitations, form array if isn't an array
        $this->extensions = is_array($extensions) ? $extensions : array($extensions);
    }

    /**
     * Set properties of current file upload
     * @param string $filePath
     * @param string $uploadName
     * @param string $fileName
     * @param string $postName
     */
    protected function setUploadProperties(& $filePath = '', & $uploadName = '', & $fileName = '', $postName = '')
    {
        /** @var string $file Read uploaded file */
        $file = $this->handler->file($postName);

        // Create file
        $this->filePath = $this->handler->write($file, $this->fileName, $this->uploadDir);

        // Save size and mimeType
        $this->size = $this->handler->size($postName);
        $this->mimeType = $this->handler->type($postName);

        // Set function parameters
        $filePath = $this->fullPath();
        $uploadName = $this->name();
        $fileName = $this->realName();
    }

    /**
     * Try to create unique file name using external callback handler
     */
    protected function setName()
    {
        // If we have callable handler for generating file name
        if (isset($this->config->fileNameHandler) && is_callable($this->config->fileNameHandler)) {
            // Add file extension as last parameter
            array_push($this->relPathParameters, $this->extension);

            // Call handler and create fileName
            $this->fileName = call_user_func_array($this->config->fileNameHandler, $this->relPathParameters);
        } else { // If we have not created filename - generic generate it
            $this->fileName = strtolower(md5(time().$this->realName).'.'.$this->extension);
        }
    }

    /**
     * Make file uploading
     * @param string $filePath
     * @param string $uploadName
     * @param string $fileName
     * @param string $postName
     * @return bool Upload status
     */
    protected function createUpload(& $filePath = '', & $uploadName = '', & $fileName = '', $postName = '')
    {
        // Get file extension
        $this->extension = pathinfo($this->realName, PATHINFO_EXTENSION);

        // If we have no extension limitations or they are matched
        if (!sizeof($this->extensions) || in_array($this->extension, $this->extensions)) {
            // Try to set file name using external handler
            $this->setName();

            // Set function parameters
            $this->setUploadProperties($filePath, $uploadName, $fileName, $postName);

            // Success
            return true;
        }

        // Failed
        return false;
    }

    /**
     * Asynchronous uploading method
     * @param string $filePath
     * @param string $uploadName
     * @param string $fileName
     * @return bool
     */
    protected function asyncUploading(& $filePath = '', & $uploadName = '', & $fileName = '')
    {
        // Try to get upload file with new upload method
        $this->realName = $this->handler->name();

        // If upload data exists
        if (isset($this->realName) && $this->realName != '') {
            // Try to create upload
            return $this->createUpload($filePath, $uploadName, $fileName);
        }

        // Failed
        return false;
    }

    /**
     * Synchronous uploading method
     * @param string $filePath
     * @param string $uploadName
     * @param string $fileName
     * @return bool
     */
    protected function syncUploading(& $filePath = '', & $uploadName = '', & $fileName = '')
    {
        foreach ($_FILES as $postName => $postArray) {
            // Try to get upload file with new upload method
            $this->realName = $this->handler->name($postName);

            // Return false if something went wrong
            if (!$this->createUpload($filePath, $uploadName, $fileName, $postName)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Constructor
     * @param mixed $extensions Collection or single excepted extension
     * @param mixed $relPathParameters Data to be passed to external rel. path builder
     * @param mixed $config External configuration class
     */
    public function __construct($extensions = array(), $relPathParameters = null, $config = null, $handler = null)
    {
        // Init main parameters of current object
        $this->initParams($extensions, $relPathParameters);

        // Get current upload adapter
        $this->config = !isset($config) ? m('upload') : $config;

        // Build relative path for uploading
        $this->uploadDir = call_user_func_array($this->config->uploadDirHandler, $this->relPathParameters);

        $this->handler = isset($handler) ? $handler : new AsyncHandler();
    }

    /**
     * Perform file uploading logic
     * @param string $filePath Uploaded file path
     * @param string $uploadName Uploaded file name real name to return on success upload
     * @param string $fileName Uploaded file name on server to return on success upload
     * @return boolean True if file successfully uploaded
     */
    public function upload(& $filePath = '', & $uploadName = '', & $fileName = '')
    {
        return $this->async ?
            $this->asyncUploading($filePath, $uploadName, $fileName) :
            $this->syncUploading($filePath, $uploadName, $fileName);

    }

    public function async($async = true)
    {
        $this->async = $async;
        return $this;
    }

    /** @return string Full path to file  */
    public function path()
    {
        return $this->config->pathPrefix.$this->filePath;
    }

    /** @return string Full path to file with file name */
    public function fullPath()
    {
        return $this->config->pathPrefix.$this->filePath.$this->fileName;
    }

    /**
     * Returns uploaded file name
     * @return string File name
     */
    public function realName()
    {
        return $this->realName;
    }

    /**
     * Returns stored file name
     * @return string File name
     */
    public function name()
    {
        return $this->fileName;
    }

    /**
     * Returns MIME type of uploaded file
     * @return string MIME type
     */
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

    /**
     * Returns file size
     * @return int File size
     */
    public function size()
    {
        return $this->size;
    }
}
