<?php
namespace Smetana\Images\Test\Unit\Block;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Setup\Declaration\Schema\Dto\Columns\Integer;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Template\Context;
use PHPUnit\Framework\TestCase;
use Smetana\Images\Block\Image;
use Smetana\Images\Model\Image\Resize;

class ImageTest extends TestCase
{
    /**
     * Size of Image
     *
     * @var Integer
     */
    const IMAGE_SIZE = 444;

    /**
     * Name of Image
     *
     * @var String
     */
    const FILE_NAME = 'filename.ext1';

    /**
     * Path to Configurations
     *
     * @var String
     */
    const CONFIG_PATH = 'smetana_section/smetana_group/';

    /**
     * Path to Image name
     *
     * @var String
     */
    const NAME_OPTION = 'smetana_upload_image';

    /**
     * Path to Image width
     *
     * @var String
     */
    const WIDTH_OPTION = 'image_width';

    /**
     * Path to Image height
     *
     * @var String
     */
    const HEIGHT_OPTION = 'image_height';

    /**
     * @var Image
     */
    private $imageBlock;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var Resize
     */
    private $resize;

    /**
     * @var Context
     */
    private $urlBuilder;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        $this->urlBuilder = $this->createMock(UrlInterface::class);
        $context = $this->createMock(Context::class);
        $context->expects($this->once())
            ->method('getUrlBuilder')
            ->willReturn($this->urlBuilder);

        $this->scopeConfig = $this->createMock(ScopeConfigInterface::class);

        $this->resize = $this->createMock(Resize::class);


        $this->imageBlock = (new ObjectManager($this))->getObject(
            Image::class,
            [
                'scopeConfig' => $this->scopeConfig,
                'resize'      => $this->resize,
                'context' => $context
            ]
        );
    }

    public function testGetImage()
    {
        $imagePath = 'smetana/resize/' . self::IMAGE_SIZE . self::IMAGE_SIZE . '_' . self::FILE_NAME;
        $imageUrl = 'http://mage.com/pub/media/';

        $this->scopeConfig
            ->expects($this->any())
            ->method('getValue')
            ->withConsecutive(
                [self::CONFIG_PATH . self::NAME_OPTION],
                [self::CONFIG_PATH . self::WIDTH_OPTION],
                [self::CONFIG_PATH . self::HEIGHT_OPTION]
            )
            ->willReturnOnConsecutiveCalls(self::FILE_NAME, self::IMAGE_SIZE, self::IMAGE_SIZE);

        $this->resize
            ->expects($this->once())
            ->method('resize')
            ->willReturn($imagePath);

        $this->urlBuilder
            ->expects($this->once())
            ->method('getBaseUrl')
            ->with(['_type' => 'media'])
            ->willReturn($imageUrl);

        $actual = $this->imageBlock->getImage();
        $this->assertEquals($imageUrl . $imagePath, $actual);
    }

    public function testGetAbsentImage()
    {
        $this->scopeConfig
            ->expects($this->any())
            ->method('getValue')
            ->withConsecutive(
                [self::CONFIG_PATH . self::NAME_OPTION],
                [self::CONFIG_PATH . self::WIDTH_OPTION],
                [self::CONFIG_PATH . self::HEIGHT_OPTION]
            )
            ->willReturnOnConsecutiveCalls(self::FILE_NAME, self::IMAGE_SIZE, self::IMAGE_SIZE);

        $this->resize
            ->expects($this->once())
            ->method('resize')
            ->willReturn('');

        $actual = $this->imageBlock->getImage();
        $this->assertEquals('', $actual);
    }
}
