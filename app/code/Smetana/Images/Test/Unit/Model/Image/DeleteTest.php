<?php
namespace Smetana\Images\Test\Unit\Model\Image;

use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\ReadInterface;
use Magento\Framework\Filesystem\Driver\File;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;
use Smetana\Images\Model\Image\Delete;

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
     * @var Delete
     */
    private $deleteModel;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var File
     */
    private $fileDriver;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        $this->filesystem = $this->createMock(Filesystem::class);
        $this->fileDriver = $this->createMock(File::class);

        $this->deleteModel = (new ObjectManager($this))->getObject(
            Delete::class,
            [
                'filesystem' => $this->filesystem,
                'fileDriver' => $this->fileDriver,
            ]
        );
    }

    public function testDeleteResizeImage()
    {
        $size = 444;
        $absoluteResizeFilePath = self::MEDIA_PATH . self::RESIZE_PATH . $size . $size . '_' . self::FILE_NAME;

        $mediaDirectory = $this->createMock(ReadInterface::class);
        $this->filesystem
            ->expects($this->once())
            ->method('getDirectoryRead')
            ->with('media')
            ->willReturn($mediaDirectory);

        $mediaDirectory
            ->expects($this->once())
            ->method('getAbsolutePath')
            ->with(self::RESIZE_PATH)
            ->willReturn(self::MEDIA_PATH . self::RESIZE_PATH);

        $this->fileDriver
            ->expects($this->once())
            ->method('isExists')
            ->with(self::MEDIA_PATH . self::RESIZE_PATH)
            ->willReturn(true);

        $this->fileDriver
            ->expects($this->once())
            ->method('readDirectory')
            ->with(self::MEDIA_PATH . self::RESIZE_PATH)
            ->willReturn([$absoluteResizeFilePath]);

        $this->fileDriver
            ->expects($this->once())
            ->method('deleteFile')
            ->with($absoluteResizeFilePath)
            ->willReturn(true);

        $actual = $this->deleteModel->deleteImage(self::RESIZE_PATH);
        $this->assertEquals(null, $actual);
    }

    public function testDeleteOrigImage()
    {
        $absoluteOrigFilePath = self::MEDIA_PATH . self::ORIG_PATH . self::FILE_NAME;

        $mediaDirectory = $this->createMock(ReadInterface::class);
        $this->filesystem
            ->expects($this->once())
            ->method('getDirectoryRead')
            ->with('media')
            ->willReturn($mediaDirectory);

        $mediaDirectory
            ->expects($this->once())
            ->method('getAbsolutePath')
            ->with(self::ORIG_PATH)
            ->willReturn(self::MEDIA_PATH . self::ORIG_PATH);

        $this->fileDriver
            ->expects($this->once())
            ->method('isExists')
            ->with(self::MEDIA_PATH . self::ORIG_PATH)
            ->willReturn(true);

        $this->fileDriver
            ->expects($this->once())
            ->method('readDirectory')
            ->with(self::MEDIA_PATH . self::ORIG_PATH)
            ->willReturn([$absoluteOrigFilePath]);

        $this->fileDriver
            ->expects($this->once())
            ->method('deleteFile')
            ->with($absoluteOrigFilePath)
            ->willReturn(true);

        $actual = $this->deleteModel->deleteImage(self::ORIG_PATH);
        $this->assertEquals(null, $actual);
    }
}
