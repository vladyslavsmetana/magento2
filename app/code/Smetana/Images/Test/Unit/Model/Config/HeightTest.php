<?php
namespace Smetana\Images\Test\Unit\Model\Config;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;
use Smetana\Images\Model\Config\Height;
use Smetana\Images\Model\Image\Delete;

class HeightTest extends TestCase
{
    /**
     * @var Height
     */
    private $heightModel;

    /**
     * @var Delete
     */
    private $deleteImageModel;

    protected function setUp()
    {
        $this->deleteImageModel = $this->createMock(Delete::class);

        $this->heightModel = (new ObjectManager($this))->getObject(
            Height::class,
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
        $this->heightModel->setValue('value');

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
