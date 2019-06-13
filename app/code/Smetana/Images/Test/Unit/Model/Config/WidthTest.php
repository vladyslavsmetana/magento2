<?php
namespace Smetana\Images\Test\Unit\Model\Config;

class WidthTest extends \PHPUnit\Framework\TestCase
{
    private $widthModel;

    private $deleteImageModel;

    protected function setUp()
    {
        $this->deleteImageModel = $this->createMock(\Smetana\Images\Model\Image\Delete::class);

        $this->widthModel = (new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this))->getObject(
            \Smetana\Images\Model\Config\Width::class,
            [
                'deleteImageModel' => $this->deleteImageModel
            ]
        );
    }

    public function testFalseBeforeSave()
    {
        $this->assertEquals(false, $this->widthModel->isValueChanged());
        $actual = $this->widthModel->beforeSave();
        $this->assertEquals($this->widthModel, $actual);
    }

    public function testTrueBeforeSave()
    {
        $this->widthModel->setValue('444');
        $this->widthModel->setOldValue('444');

        $this->assertEquals(true, $this->widthModel->isValueChanged());

        $this->deleteImageModel
            ->expects($this->once())
            ->method('deleteImage')
            ->with('smetana/resize/')
            ->willReturn(null);

        $actual = $this->widthModel->beforeSave();
        $this->assertEquals($this->widthModel, $actual);
    }
}
