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
        $mediaPath = '/a/b/c/pub/media/';
        $fileName = 'filename.ext1';
        $resizePath = 'smetana/resize/';
        $absoluteResizeFilePath = $mediaPath . $resizePath . $size . $size . '_' . $fileName;

        $mediaDirectory = $this->createMock(ReadInterface::class);
        $this->filesystem
            ->expects($this->once())
            ->method('getDirectoryRead')
            ->with('media')
            ->willReturn($mediaDirectory);

        $mediaDirectory
            ->expects($this->once())
            ->method('getAbsolutePath')
            ->with($resizePath)
            ->willReturn($mediaPath . $resizePath);

        $this->fileDriver
            ->expects($this->once())
            ->method('isExists')
            ->with($mediaPath . $resizePath)
            ->willReturn(true);

        $this->fileDriver
            ->expects($this->once())
            ->method('readDirectory')
            ->with($mediaPath . $resizePath)
            ->willReturn([$absoluteResizeFilePath]);

        $this->fileDriver
            ->expects($this->once())
            ->method('deleteFile')
            ->with($absoluteResizeFilePath)
            ->willReturn(true);

        $actual = $this->deleteModel->deleteImage($resizePath);
        $this->assertEquals(null, $actual);
    }

    public function testDeleteOrigImage()
    {
        $mediaPath = '/a/b/c/pub/media/';
        $fileName = 'filename.ext1';
        $origPath = 'smetana/original/';
        $absoluteOrigFilePath = $mediaPath . $origPath . $fileName;

        $mediaDirectory = $this->createMock(ReadInterface::class);
        $this->filesystem
            ->expects($this->once())
            ->method('getDirectoryRead')
            ->with('media')
            ->willReturn($mediaDirectory);

        $mediaDirectory
            ->expects($this->once())
            ->method('getAbsolutePath')
            ->with($origPath)
            ->willReturn($mediaPath . $origPath);

        $this->fileDriver
            ->expects($this->once())
            ->method('isExists')
            ->with($mediaPath . $origPath)
            ->willReturn(true);

        $this->fileDriver
            ->expects($this->once())
            ->method('readDirectory')
            ->with($mediaPath . $origPath)
            ->willReturn([$absoluteOrigFilePath]);

        $this->fileDriver
            ->expects($this->once())
            ->method('deleteFile')
            ->with($absoluteOrigFilePath)
            ->willReturn(true);

        $actual = $this->deleteModel->deleteImage($origPath);
        $this->assertEquals(null, $actual);
    }
}
