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

/**
 * @covers \Smetana\Images\Block\Image
 */
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
     * @var ScopeConfigInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $scopeConfigMock;

    /**
     * @var Resize|\PHPUnit_Framework_MockObject_MockObject
     */
    private $resizeMock;

    /**
     * @var UrlInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $urlBuilderMock;

    /**
     * @var Image
     */
    private $imageBlock;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        $this->urlBuilderMock = $this->createMock(UrlInterface::class);
        $context = $this->createMock(Context::class);
        $context->expects($this->once())
            ->method('getUrlBuilder')
            ->willReturn($this->urlBuilderMock);

        $this->scopeConfigMock = $this->createMock(ScopeConfigInterface::class);

        $this->resizeMock = $this->createMock(Resize::class);

        $this->imageBlock = (new ObjectManager($this))->getObject(
            Image::class,
            [
                'scopeConfig' => $this->scopeConfigMock,
                'resize'      => $this->resizeMock,
                'context'     => $context,
            ]
        );
    }

    /**
     * Testing process of getting image
     *
     * @return void
     */
    public function testGetImage(): void
    {
        $imagePath = 'smetana/resize/' . self::IMAGE_SIZE . self::IMAGE_SIZE . '_' . self::FILE_NAME;
        $imageUrl = 'http://mage.com/pub/media/';

        $this->urlBuilderMock
            ->expects($this->once())
            ->method('getBaseUrl')
            ->with(['_type' => 'media'])
            ->willReturn($imageUrl);

        $this->configureVars($imagePath, $imageUrl . $imagePath);
    }

    /**
     * Testing process of getting image when file missing
     *
     * @return void
     */
    public function testGetAbsentImage(): void
    {
        $this->urlBuilderMock
            ->expects($this->never())
            ->method('getBaseUrl');

        $this->configureVars('', '');
    }

    /**
     * General interface
     *
     * @param string $imagePath
     * @param string $expect
     *
     * @return void
     */
    private function configureVars(string $imagePath, string $expect): void
    {
        $this->scopeConfigMock
            ->expects($this->any())
            ->method('getValue')
            ->withConsecutive(
                [self::CONFIG_PATH . self::NAME_OPTION],
                [self::CONFIG_PATH . self::WIDTH_OPTION],
                [self::CONFIG_PATH . self::HEIGHT_OPTION]
            )
            ->willReturnOnConsecutiveCalls(self::FILE_NAME, self::IMAGE_SIZE, self::IMAGE_SIZE);

        $this->resizeMock
            ->expects($this->once())
            ->method('resize')
            ->willReturn($imagePath);

        $actual = $this->imageBlock->getImage();

        $this->assertEquals($expect, $actual);
    }
}
