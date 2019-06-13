<?php
namespace Smetana\Images\Test\Unit\Model\Config;

class ImageTest extends \PHPUnit\Framework\TestCase
{
    private $imageModel;

    private $requestData;

    protected function setUp()
    {
        $this->requestData = $this->createMock(\Magento\Config\Model\Config\Backend\File\RequestData\RequestDataInterface::class);

        $this->imageModel = (new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this))->getObject(
            \Smetana\Images\Model\Config\Image::class,
            [
                'requestData' => $this->requestData
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
            ->with('smetana_section/smetana_group/smetana_upload_image')
            ->willReturn('/tmp/phpgHrwD1');

        $this->imageModel
            ->expects($this->any())
            ->method('mime_content_type')
            ->with('444')
            ->willReturn('444');

        $actual = $this->imageModel->beforeSave();
        $this->assertEquals($this->imageModel, $actual);
    }
}
