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
     * Path to Original folder
     *
     * @var String
     */
    const ORIG_PATH = 'smetana/original/';

    /**
     * Path to Resize folder
     *
     * @var String
     */
    const RESIZE_PATH = 'smetana/resize/';

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
    public function testNonexistentResize(): void
    {
        $imageResize = $this->createMock(AdapterInterface::class);

        $this->imageFactoryMock
            ->expects($this->once())
            ->method('create')
            ->willReturn($imageResize);

        $imageResize
            ->expects($this->once())
            ->method('open')
            ->with(self::MEDIA_PATH . self::ORIG_PATH . self::FILE_NAME);

        $imageResize
            ->expects($this->once())
            ->method('resize')
            ->with(self::IMAGE_SIZE, self::IMAGE_SIZE);

        $imageResize
            ->expects($this->once())
            ->method('save')
            ->with(self::MEDIA_PATH . self::RESIZE_PATH . self::IMAGE_SIZE . self::IMAGE_SIZE . '_' . self::FILE_NAME);

        $this->setImageExistence(false);
    }

    /**
     * Test to avoid resizing process
     *
     * @return void
     */
    public function testExistingResize(): void
    {
        $imageResize = $this->createMock(AdapterInterface::class);

        $this->imageFactoryMock
            ->expects($this->never())
            ->method('create');

        $imageResize
            ->expects($this->never())
            ->method('open');

        $imageResize
            ->expects($this->never())
            ->method('resize');

        $imageResize
            ->expects($this->never())
            ->method('save');

        $this->setImageExistence(true);
    }

    /**
     * Configure existence of Image
     *
     * @param bool $isExists
     *
     * @return void
     */
    private function setImageExistence(bool $isExists): void
    {
        $absoluteOrigFilePath = self::MEDIA_PATH . self::ORIG_PATH . self::FILE_NAME;
        $absoluteResizeFilePath = self::MEDIA_PATH . self::RESIZE_PATH . self::IMAGE_SIZE . self::IMAGE_SIZE . '_' . self::FILE_NAME;

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
                [self::ORIG_PATH . self::FILE_NAME],
                ['']
            )
            ->willReturnOnConsecutiveCalls(
                $absoluteOrigFilePath,
                self::MEDIA_PATH
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
                $isExists
            );

        $mediaDirectory
            ->expects($this->once())
            ->method('getRelativePath')
            ->with($absoluteResizeFilePath)
            ->willReturn(self::ORIG_PATH . self::FILE_NAME);

        $actual = $this->resizeModel->resize(self::FILE_NAME, self::IMAGE_SIZE, self::IMAGE_SIZE);
        $this->assertEquals(self::ORIG_PATH . self::FILE_NAME, $actual);
    }
}
