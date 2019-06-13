<?php
namespace Smetana\Images\Test\Unit\Block;

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
    //private $context;

    /*public function __construct(
        \Magento\Framework\View\Element\Context $context
    ) {
        $this->context = $context;
    }*/

    protected function setUp()
    {
        $this->scopeConfig = $this->createMock(\Magento\Framework\App\Config\ScopeConfigInterface::class);

        $this->resize = $this->createMock(\Smetana\Images\Model\Image\Resize::class);

//        $this->context = $this->createMock(\Magento\Framework\View\Element\Context::class);

        $this->imageBlock = (new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this))->getObject(
            \Smetana\Images\Block\Image::class,
            [
                'scopeConfig' => $this->scopeConfig,
                'resize'      => $this->resize,
            ]
        );
    }

    public function testGetImage()
    {
        $configPath = 'smetana_section/smetana_group/';
        $this->scopeConfig
            ->expects($this->any())
            ->method('getValue')
            ->withConsecutive(
                [$configPath . 'smetana_upload_image'],
                [$configPath . 'image_width'],
                [$configPath . 'image_height']
            )
            ->willReturnOnConsecutiveCalls('2.jpeg', '444', '444');

        $this->resize
            ->expects($this->once())
            ->method('resize')
            ->willReturn('smetana/resize/444444_2.jpeg');

        /*$this->context->getUrlBuilder()
            ->expects($this->once())
            ->method('getBaseUrl')
            ->with(['type' => 'media'])
            ->willReturn('http://magefilter.loc/pub/media/smetana/resize/444444_2.jpeg');*/

        $actual = $this->imageBlock->getImage();
        $this->assertEquals('smetana/resize/444444_2.jpeg', $actual);
    }
}
