<?php
namespace Smetana\Images\Test\Unit\Model\Config;

class ImageTest extends \PHPUnit\Framework\TestCase
{
    protected $imageModel;

    protected function setUp()
    {
        $this->imageModel = (new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this))->getObject(
            \Smetana\Images\Model\Config\Image::class
        );
    }

    public function testBeforeSave()
    {
        $actual = $this->imageModel->beforeSave();
        $this->assertEquals($this->imageModel, $actual);
    }
}
