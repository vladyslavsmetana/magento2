<?php
namespace Smetana\Images\Test\Unit\Model\Config;

class HeightTest extends \PHPUnit\Framework\TestCase
{
    private $heightModel;

    private $deleteImageModel;

    protected function setUp()
    {
        $this->deleteImageModel = $this->createMock(\Smetana\Images\Model\Image\Delete::class);

        $this->heightModel = (new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this))->getObject(
            \Smetana\Images\Model\Config\Height::class,
            [
                'deleteImageModel' => $this->deleteImageModel
            ]
        );
    }

    public function testFalseBeforeSave()
    {
        $this->assertEquals(false, $this->heightModel->isValueChanged());
        $actual = $this->heightModel->beforeSave();
        $this->assertEquals($this->heightModel, $actual);
    }

    public function testTrueBeforeSave()
    {

        $this->heightModel->setValue('444');
        $this->heightModel->setOldValue('444');

        $this->assertEquals(true, $this->heightModel->isValueChanged());

        $this->deleteImageModel
            ->expects($this->once())
            ->method('deleteImage')
            ->with('smetana/resize/')
            ->willReturn(null);

        $actual = $this->heightModel->beforeSave();
        $this->assertEquals($this->heightModel, $actual);
    }
}
