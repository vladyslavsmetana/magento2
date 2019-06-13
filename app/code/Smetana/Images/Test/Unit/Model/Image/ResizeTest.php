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

    public function testResize()    //!!!!!!!!!!!!!!!!!!!!!реальні довгі path не використовувати
    {
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
                ['smetana/original/2.jpeg'],
                ['']
            )
            ->willReturnOnConsecutiveCalls(
                '/home/vladyslav/sites/2.3-dev/magefilter/pub/media/smetana/original/2.jpeg',
                '/home/vladyslav/sites/2.3-dev/magefilter/pub/media/'
            );

        $this->fileDriver
            ->expects($this->any())
            ->method('isExists')
            ->withConsecutive(
                ['/home/vladyslav/sites/2.3-dev/magefilter/pub/media/smetana/original/2.jpeg',],
                ['/home/vladyslav/sites/2.3-dev/magefilter/pub/media/smetana/resize/444444_2.jpeg']
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
            ->with(444, 444)
            ->willReturn(true);

        $imageResize
            ->expects($this->once())
            ->method('save')
            ->with('/home/vladyslav/sites/2.3-dev/magefilter/pub/media/smetana/resize/444444_2.jpeg')
            ->willReturn(true);

        $mediaDirectory
            ->expects($this->once())
            ->method('getRelativePath')
            ->with('/home/vladyslav/sites/2.3-dev/magefilter/pub/media/smetana/resize/444444_2.jpeg')
            ->willReturn('smetana/resize/444444_2.jpeg');


        $actual = $this->resizeModel->resize('2.jpeg', 444, 444);
        $this->assertEquals('smetana/resize/444444_2.jpeg', $actual);
    }
}
