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
     * Image name Configuration
     *
     * @var String
     */
    const IMAGE_NAME_CONF = 'smetana_section/smetana_group/smetana_upload_image';

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
     * Testing process of saving image
     *
     * @return void
     */
    public function testBeforeSave(): void
    {
        $fileName = 'filename.ext1';

        $this->imageModel->setValue(['value ' => $fileName]);
        $this->imageModel->setPath(self::IMAGE_NAME_CONF);

        $this->requestDataMock
            ->expects($this->any())
            ->method('getTmpName')
            ->withConsecutive(
                [self::IMAGE_NAME_CONF],
                [self::IMAGE_NAME_CONF]
            )
            ->willReturnOnConsecutiveCalls(
                false,
                '/tmp/phpgHrwD1'
            );

        $this->deleteImageMock
            ->expects($this->any())
            ->method('deleteImage')
            ->willReturnOnConsecutiveCalls(
                null,
                null
            );

        $actual = $this->imageModel->beforeSave();
        $this->assertEquals($this->imageModel, $actual);
    }
}
