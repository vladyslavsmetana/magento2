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

/**
 * @covers \Smetana\Images\Model\Image\Resize
 */
class ResizeTest extends TestCase
{
    /**
     * Size of Image
     *
     * @var Integer
     */
    const IMAGE_SIZE = 444;

    /**
     * @var Filesystem|\PHPUnit_Framework_MockObject_MockObject
     */
    private $filesystemMock;

    /**
     * @var File|\PHPUnit_Framework_MockObject_MockObject
     */
    private $fileDriverMock;

    /**
     * @var AdapterFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $imageFactoryMock;

    /**
     * @var Resize
     */
    private $resizeModel;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        $this->filesystemMock = $this->createMock(Filesystem::class);
        $this->fileDriverMock = $this->createMock(File::class);
        $this->imageFactoryMock = $this->createMock(AdapterFactory::class);

        $this->resizeModel = (new ObjectManager($this))->getObject(
            Resize::class,
            [
                'filesystem' => $this->filesystemMock,
                'imageFactory' => $this->imageFactoryMock,
                'fileDriver' => $this->fileDriverMock,
            ]
        );
    }

    /**
     * Testing image resizing process
     *
     * @return void
     */
    public function testResize(): void
    {
        $mediaPath = '/a/b/c/pub/media/';
        $fileName = 'filename.ext1';
        $origPath = 'smetana/original/';
        $resizePath = 'smetana/resize/';
        $absoluteOrigFilePath = $mediaPath . $origPath . $fileName;
        $absoluteResizeFilePath = $mediaPath . $resizePath . self::IMAGE_SIZE . self::IMAGE_SIZE . '_' . $fileName;

        $mediaDirectory = $this->createMock(ReadInterface::class);
        $this->filesystemMock
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

        $this->fileDriverMock
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

        $this->imageFactoryMock
            ->expects($this->once())
            ->method('create')
            ->willReturn($imageResize);

        $imageResize
            ->expects($this->once())
            ->method('resize')
            ->with(self::IMAGE_SIZE, self::IMAGE_SIZE)
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

        $actual = $this->resizeModel->resize($fileName, self::IMAGE_SIZE, self::IMAGE_SIZE);
        $this->assertEquals($origPath . $fileName, $actual);
    }
}
