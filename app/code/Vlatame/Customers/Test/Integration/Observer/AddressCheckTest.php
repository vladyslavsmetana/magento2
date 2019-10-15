<?php
namespace Vlatame\Customers\Test\Integration\Observer;

use PHPUnit\Framework\TestCase;
use Magento\Customer\Model;
use Magento\Framework\Event\Observer;
use Magento\Directory\Model\CountryFactory;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\Checkout\Controller\Index\Index;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Vlatame\Customers\Block\AttributeNames;
use Vlatame\Customers\Observer\AddressCheck;

/**
 * @package Vlatame\Customers\Test\Integration\Observer
 */
class AddressCheckTest extends TestCase
{
    /**
     * Number of messages element
     *
     * @var String
     */
    const MESSAGE_QTY_PARAMETER = 'messageQty';

    /**
     * Page will be redirected element
     *
     * @var String
     */
    const IS_REDIRECT_PARAMETER = 'isRedirect';

    /**
     * @var Observer
     */
    private $observer;

    /**
     * @var Model\Customer
     */
    private $customer;

    /**
     * @var Model\Session
     */
    private $customerSession;

    /**
     * @var Model\Address
     */
    private $customerAddress;

    /**
     * @var CountryFactory
     */
    private $countryFactory;

    /**
     * @var Index
     */
    private $checkoutController;

    /**
     * @var ManagerInterface
     */
    private $messageManager;

    /**
     * @var AddressCheck
     */
    private $addressCheckObserver;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        $objectManager = Bootstrap::getObjectManager();
        $this->observer = $objectManager->get(Observer::class);
        $this->customer = $objectManager->get(Model\Customer::class);
        $this->customerSession = $objectManager->get(Model\Session::class);
        $this->customerAddress = $objectManager->get(Model\Address::class);
        $this->countryFactory = $objectManager->get(CountryFactory::class);
        $this->checkoutController = $objectManager->get(Index::class);
        $this->messageManager = $objectManager->get(ManagerInterface::class);
        $this->observer->setData('controller_action', $this->checkoutController);
    }

    /**
     * Test if customer address is not allowed
     *
     * @return void
     */
    public function testDisallowedAddress(): void
    {
        $expectedParameters = [
            self::MESSAGE_QTY_PARAMETER => 1,
            self::IS_REDIRECT_PARAMETER => true,
        ];
        $customerData = [
            AttributeNames::RESTRICTION_ENABLE    => '1',
            AttributeNames::COUNTRIES_RESTRICTION => 'AF,AX',
        ];

        $this->testExecute($expectedParameters, $customerData);
        $this->assertEquals(
            'United States is not allowed for delivery, please change the country',
            $this->messageManager->getMessages()->getLastAddedMessage()->getText()
        );
    }

    /**
     * Guest session test
     *
     * @return void
     */
    public function testGuestSession(): void
    {
        $expectedParameters = [
            self::MESSAGE_QTY_PARAMETER => 0,
            self::IS_REDIRECT_PARAMETER => false,
        ];

        $this->testExecute($expectedParameters, []);
    }

    /**
     * General test functionality
     *
     * @param array $expectedParameters
     * @param array $customerData
     *
     * @return void
     */
    private function testExecute(array $expectedParameters, array $customerData): void
    {
        foreach ($customerData as $key => $value) {
            $this->customer->setData($key, $value);
        }
        $this->customerAddress->setData('country_id', 'US');
        $this->customer->addAddress($this->customerAddress);
        $this->customerSession->setCustomer($this->customer);
        $this->addressCheckObserver = (new ObjectManager($this))->getObject(
            AddressCheck::class,
            [
                'customerSession' => $this->customerSession,
                'countryFactory' => $this->countryFactory,
                'messageManager' => $this->messageManager,
            ]
        );

        $this->addressCheckObserver->execute($this->observer);
        $this->assertCount(
            $expectedParameters[self::MESSAGE_QTY_PARAMETER],
            $this->messageManager->getMessages()->getItems()
        );
        $this->assertEquals(
            $expectedParameters[self::IS_REDIRECT_PARAMETER],
            $this->observer->getData('controller_action')->getResponse()->isRedirect()
        );
    }
}
