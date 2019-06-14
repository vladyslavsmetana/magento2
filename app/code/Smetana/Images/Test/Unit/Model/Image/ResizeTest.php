<?php
namespace Smetana\Images\Test\Unit\Model\Image;

class ResizeTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Smetana\Images\Model\Image\Resize
     */
    private $resizeModel;

    /**
     * @var \Magento\Framework\Filesystem
     */
    private $filesystem;

    /**
     * @var \Magento\Framework\Filesystem\Driver\File
     */
    private $fileDriver;

    /**
     * @var \Magento\Framework\Image\AdapterFactory
     */
    private $imageFactory;

    protected function setUp()
    {
        $this->filesystem = $this->createMock(\Magento\Framework\Filesystem::class);
        $this->fileDriver = $this->createMock(\Magento\Framework\Filesystem\Driver\File::class);
        $this->imageFactory = $this->createMock(\Magento\Framework\Image\AdapterFactory::class);

        $this->resizeModel = (new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this))->getObject(
            \Smetana\Images\Model\Image\Resize::class,
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

        $mediaDirectory = $this->createMock(\Magento\Framework\Filesystem\Directory\ReadInterface::class);
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

        $imageResize = $this->createMock(\Magento\Framework\Image\Adapter\AdapterInterface::class);

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
