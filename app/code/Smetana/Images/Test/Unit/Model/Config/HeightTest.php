<?php
namespace Smetana\Images\Test\Unit\Model\Config;

class HeightTest extends \PHPUnit\Framework\TestCase
{
    private $heightModel;

    private $heightModelMock;

    protected function setUp()
    {
        $this->heightModel = (new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this))->getObject(
            \Smetana\Images\Model\Config\Height::class
        );
        $this->heightModelMock = $this->createMock(\Smetana\Images\Model\Config\Height::class);
    }

    public function testFalseBeforeSave()
    {
        $this->assertEquals($this->heightModel->isValueChanged(), false);
        $actual = $this->heightModel->beforeSave();
        $this->assertEquals($this->heightModel, $actual);
    }

    public function testTrueBeforeSave()
    {
        //$this->assertEquals($this->heightModel->isValueChanged(), false);

        $this->heightModelMock
            ->expects($this->any())
            ->method('isValueChanged')
            ->willReturn(true);
        $this->assertEquals($this->heightModelMock->isValueChanged(), true);
        $actual = $this->heightModelMock->beforeSave();
        $this->assertEquals($this->heightModelMock, $actual);
    }
}
