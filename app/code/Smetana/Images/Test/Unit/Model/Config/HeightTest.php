<?php
namespace Smetana\Images\Test\Unit\Model\Config;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;
use Smetana\Images\Model\Config\Height;
use Smetana\Images\Model\Image\Delete;
use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * @covers \Smetana\Images\Model\Config\Height
 */
class HeightTest extends TestCase
{
    /**
     * Path to image height Configuration
     *
     * @var String
     */
    const CONFIG_HEIGHT_PATH = 'smetana_section/smetana_group/image_height';

    /**
     * Image standard size
     *
     * @var Integer
     */
    const IMAGE_SIZE = 444;

    /**
     * @var Delete|\PHPUnit_Framework_MockObject_MockObject
     */
    private $deleteImageModelMock;

    /**
     * @var ScopeConfigInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $configMock;

    /**
     * @var Height
     */
    private $heightModel;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        $this->deleteImageModelMock = $this->createMock(Delete::class);

        $this->configMock = $this->createMock(ScopeConfigInterface::class);

        $this->heightModel = (new ObjectManager($this))->getObject(
            Height::class,
            [
                'deleteImageModel' => $this->deleteImageModelMock,
                'config'           => $this->configMock,
            ]
        );
    }

    /**
     * Testing process of saving image with no change height value
     *
     * @return void
     */
    public function testBeforeSaveValueIsntChanged(): void
    {
        $this->deleteImageModelMock
            ->expects($this->never())
            ->method('deleteImage');

        $this->configureValueChange(self::IMAGE_SIZE, self::IMAGE_SIZE);
    }

    /**
     * Testing process of saving image with changed height value
     *
     * @return void
     */
    public function testBeforeSaveValueIsChanged(): void
    {
        $this->deleteImageModelMock
            ->expects($this->once())
            ->method('deleteImage')
            ->with('smetana/resize/');

        $this->configureValueChange(self::IMAGE_SIZE, 555);
    }

    /**
     * Configure height value
     *
     * @param int $value
     * @param int $oldValue
     *
     * @return void
     */
    private function configureValueChange(int $value, int $oldValue): void
    {
        $this->heightModel->setValue($value);
        $this->heightModel->setPath(self::CONFIG_HEIGHT_PATH);
        $this->heightModel->setScope('default');
        $this->heightModel->setScopeCode('');

        $this->configMock
            ->expects($this->once())
            ->method('getValue')
            ->willReturn($oldValue);

        $this->heightModel->beforeSave();
    }
}
