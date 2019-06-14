<?php
namespace Smetana\Images\Test\Unit\Model\Config;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;
use Smetana\Images\Model\Config\Width;
use Smetana\Images\Model\Image\Delete;

class WidthTest extends TestCase
{
    /**
     * @var Width
     */
    private $widthModel;

    /**
     * @var Delete
     */
    private $deleteImageModel;

    protected function setUp()
    {
        $this->deleteImageModel = $this->createMock(Delete::class);

        $this->widthModel = (new ObjectManager($this))->getObject(
            Width::class,
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
        $this->widthModel->setValue('value');

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
