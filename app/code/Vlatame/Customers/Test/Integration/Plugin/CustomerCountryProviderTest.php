<?php
namespace Vlatame\Customers\Test\Integration\Plugin;

use PHPUnit\Framework\TestCase;
use Magento\Customer\Model\Session;
use Magento\Customer\Model\Customer;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\Directory\Model\ResourceModel\Country\Collection;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Customer\Model\ResourceModel\Address\Attribute\Source\Country;
use Vlatame\Customers\Block\AttributeNames;
use Vlatame\Customers\Plugin\CustomerCountryProvider;

/**
 * @package Vlatame\Customers\Test\Integration\Plugin
 */
class CustomerCountryProviderTest extends TestCase
{
    /**
     * @var Collection
     */
    private $filteredCountryCollection;

    /**
     * @var Customer
     */
    private $customer;

    /**
     * @var Session
     */
    private $customerSession;

    /**
     * @var Country
     */
    private $country;

    /**
     * @var CustomerCountryProvider
     */
    private $countryPlugin;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        $objectManager = Bootstrap::getObjectManager();
        $this->filteredCountryCollection = $objectManager->get(Collection::class);
        $this->customer = $objectManager->get(Customer::class);
        $this->customerSession = $objectManager->get(Session::class);
        $this->country = $objectManager->get(Country::class);

        $customerData = [
            AttributeNames::RESTRICTION_ENABLE    => '1',
            AttributeNames::COUNTRIES_RESTRICTION => 'AF,AX',
        ];
        foreach ($customerData as $key => $value) {
            $this->customer->setData($key, $value);
        }
        $this->customerSession->setCustomer($this->customer);

        $this->countryPlugin = (new ObjectManager($this))->getObject(
            CustomerCountryProvider::class,
            [
                'customerSession' => $this->customerSession
            ]
        );
    }

    /**
     * Change Country options test
     *
     * @return void
     */
    public function testAfterLoadByStore(): void
    {
        $allowedCountryData = array_slice($this->country->getAllOptions(), 0, 3);

        $this->countryPlugin->afterLoadByStore($this->filteredCountryCollection);
        $this->assertEquals($allowedCountryData, $this->filteredCountryCollection->toOptionArray());

        //        $allowedCountryData = [
//            [
//                'value' => '',
//                'label' => ' ',
//            ],
//            [
//                'value' => 'AF',
//                'label' => 'Afghanistan',
//                'is_region_visible' => true,
//            ],
//            [
//                'value' => 'AX',
//                'label' => 'Åland Islands',
//                'is_region_visible' => true,
//            ],
//        ];
    }
}
