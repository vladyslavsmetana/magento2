<?php
namespace Smetana\Images\Test\Unit\Model\Config;

class HeightTest extends \PHPUnit\Framework\TestCase
{
    protected $heightModel;

    protected function setUp()
    {
        $this->heightModel = (new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this))->getObject(
            \Smetana\Images\Model\Config\Height::class
        );
    }

    public function testBeforeSave()
    {
        $actual = $this->heightModel->beforeSave();
        $this->assertEquals(null, $actual);
    }
}
