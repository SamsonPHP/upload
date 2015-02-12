<?php
namespace samsonphp\upload;
use SebastianBergmann\Comparator\MockObjectComparator;

/**
 * Created by Vitaly Iegorov <egorov@samsonos.com>
 * on 04.08.14 at 16:42
 */
class MainTest extends \PHPUnit_Framework_TestCase
{
    /** @var \samsonphp\upload\UploadController */
    public $instance;

    protected function setHandlerParams(& $handler)
    {
        $handler
            ->expects($this->once())
            ->method('name')
            ->willReturn('tests/samsonos.png');

        $handler
            ->expects($this->once())
            ->method('size')
            ->willReturn('1003');

        $handler
            ->expects($this->once())
            ->method('file')
            ->willReturn(file_get_contents('tests/samsonos.png'));

        $handler
            ->expects($this->once())
            ->method('type')
            ->willReturn('png');
    }

    public function fileNameHandler($name)
    {
        return $name;
    }

    public function setUp()
    {
        \samson\core\Error::$OUTPUT = false;

        $this->instance = \samson\core\Service::getInstance('\samsonphp\upload\UploadController');
        $this->instance->fs = \samson\core\Service::getInstance('\samsonphp\fs\FileService');
        $this->instance->fs->loadExternalService('\samsonphp\fs\LocalFileService');
    }

    // Test main method
    public function testUpload()
    {
        $this->instance->init();

        $result = $this->instance->__async_maxfilesize();

        $this->assertEquals(
            true,
            in_array($result['sizeString'], array(ini_get('post_max_size'), ini_get('upload_max_filesize')))
        );

        $upload = new Upload(array(), null, $this->instance);

        // Create Server Handler mock
        $upload->handler = $this->getMockBuilder('\samsonphp\upload\AsyncHandler')
            ->disableOriginalConstructor()
            ->getMock();

        $this->setHandlerParams($upload->handler);

        $upload->upload($filePath, $uploadName, $fileName);

        $this->assertTrue($upload->extension('png'));
        $this->assertEquals($upload->extension(), 'png');
        $this->assertEquals($upload->mimeType(), 'png');
        $this->assertEquals($upload->size(), 1003);
        $this->assertEquals($fileName, 'tests/samsonos.png');
        $this->assertEquals($upload->realName(), 'tests/samsonos.png');
        $this->assertNotNull($filePath);
        $this->assertNotNull($uploadName);
        $this->assertNotNull($upload->path());
        $this->assertNotNull($upload->name());
        $this->assertNotNull($upload->fullPath());
    }

    // Test help functions after uploading
    public function testUploadFunctions()
    {
        $this->instance->init();

        $upload = new Upload(array(), null, $this->instance);

        // Create Server Handler mock
        $upload->handler = $this->getMockBuilder('\samsonphp\upload\AsyncHandler')
            ->disableOriginalConstructor()
            ->getMock();

        $upload->handler
            ->expects($this->once())
            ->method('name')
            ->willReturn('');

        $this->assertFalse($upload->upload());
    }

    // Test upload file name handler
    public function testHandler()
    {
        $this->instance->init();
        $this->instance->fileNameHandler = array($this, 'fileNameHandler');

        $upload = new Upload(array(), 'myFile.png', $this->instance);

        // Create Server Handler mock
        $upload->handler = $this->getMockBuilder('\samsonphp\upload\AsyncHandler')
            ->disableOriginalConstructor()
            ->getMock();

        $this->setHandlerParams($upload->handler);

        $upload->upload($filePath);

        $this->assertNotEquals(0, strripos($filePath, 'myFile.png'));
    }

    // Test upload with extension error
    public function testExtension()
    {
        $this->instance->init();

        $upload = new Upload(array('xls', 'gif'), null, $this->instance);

        // Create Server Handler mock
        $upload->handler = $this->getMockBuilder('\samsonphp\upload\AsyncHandler')
            ->disableOriginalConstructor()
            ->getMock();

        $upload->handler
            ->expects($this->once())
            ->method('name')
            ->willReturn('tests/samsonos.png');

        $this->assertFalse($upload->upload());
    }

    // Test Class ServerHandler
    public function testServerHandler()
    {
        // Create fs mock
        $fs = $this->getMockBuilder('\samsonphp\fs\FileService')
            ->disableOriginalConstructor()
            ->getMock();

        $serverHandler = new AsyncHandler($fs);

        $serverHandler->name();
        $serverHandler->size();
        $serverHandler->file();
        $serverHandler->type();
        $serverHandler->write('fileName', 'fileDir', 'uploadDir');
    }

    public function testSyncUploading()
    {
        $this->instance->init();

        $upload = new Upload(array(), null, $this->instance);

        // Create Server Handler mock
        $upload->handler = $this->getMockBuilder('\samsonphp\upload\SyncHandler')
            ->disableOriginalConstructor()
            ->getMock();

        $upload->handler
            ->expects($this->once())
            ->method('name')
            ->with($this->anything())
            ->willReturn('samsonos.png');

        $upload->handler
            ->expects($this->once())
            ->method('size')
            ->with($this->anything())
            ->willReturn('1003');

        $upload->handler
            ->expects($this->once())
            ->method('file')
            ->with($this->anything())
            ->willReturn(file_get_contents('tests/samsonos.png'));

        $upload->handler
            ->expects($this->once())
            ->method('type')
            ->with($this->anything())
            ->willReturn('png');

        $upload->filesContainer = array(
            'filename' => array(
                'name' => 'samsonos.png',
                'type' => 'image/jpeg',
                'tmp_name' => 'tests/samsonos.png',
                'error' => 0,
                'size' => '1003'
            )
        );

        $upload->async(false)->upload($fileName, $filePath, $uploadName);
    }
}
