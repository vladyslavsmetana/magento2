<?php
namespace Smetana\Images\Test\Unit\Model\Config;

use Magento\Config\Model\Config\Backend\File\RequestData\RequestDataInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;
use Smetana\Images\Model\Config\Image;
use Smetana\Images\Model\Image\Delete;

class ImageTest extends TestCase
{
    /**
     * @var Image
     */
    private $imageModel;

    /**
     * @var RequestDataInterface
     */
    private $requestData;

    /**
     * @var Delete
     */
    private $deleteImageModel;

    protected function setUp()
    {
        $this->requestData = $this->createMock(RequestDataInterface::class);

        $this->deleteImageModel = $this->createMock(Delete::class);

        $this->imageModel = (new ObjectManager($this))->getObject(
            Image::class,
            [
                'requestData' => $this->requestData,
                'deleteImageModel' => $this->deleteImageModel
            ]
        );
    }

    public function testBeforeSave()
    {
        $fileName = 'filename.ext1';
        $imageParameter = 'smetana_section/smetana_group/smetana_upload_image';

        $this->imageModel->setValue(['value ' => $fileName]);
        $this->imageModel->setPath($imageParameter);

        $this->requestData
            ->expects($this->any())
            ->method('getTmpName')
            ->withConsecutive(
                [$imageParameter],
                [$imageParameter]
            )
            ->willReturnOnConsecutiveCalls(
                false,
                '/tmp/phpgHrwD1'
            );

        $this->deleteImageModel
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
