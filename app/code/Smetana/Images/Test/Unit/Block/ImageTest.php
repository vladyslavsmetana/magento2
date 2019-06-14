<?php
namespace Smetana\Images\Test\Unit\Block;

use Magento\Framework\App\ObjectManager;

class ImageTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Smetana\Images\Block\Image
     */
    private $imageBlock;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var \Smetana\Images\Model\Image\Resize
     */
    private $resize;

    /**
     * @var \Magento\Framework\View\Element\Context
     */
    private $_urlBuilder;

    public function __construct(
        \Magento\Framework\View\Element\Context $_urlBuilder = null
    ) {
        $this->_urlBuilder = $_urlBuilder
            ?? ObjectManager::getInstance()->get(\Magento\Framework\View\Element\Context::class);
    }

    protected function setUp()
    {
        $this->scopeConfig = $this->createMock(\Magento\Framework\App\Config\ScopeConfigInterface::class);

        $this->resize = $this->createMock(\Smetana\Images\Model\Image\Resize::class);

        //$this->_urlBuilder = $this->createMock(\Magento\Framework\View\Element\Context::class);

        $this->imageBlock = (new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this))->getObject(
            \Smetana\Images\Block\Image::class,
            [
                'scopeConfig' => $this->scopeConfig,
                'resize'      => $this->resize,
                '_urlBuilder' => $this->_urlBuilder
            ]
        );
    }

    public function testGetImage()
    {
        $size = 444;
        $fileName = 'filename.ext1';
        $configPath = 'smetana_section/smetana_group/';
        $imagePath = 'smetana/resize/' . $size . $size . '_' . $fileName;

        $this->scopeConfig
            ->expects($this->any())
            ->method('getValue')
            ->withConsecutive(
                [$configPath . 'smetana_upload_image'],
                [$configPath . 'image_width'],
                [$configPath . 'image_height']
            )
            ->willReturnOnConsecutiveCalls($fileName, $size, $size);

        $this->resize
            ->expects($this->once())
            ->method('resize')
            ->willReturn($imagePath);

        /*$this->_urlBuilder
            ->expects($this->once())
            ->method('getBaseUrl')
            ->will(['_type' => 'media'])
            ->willReturn('444');*/


        $actual = $this->imageBlock->getImage();
        $this->assertEquals($imagePath, $actual);
    }

    public function testGetAbsentImage()
    {
        $size = 444;
        $configPath = 'smetana_section/smetana_group/';

        $this->scopeConfig
            ->expects($this->any())
            ->method('getValue')
            ->withConsecutive(
                [$configPath . 'smetana_upload_image'],
                [$configPath . 'image_width'],
                [$configPath . 'image_height']
            )
            ->willReturnOnConsecutiveCalls('filename.ext1', $size, $size);

        $this->resize
            ->expects($this->once())
            ->method('resize')
            ->willReturn('');

        $actual = $this->imageBlock->getImage();
        $this->assertEquals('', $actual);
    }
}
