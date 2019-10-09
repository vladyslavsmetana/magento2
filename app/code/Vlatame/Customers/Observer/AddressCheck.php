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
     * Frontend Observer instance
     *
     * @var Observer
     */
    private $observer;

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
        $this->observer = $observer;
        $customerCountries = [];
        $customer = $this->customerSession->getCustomer();
        $customerData = $customer->getData();
        $addresses = $customer->getAddresses();

        if (
            !empty($customerData)
            && isset($customerData[AttributeNames::RESTRICTION_ENABLE])
            && $customerData[AttributeNames::RESTRICTION_ENABLE]
        ) {
            if (!isset($customerData[AttributeNames::COUNTRIES_RESTRICTION])) {
                $this->errorRedirect(
                    __('Delivery is not allowed for customer with such email %1', $customerData['email']),
                    'home'
                );

                return $this;
            }

            if (!empty($addresses)) {
                foreach ($addresses as $key => $address) {
                    $customerCountries[$key] = $address->getData('country_id');
                }

                $allowedCountries = $customerData[AttributeNames::COUNTRIES_RESTRICTION];
                $allowedCountries = explode(',', $allowedCountries);
                foreach ($customerCountries as $key => $country) {
                    if (!in_array($country, $allowedCountries)) {
                        $countryModel = $this->countryFactory->create()->loadByCode($country);
                        $this->errorRedirect(
                            __('%1 is not allowed for delivery, please change the country', $countryModel->getName()),
                            sprintf('customer/address/edit/id/%1u', $key)
                        );
                        break;
                    }
                }
            }
        }

        return $this;
    }

    /**
     * Add error message and set redirection from params
     *
     * @param string $message
     * @param string $request
     *
     * @return void
     */
    private function errorRedirect(string $message, string $request): void
    {
        $this->messageManager->addErrorMessage($message);
        $redirectionUrl = $this->url->getUrl($request);
        $this->observer->getData('controller_action')->getResponse()->setRedirect($redirectionUrl);
    }
}
