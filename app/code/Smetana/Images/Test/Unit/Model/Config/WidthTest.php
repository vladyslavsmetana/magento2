<?php
namespace Smetana\Images\Test\Unit\Model\Config;

class WidthTest extends \PHPUnit\Framework\TestCase
{
    protected $widthModel;

    protected function setUp()
    {
        $this->widthModel = (new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this))->getObject(
            \Smetana\Images\Model\Config\Width::class
        );
    }

    public function testBeforeSave()
    {
        $actual = $this->widthModel->beforeSave();
        $this->assertEquals(null, $actual);
    }
}
