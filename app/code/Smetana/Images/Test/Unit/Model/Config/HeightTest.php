<?php
namespace Smetana\Images\Test\Unit\Model\Config;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;
use Smetana\Images\Model\Config\Height;
use Smetana\Images\Model\Image\Delete;

/**
 * @covers \Smetana\Images\Model\Config\Height
 */
class HeightTest extends TestCase
{
    /**
     * @var Height
     */
    private $heightModel;

    /**
     * @var Delete|\PHPUnit_Framework_MockObject_MockObject
     */
    private $deleteImageModelMock;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        $this->deleteImageModelMock = $this->createMock(Delete::class);

        $this->heightModel = (new ObjectManager($this))->getObject(
            Height::class,
            [
                'deleteImageModel' => $this->deleteImageModelMock
            ]
        );
    }

    /**
     * Testing process of saving image without changed height value
     *
     * @return void
     */
    public function testFalseBeforeSave(): void
    {
        $this->assertEquals(false, $this->heightModel->isValueChanged());
        //засетити і замокати дані для getOldValue
        $actual = $this->heightModel->beforeSave();
        $this->assertEquals($this->heightModel, $actual);
    }

    /**
     * Testing process of saving image with changed height value
     *
     * @return void
     */
    public function testTrueBeforeSave(): void
    {
        $this->heightModel->setValue('value');
        $this->heightModel->setOldValue('old');
        //засетити і замокати дані для getOldValue

        $this->assertEquals(true, $this->heightModel->isValueChanged());

        $this->deleteImageModelMock
            ->expects($this->once())
            ->method('deleteImage')
            ->with('smetana/resize/')
            ->willReturn(null);

        $actual = $this->heightModel->beforeSave();
        $this->assertEquals($this->heightModel, $actual);
    }
    //!!!!!!!!!!!!написати private function для скорочення коду
}
