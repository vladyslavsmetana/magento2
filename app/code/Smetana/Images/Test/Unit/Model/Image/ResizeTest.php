<?php
namespace Smetana\Images\Test\Unit\Model\Image;

use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\ReadInterface;
use Magento\Framework\Filesystem\Driver\File;
use Magento\Framework\Image\Adapter\AdapterInterface;
use Magento\Framework\Image\AdapterFactory;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;
use Smetana\Images\Model\Image\Resize;

class ResizeTest extends TestCase
{
    /**
     * @var Resize
     */
    private $resizeModel;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var File
     */
    private $fileDriver;

    /**
     * @var AdapterFactory
     */
    private $imageFactory;

    protected function setUp()
    {
        $this->filesystem = $this->createMock(Filesystem::class);
        $this->fileDriver = $this->createMock(File::class);
        $this->imageFactory = $this->createMock(AdapterFactory::class);

        $this->resizeModel = (new ObjectManager($this))->getObject(
            Resize::class,
            [
                'filesystem' => $this->filesystem,
                'imageFactory' => $this->imageFactory,
                'fileDriver' => $this->fileDriver,
            ]
        );
    }

    public function testResize()
    {
        $size = 444;
        $mediaPath = '/a/b/c/pub/media/';
        $fileName = 'filename.ext1';
        $origPath = 'smetana/original/';
        $resizePath = 'smetana/resize/';
        $absoluteOrigFilePath = $mediaPath . $origPath . $fileName;
        $absoluteResizeFilePath = $mediaPath . $resizePath . $size . $size . '_' . $fileName;

        $mediaDirectory = $this->createMock(ReadInterface::class);
        $this->filesystem
            ->expects($this->once())
            ->method('getDirectoryRead')
            ->with('media')
            ->willReturn($mediaDirectory);

        $mediaDirectory
            ->expects($this->any())
            ->method('getAbsolutePath')
            ->withConsecutive(
                [$origPath . $fileName],
                ['']
            )
            ->willReturnOnConsecutiveCalls(
                $absoluteOrigFilePath,
                $mediaPath
            );

        $this->fileDriver
            ->expects($this->any())
            ->method('isExists')
            ->withConsecutive(
                [$absoluteOrigFilePath],
                [$absoluteResizeFilePath]
            )
            ->willReturnOnConsecutiveCalls(
                true,
                false
            );

        $imageResize = $this->createMock(AdapterInterface::class);

        $this->imageFactory
            ->expects($this->once())
            ->method('create')
            ->willReturn($imageResize);

        $imageResize
            ->expects($this->once())
            ->method('resize')
            ->with($size, $size)
            ->willReturn(true);

        $imageResize
            ->expects($this->once())
            ->method('save')
            ->with($absoluteResizeFilePath)
            ->willReturn(true);

        $mediaDirectory
            ->expects($this->once())
            ->method('getRelativePath')
            ->with($absoluteResizeFilePath)
            ->willReturn($origPath . $fileName);


        $actual = $this->resizeModel->resize($fileName, $size, $size);
        $this->assertEquals($origPath . $fileName, $actual);
    }
}
