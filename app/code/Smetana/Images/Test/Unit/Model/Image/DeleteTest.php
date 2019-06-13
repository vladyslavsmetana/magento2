<?php
namespace Smetana\Images\Test\Unit\Model\Image;

class DeleteTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Smetana\Images\Model\Image\Delete
     */
    private $deleteModel;

    /**
     * @var \Magento\Framework\Filesystem
     */
    private $filesystem;

    /**
     * @var \Magento\Framework\Filesystem\Driver\File
     */
    private $fileDriver;

    protected function setUp()
    {
        $this->filesystem = $this->createMock(\Magento\Framework\Filesystem::class);
        $this->fileDriver = $this->createMock(\Magento\Framework\Filesystem\Driver\File::class);

        $this->deleteModel = (new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this))->getObject(
            \Smetana\Images\Model\Image\Delete::class,
            [
                'filesystem' => $this->filesystem,
                'fileDriver' => $this->fileDriver,
            ]
        );
    }

    public function testDeleteImage()
    {
        $mediaDirectory = $this->createMock(\Magento\Framework\Filesystem\Directory\ReadInterface::class);
        $this->filesystem
            ->expects($this->once())
            ->method('getDirectoryRead')
            ->with('media')
            ->willReturn($mediaDirectory);

        $mediaDirectory
            ->expects($this->once())
            ->method('getAbsolutePath')
            ->with('smetana/original/')
            ->willReturn('/home/vladyslav/sites/2.3-dev/magefilter/pub/media/smetana/resize/');

        $this->fileDriver
            ->expects($this->once())
            ->method('isExists')
            ->with('/home/vladyslav/sites/2.3-dev/magefilter/pub/media/smetana/resize/')
            ->willReturn(true);

        $this->fileDriver
            ->expects($this->once())
            ->method('readDirectory')
            ->with('/home/vladyslav/sites/2.3-dev/magefilter/pub/media/smetana/resize/')
            ->willReturn(['/home/vladyslav/sites/2.3-dev/magefilter/pub/media/smetana/resize/2.jpeg']);

        $this->fileDriver
            ->expects($this->once())
            ->method('deleteFile')
            ->with('/home/vladyslav/sites/2.3-dev/magefilter/pub/media/smetana/resize/2.jpeg')
            ->willReturn(true);

        $actual = $this->deleteModel->deleteImage('smetana/original/');
        $this->assertEquals(null, $actual);
    }
}
