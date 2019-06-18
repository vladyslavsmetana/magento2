<?php
namespace Smetana\Images\Test\Unit\Model\Config;

use Magento\Config\Model\Config\Backend\File\RequestData\RequestDataInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;
use Smetana\Images\Model\Config\Image;
use Smetana\Images\Model\Image\Delete;

/**
 * @covers \Smetana\Images\Model\Config\Image
 */
class ImageTest extends TestCase
{
    /**
     * Name of Image
     *
     * @var String
     */
    const FILE_NAME = 'filename.ext1';

    /**
     * @var RequestDataInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $requestDataMock;

    /**
     * @var Delete|\PHPUnit_Framework_MockObject_MockObject
     */
    private $deleteImageMock;

    /**
     * @var Image
     */
    private $imageModel;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        $this->requestDataMock = $this->createMock(RequestDataInterface::class);

        $this->deleteImageMock = $this->createMock(Delete::class);

        $this->imageModel = (new ObjectManager($this))->getObject(
            Image::class,
            [
                'requestData' => $this->requestDataMock,
                'deleteImage' => $this->deleteImageMock
            ]
        );
    }

    /**
     * Testing process of saving with image
     *
     * @return void
     */
    public function testBeforeSaveWithFile(): void
    {
        $this->requestDataMock
            ->expects($this->once())
            ->method('getName')
            ->willReturn(self::FILE_NAME);

        $this->deleteImageMock
            ->expects($this->any())
            ->method('deleteImage');

        $this->configureFileData([false, '/tmp/phpgHrwD1'], 'section/group/image');
    }

    /**
     * Testing process of saving without image
     *
     * @return void
     */
    public function testBeforeSaveWithoutFile(): void
    {
        $this->requestDataMock
            ->expects($this->never())
            ->method('getName');

        $this->deleteImageMock
            ->expects($this->never())
            ->method('deleteImage');

        $this->configureFileData([false, false], '');
    }

    /**
     * Configure file data
     *
     * @param array $fileParameters
     * @param string $path
     *
     * @return void
     */
    private function configureFileData(array $fileParameters, string $path): void
    {
        $this->imageModel->setValue(['value ' => self::FILE_NAME]);
        $this->imageModel->setPath($path);

        $this->requestDataMock
            ->expects($this->any())
            ->method('getTmpName')
            ->willReturnOnConsecutiveCalls(
                $fileParameters[0],
                $fileParameters[1]
            );

        $this->imageModel->beforeSave();
    }
}
