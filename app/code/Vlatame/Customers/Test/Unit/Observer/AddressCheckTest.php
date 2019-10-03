<?php
namespace Vlatame\Customers\Test\Unit\Observer;

use Magento\Customer\Model\Customer;
use Magento\Customer\Model\Session;
use Magento\Directory\Model;
use Magento\Framework\Event\Observer;
use Magento\Framework\Message\ManagerInterface;
use Magento\Customer\Model\Address;
use Magento\Checkout\Controller\Index\Index;
use Magento\Framework\App\Response\Http;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\UrlInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Vlatame\Customers\Observer\AddressCheck;
use Vlatame\Customers\Block\AttributeNames;

/**
 * @package Vlatame\Customers\Test\Unit\Observer
 */
class AddressCheckTest extends TestCase
{
    /**
     * @var Observer|MockObject
     */
    private $observerMock;

    /**
     * @var ManagerInterface|MockObject
     */
    private $messageManagerMock;

    /**
     * @var Session|MockObject
     */
    private $customerSessionMock;

    /**
     * @var UrlInterface|MockObject
     */
    private $urlMock;

    /**
     * @var Model\CountryFactory|MockObject
     */
    private $countryFactoryMock;

    /**
     * @var Customer|MockObject
     */
    private $customerMock;

    /**
     * @var Address|MockObject
     */
    private $customerAddressMock;

    /**
     * @var Model\Country|MockObject
     */
    private $countryMock;

    /**
     * @var Index|MockObject
     */
    private $controllerActionMock;

    /**
     * @var Http|MockObject
     */
    private $responseMock;

    /**
     * @var AddressCheck
     */
    private $addressCheckObserver;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        $this->observerMock = $this->createMock(Observer::class);
        $this->messageManagerMock = $this->createMock(ManagerInterface::class);
        $this->customerSessionMock = $this->createMock(Session::class);
        $this->urlMock = $this->createMock(UrlInterface::class);
        $this->countryFactoryMock = $this->createMock(Model\CountryFactory::class);

        $this->customerMock = $this->createMock(Customer::class);
        $this->customerAddressMock = $this->createMock(Address::class);
        $this->countryMock = $this->createMock(Model\Country::class);
        $this->controllerActionMock = $this->createMock(Index::class);
        $this->responseMock = $this->createMock(Http::class);

        $this->addressCheckObserver = (new ObjectManager($this))->getObject(
            AddressCheck::class,
            [
                'messageManager'    => $this->messageManagerMock,
                'customerSession'   => $this->customerSessionMock,
                'url'               => $this->urlMock,
                'countryFactory'    => $this->countryFactoryMock,
            ]
        );
    }

    /**
     * Test if customer address is restricted
     *
     * @param void
     *
     * @return void
     */
    public function testExecute(): void
    {
        $countryCode = 'US';
        $url = 'http://mage2.com/';
        $request = 'customer/address/edit/id/0';

        $this->customerSessionMock
            ->expects($this->once())
            ->method('getCustomer')
            ->willReturn($this->customerMock);

        $this->customerMock
            ->expects($this->once())
            ->method('getData')
            ->willReturn([AttributeNames::RESTRICTION_ENABLE => 1, AttributeNames::COUNTRIES_RESTRICTION => 'AF,US']);

        $this->customerMock
            ->expects($this->once())
            ->method('getAddresses')
            ->willReturn([$this->customerAddressMock]);

        $this->customerAddressMock
            ->expects($this->once())
            ->method('getData')
            ->with('country_id')
            ->willReturn($countryCode);

        $this->countryFactoryMock
            ->expects($this->once())
            ->method('create')
            ->willReturn($this->countryMock);

        $this->countryMock
            ->expects($this->once())
            ->method('loadByCode')
            ->with($countryCode)
            ->willReturnSelf();

        $this->countryMock
            ->expects($this->once())
            ->method('getName')
            ->willReturn('United States');

        $this->messageManagerMock
            ->expects($this->once())
            ->method('addErrorMessage')
            ->willReturnSelf();

        $this->urlMock
            ->expects($this->once())
            ->method('getUrl')
            ->with($request)
            ->willReturn($url . $request);

        $this->observerMock
            ->expects($this->once())
            ->method('getData')
            ->willReturn($this->controllerActionMock);

        $this->controllerActionMock
            ->expects($this->once())
            ->method('getResponse')
            ->willReturn($this->responseMock);

        $this->responseMock
            ->expects($this->once())
            ->method('setRedirect')
            ->with($url . $request)
            ->willReturnSelf();

        $this->addressCheckObserver->execute($this->observerMock);
    }
}
