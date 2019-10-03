<?php
namespace Vlatame\Customers\Observer;

use Magento\Customer\Model\Session;
use Magento\Directory\Model\CountryFactory;
use Magento\Framework\Event\Observer;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\UrlInterface;
use Vlatame\Customers\Block\AttributeNames;

/**
 * Proceed to Checkout Observer class
 *
 * @package Vlatame\Customers\Observer
 */
class AddressCheck implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * Message manager instance
     *
     * @var ManagerInterface
     */
    private $messageManager;

    /**
     * Frontend customer session instance
     *
     * @var Session
     */
    private $customerSession;

    /**
     * Magento Url instance
     *
     * @var UrlInterface
     */
    private $url;

    /**
     * Country model instance
     *
     * @var CountryFactory
     */
    private $countryFactory;

    /**
     * @param ManagerInterface $messageManager
     * @param Session $customerSession
     * @param UrlInterface $url
     * @param CountryFactory $countryFactory
     */
    public function __construct(
        ManagerInterface $messageManager,
        Session $customerSession,
        UrlInterface $url,
        CountryFactory $countryFactory
    ) {
        $this->messageManager = $messageManager;
        $this->customerSession = $customerSession;
        $this->url = $url;
        $this->countryFactory = $countryFactory;
    }

    /**
     * Check if customer address is restricted
     *
     * @param Observer $observer
     *
     * @return $this
     */
    public function execute(Observer $observer)
    {
        $customerCountries = [];
        $customer = $this->customerSession->getCustomer();
        $customerData = $customer->getData();
        $addresses = $customer->getAddresses();

        if (
            !empty($customerData)
            && !empty($addresses)
            && isset($customerData[AttributeNames::RESTRICTION_ENABLE])
            && $customerData[AttributeNames::RESTRICTION_ENABLE]
        ) {
            foreach ($addresses as $key => $address) {
                $customerCountries[$key] = $address->getData('country_id');
            }

            $restrictCountries = $customerData[AttributeNames::COUNTRIES_RESTRICTION];
            $restrictCountries = explode(',', $restrictCountries);
            foreach ($customerCountries as $key => $country) {
                if (in_array($country, $restrictCountries)) {
                    $countryModel = $this->countryFactory->create()->loadByCode($country);
                    $this->messageManager->addErrorMessage(
                        __('%1 is not allowed for delivery, please change the country', $countryModel->getName())
                    );

                    $redirectionUrl = $this->url->getUrl(sprintf('customer/address/edit/id/%1u', $key));
                    $observer->getData('controller_action')->getResponse()->setRedirect($redirectionUrl);
                    break;
                }
            }
        }

        return $this;
    }
}
