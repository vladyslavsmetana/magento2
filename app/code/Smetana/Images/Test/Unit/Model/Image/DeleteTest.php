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
     * Path to Image Folder
     *
     * @var String
     */
    const IMAGE_PATH = 'images';

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
     * Testing process of deleting existing image
     *
     * @return void
     */
    public function testDeleteExistingImage(): void
    {
        $absoluteFilePath = self::MEDIA_PATH . 'images' . self::FILE_NAME;

        $this->fileDriverMock
            ->expects($this->once())
            ->method('readDirectory')
            ->with(self::MEDIA_PATH . self::IMAGE_PATH)
            ->willReturn([$absoluteFilePath]);

        $this->fileDriverMock
            ->expects($this->once())
            ->method('deleteFile')
            ->with($absoluteFilePath)
            ->willReturn(true);

        $this->setImageExistence(true);
    }

    /**
     * Testing process of deleting non existent image
     *
     * @return void
     */
    public function testDeleteNonexistentImage(): void
    {
        $this->fileDriverMock
            ->expects($this->never())
            ->method('readDirectory');

        $this->fileDriverMock
            ->expects($this->never())
            ->method('deleteFile');

        $this->setImageExistence(false);
    }

    /**
     * Configure existence of Image
     *
     * @param bool $isExists
     *
     * @return void
     */
    private function setImageExistence(string $isExists): void
    {
        $mediaDirectory = $this->createMock(ReadInterface::class);
        $this->filesystemMock
            ->expects($this->once())
            ->method('getDirectoryRead')
            ->willReturn($mediaDirectory);

        $mediaDirectory
            ->expects($this->once())
            ->method('getAbsolutePath')
            ->with(self::IMAGE_PATH)
            ->willReturn(self::MEDIA_PATH . self::IMAGE_PATH);

        $this->fileDriverMock
            ->expects($this->once())
            ->method('isExists')
            ->with(self::MEDIA_PATH . self::IMAGE_PATH)
            ->willReturn($isExists);

        $this->deleteModel->deleteImage(self::IMAGE_PATH);
    }
}
