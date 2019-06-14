<?php
namespace Smetana\Images\Test\Unit\Model\Image;

use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\ReadInterface;
use Magento\Framework\Filesystem\Driver\File;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;
use Smetana\Images\Model\Image\Delete;

/**
 * @covers \Smetana\Images\Model\Image\Delete
 */
class DeleteTest extends TestCase
{
    /**
     * Path to Media directory
     *
     * @var String
     */
    const MEDIA_PATH = '/a/b/c/pub/media/';

    /**
     * Name of Image
     *
     * @var String
     */
    const FILE_NAME = 'filename.ext1';

    /**
     * Path to resize Folder
     *
     * @var String
     */
    const RESIZE_PATH = 'smetana/resize/';

    /**
     * Path to original Folder
     *
     * @var String
     */
    const ORIG_PATH = 'smetana/original/';

    /**
     * @var Filesystem|\PHPUnit_Framework_MockObject_MockObject
     */
    private $filesystemMock;

    /**
     * @var File|\PHPUnit_Framework_MockObject_MockObject
     */
    private $fileDriverMock;

    /**
     * @var Delete
     */
    private $deleteModel;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        $this->filesystemMock = $this->createMock(Filesystem::class);
        $this->fileDriverMock = $this->createMock(File::class);

        $this->deleteModel = (new ObjectManager($this))->getObject(
            Delete::class,
            [
                'filesystem' => $this->filesystemMock,
                'fileDriver' => $this->fileDriverMock,
            ]
        );
    }

    /**
     * Testing process of deleting resized image
     *
     * @return void
     */
    public function testDeleteResizeImage(): void
    {
        $size = 444;
        $absoluteResizeFilePath = self::MEDIA_PATH . self::RESIZE_PATH . $size . $size . '_' . self::FILE_NAME;

        $mediaDirectory = $this->createMock(ReadInterface::class);
        $this->filesystemMock
            ->expects($this->once())
            ->method('getDirectoryRead')
            ->with('media')
            ->willReturn($mediaDirectory);

        $mediaDirectory
            ->expects($this->once())
            ->method('getAbsolutePath')
            ->with(self::RESIZE_PATH)
            ->willReturn(self::MEDIA_PATH . self::RESIZE_PATH);

        $this->fileDriverMock
            ->expects($this->once())
            ->method('isExists')
            ->with(self::MEDIA_PATH . self::RESIZE_PATH)
            ->willReturn(true);

        $this->fileDriverMock
            ->expects($this->once())
            ->method('readDirectory')
            ->with(self::MEDIA_PATH . self::RESIZE_PATH)
            ->willReturn([$absoluteResizeFilePath]);

        $this->fileDriverMock
            ->expects($this->once())
            ->method('deleteFile')
            ->with($absoluteResizeFilePath)
            ->willReturn(true);

        $actual = $this->deleteModel->deleteImage(self::RESIZE_PATH);
        $this->assertEquals(null, $actual);
    }

    /**
     * Testing process of deleting original image
     *
     * @return void
     */
    public function testDeleteOrigImage(): void
    {
        $absoluteOrigFilePath = self::MEDIA_PATH . self::ORIG_PATH . self::FILE_NAME;

        $mediaDirectory = $this->createMock(ReadInterface::class);
        $this->filesystemMock
            ->expects($this->once())
            ->method('getDirectoryRead')
            ->with('media')
            ->willReturn($mediaDirectory);

        $mediaDirectory
            ->expects($this->once())
            ->method('getAbsolutePath')
            ->with(self::ORIG_PATH)
            ->willReturn(self::MEDIA_PATH . self::ORIG_PATH);

        $this->fileDriverMock
            ->expects($this->once())
            ->method('isExists')
            ->with(self::MEDIA_PATH . self::ORIG_PATH)
            ->willReturn(true);

        $this->fileDriverMock
            ->expects($this->once())
            ->method('readDirectory')
            ->with(self::MEDIA_PATH . self::ORIG_PATH)
            ->willReturn([$absoluteOrigFilePath]);

        $this->fileDriverMock
            ->expects($this->once())
            ->method('deleteFile')
            ->with($absoluteOrigFilePath)
            ->willReturn(true);

        $actual = $this->deleteModel->deleteImage(self::ORIG_PATH);
        $this->assertEquals(null, $actual);
    }
}
