<?php
namespace Smetana\Images\Test\Unit\Model\Config;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;
use Smetana\Images\Model\Config\Width;
use Smetana\Images\Model\Image\Delete;
use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * @covers \Smetana\Images\Model\Config\Width
 */
class WidthTest extends TestCase
{
    /**
     * Path to image width Configuration
     *
     * @var String
     */
    const CONFIG_WIDTH_PATH = 'smetana_section/smetana_group/image_width';

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
     * @var Width
     */
    private $widthModel;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        $this->deleteImageModelMock = $this->createMock(Delete::class);

        $this->configMock = $this->createMock(ScopeConfigInterface::class);

        $this->widthModel = (new ObjectManager($this))->getObject(
            Width::class,
            [
                'deleteImageModel' => $this->deleteImageModelMock,
                'config'           => $this->configMock,
            ]
        );
    }

    /**
     * Save image process test with no change width value
     *
     * @return void
     */
    public function testFalseBeforeSave(): void
    {
        $this->deleteImageModelMock
            ->expects($this->never())
            ->method('deleteImage');

        $this->configureValueChange(self::IMAGE_SIZE, self::IMAGE_SIZE);
    }

    /**
     * Save image process test with changed width value
     *
     * @return void
     */
    public function testTrueBeforeSave(): void
    {
        $this->deleteImageModelMock
            ->expects($this->once())
            ->method('deleteImage')
            ->with('smetana/resize/');

        $this->configureValueChange(self::IMAGE_SIZE, 555);
    }

    /**
     * Configure width value
     *
     * @param int $value
     * @param int $oldValue
     *
     * @return void
     */
    private function configureValueChange(int $value, int $oldValue): void
    {
        $this->widthModel->setValue($value);
        $this->widthModel->setPath(self::CONFIG_WIDTH_PATH);
        $this->widthModel->setScope('default');
        $this->widthModel->setScopeCode('');

        $this->configMock
            ->expects($this->once())
            ->method('getValue')
            ->willReturn($oldValue);

        $this->widthModel->beforeSave();
    }
}
