<?php
namespace Vlatame\Customers\Test\Unit\Plugin;

use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Vlatame\Customers\Plugin\CustomerCountryProvider;
use Vlatame\Customers\Block\AttributeNames;
use PHPUnit\Framework\MockObject\MockObject;
use Magento\Customer\Model\Session;
use Magento\Customer\Model\Customer;
use Magento\Directory\Model\ResourceModel\Country\Collection;

/**
 * @package Vlatame\Customers\Test\Unit\Plugin
 */
class CustomerCountryProviderTest extends TestCase
{
    /**
     * Expected test result
     *
     * @var array
     */
    private $expected = [
        'value' => 'AL',
        'label' => 'Albania',
    ];

    /**
     * @var Session|MockObject
     */
    private $customerSessionMock;

    /**
     * @var Collection|MockObject
     */
    private $pluginObjectMock;

    /**
     * @var Customer|MockObject
     */
    private $customerModelMock;

    /**
     * @var CustomerCountryProvider
     */
    private $countryPlugin;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        $this->customerSessionMock = $this->createMock(Session::class);

        $this->pluginObjectMock = $this->createMock(Collection::class);
        $this->customerModelMock = $this->createMock(Customer::class);

        $this->countryPlugin = (new ObjectManager($this))->getObject(
            CustomerCountryProvider::class,
            [
                'customerSession' => $this->customerSessionMock
            ]
        );
    }

    /**
     * Change Country data test
     *
     * @param void
     *
     * @return void
     */
    public function testAfterToOptionArray(): void
    {
        $options = [
            [
                'value' => 'AF',
                'label' => 'Afghanistan',
            ],
            $this->expected,
            [
                'value' => 'US',
                'label' => 'United States',
            ],
        ];

        $this->customerSessionMock
            ->expects($this->once())
            ->method('getCustomer')
            ->willReturn($this->customerModelMock);

        $this->customerModelMock
            ->expects($this->once())
            ->method('getData')
            ->willReturn([AttributeNames::RESTRICTION_ENABLE => 1, AttributeNames::COUNTRIES_RESTRICTION => 'AF,US']);

        $actual = $this->countryPlugin->afterToOptionArray($this->pluginObjectMock, $options);
        $this->assertEquals(['1' => $this->expected], $actual);
    }
}
