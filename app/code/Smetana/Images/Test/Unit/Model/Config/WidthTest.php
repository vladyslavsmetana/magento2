<?php
namespace Smetana\Images\Test\Unit\Model\Config;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;
use Smetana\Images\Model\Config\Width;
use Smetana\Images\Model\Image\Delete;

/**
 * @covers \Smetana\Images\Model\Config\Width
 */
class WidthTest extends TestCase
{
    /**
     * @var Width
     */
    private $widthModel;

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

        $this->widthModel = (new ObjectManager($this))->getObject(
            Width::class,
            [
                'deleteImageModel' => $this->deleteImageModelMock
            ]
        );
    }

    /**
     * Testing process of saving image without changed width value
     *
     * @return void
     */
    public function testFalseBeforeSave(): void
    {
        $this->assertEquals(false, $this->widthModel->isValueChanged());
        $actual = $this->widthModel->beforeSave();
        $this->assertEquals($this->widthModel, $actual);
    }

    /**
     * Testing process of saving image with changed width value
     *
     * @return void
     */
    public function testTrueBeforeSave(): void
    {
        $this->widthModel->setValue('value');

        $this->assertEquals(true, $this->widthModel->isValueChanged());

        $this->deleteImageModelMock
            ->expects($this->once())
            ->method('deleteImage')
            ->with('smetana/resize/')
            ->willReturn(null);

        $actual = $this->widthModel->beforeSave();
        $this->assertEquals($this->widthModel, $actual);
    }
}
