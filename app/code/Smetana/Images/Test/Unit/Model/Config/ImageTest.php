<?php
namespace Smetana\Images\Test\Unit\Model\Config;

class ImageTest extends \PHPUnit\Framework\TestCase
{
    private $imageModel;

    private $requestData;

    private $deleteImageModel;

    protected function setUp()
    {
        $this->requestData = $this->createMock(\Magento\Config\Model\Config\Backend\File\RequestData\RequestDataInterface::class);

        $this->deleteImageModel = $this->createMock(\Smetana\Images\Model\Image\Delete::class);

        $this->imageModel = (new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this))->getObject(
            \Smetana\Images\Model\Config\Image::class,
            [
                'requestData' => $this->requestData,
                'deleteImageModel' => $this->deleteImageModel
            ]
        );
    }

    public function testBeforeSave()
    {
        $this->imageModel->setValue(['value' => '2.jpeg']);
        $this->imageModel->setPath('smetana_section/smetana_group/smetana_upload_image');

        $this->requestData
            ->expects($this->any())
            ->method('getTmpName')
            ->withConsecutive(
                ['smetana_section/smetana_group/smetana_upload_image'],
                ['smetana_section/smetana_group/smetana_upload_image']
            )
            ->willReturnOnConsecutiveCalls(
                false,
                '/tmp/phpgHrwD1'
            );

        $this->deleteImageModel
            ->expects($this->any())
            ->method('deleteImage')
            ->withConsecutive(
                ['smetana/resize/'],
                ['smetana/original/']
            )
            ->willReturnOnConsecutiveCalls(
                null,
                null
            );

        $actual = $this->imageModel->beforeSave();
        $this->assertEquals($this->imageModel, $actual);
    }
}
